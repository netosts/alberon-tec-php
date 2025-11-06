<?php

namespace App\Jobs;

use App\Events\CsvImportProgressUpdated;
use App\Models\CsvImport;
use App\Repositories\Contracts\IContactRepository;
use App\Utils\CsvHelper;
use App\Utils\ValidationHelper;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessContactsCsvChunk implements ShouldQueue
{
    use Queueable;

    public $timeout = 300; // 5 minutes per chunk
    public $tries = 3; // Retry failed chunks up to 3 times

    /**
     * Create a new job instance.
     */
    public function __construct(
        public array $rows,
        public array $columnMap,
        public array $validationRules,
        public int $chunkNumber,
        public int $importId
    ) {}

    /**
     * Execute the job.
     */
    public function handle(IContactRepository $contactRepository): void
    {
        $stats = [
            'chunk' => $this->chunkNumber,
            'processed' => 0,
            'imported' => 0,
            'duplicates' => 0,
            'errors' => 0,
        ];

        foreach ($this->rows as $row) {
            $stats['processed']++;

            try {
                // Extract data from row
                $data = CsvHelper::extractData($row, $this->columnMap);

                // Validate data
                $validation = ValidationHelper::validate($data, $this->validationRules);

                if (!$validation['valid']) {
                    $stats['errors']++;
                    Log::warning("CSV validation failed for row in chunk {$this->chunkNumber}", [
                        'data' => $data,
                        'errors' => $validation['errors']
                    ]);
                    continue;
                }

                // Check for duplicate
                if ($contactRepository->isEmailDuplicate($data['email'])) {
                    $stats['duplicates']++;
                    continue;
                }

                // Create contact
                $contactRepository->create($data);
                $stats['imported']++;
            } catch (\Exception $e) {
                $stats['errors']++;
                Log::error("Error processing row in chunk {$this->chunkNumber}: {$e->getMessage()}", [
                    'row' => $row,
                    'exception' => $e
                ]);
            }
        }

        // Update import stats in a transaction
        DB::transaction(function () use ($stats) {
            $csvImport = CsvImport::lockForUpdate()->findOrFail($this->importId);

            $csvImport->processed_chunks += 1;
            $csvImport->total_rows += $stats['processed'];
            $csvImport->imported += $stats['imported'];
            $csvImport->duplicates += $stats['duplicates'];
            $csvImport->errors += $stats['errors'];

            // Check if this was the last chunk
            if ($csvImport->processed_chunks >= $csvImport->total_chunks) {
                $csvImport->markAsCompleted();
            }

            $csvImport->save();

            // Broadcast progress update via WebSocket
            broadcast(new CsvImportProgressUpdated($csvImport))->toOthers();

            Log::info("Broadcasting progress update", [
                'chunk' => $this->chunkNumber,
                'import_id' => $csvImport->id,
                'progress' => $csvImport->progress,
                'status' => $csvImport->status
            ]);
        });

        Log::info("Chunk {$this->chunkNumber} processed", $stats);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        DB::transaction(function () {
            $csvImport = CsvImport::find($this->importId);
            if ($csvImport) {
                $csvImport->markAsFailed();
                broadcast(new CsvImportProgressUpdated($csvImport))->toOthers();
            }
        });

        Log::error("Chunk {$this->chunkNumber} failed after {$this->tries} attempts", [
            'import_id' => $this->importId,
            'exception' => $exception->getMessage()
        ]);
    }
}
