<?php

namespace App\Jobs;

use App\Events\CsvImportProgressUpdated;
use App\Models\CsvImport;
use App\Repositories\Contracts\IContactRepository;
use App\Utils\CsvHelper;
use App\Utils\ValidationHelper;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
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
        public CsvImport $csvImport
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

        // Extract all emails from this chunk
        $emails = [];
        $validatedData = [];

        foreach ($this->rows as $row) {
            $stats['processed']++;

            try {
                $data = CsvHelper::extractData($row, $this->columnMap);
                $validation = ValidationHelper::validate($data, $this->validationRules);

                if (!$validation['valid']) {
                    $stats['errors']++;
                    // Only log first 10 errors per chunk to avoid log spam
                    if ($stats['errors'] <= 10) {
                        Log::warning("CSV validation failed for row in chunk {$this->chunkNumber}", [
                            'data' => $data,
                            'errors' => $validation['errors']
                        ]);
                    }
                    continue;
                }

                $emails[] = $data['email'];
                $validatedData[] = $data;
            } catch (\Exception $e) {
                $stats['errors']++;
                Log::error("Error processing row in chunk {$this->chunkNumber}: {$e->getMessage()}", [
                    'row' => $row,
                    'exception' => $e
                ]);
            }
        }

        // Single query to check all duplicates at once
        $existingEmails = $contactRepository->getExistingEmails($emails);

        // Now process the validated data
        foreach ($validatedData as $data) {
            try {
                // O(1) lookup instead of database query
                if (isset($existingEmails[$data['email']])) {
                    $stats['duplicates']++;
                    continue;
                }

                $contactRepository->create([...$data, 'csv_import_id' => $this->csvImport->id]);
                $stats['imported']++;
            } catch (\Exception $e) {
                $stats['errors']++;
                Log::error("Error creating contact in chunk {$this->chunkNumber}: {$e->getMessage()}", [
                    'data' => $data,
                    'exception' => $e
                ]);
            }
        }

        // Update stats in transaction
        $updatedImport = DB::transaction(function () use ($stats) {
            $this->csvImport->refresh();
            $this->csvImport->lockForUpdate();

            $this->csvImport->increment('processed_chunks');
            $this->csvImport->increment('total_rows', $stats['processed']);
            $this->csvImport->increment('imported', $stats['imported']);
            $this->csvImport->increment('duplicates', $stats['duplicates']);
            $this->csvImport->increment('errors', $stats['errors']);

            if ($this->csvImport->processed_chunks >= $this->csvImport->total_chunks) {
                $this->csvImport->markAsCompleted();
                Cache::flush();
            }

            $this->csvImport->save();

            return $this->csvImport->fresh(); // Return fresh model
        });

        // Broadcast OUTSIDE transaction (non-blocking)
        broadcast(new CsvImportProgressUpdated($updatedImport))->toOthers();


        if ($updatedImport->status === 'completed') {
            Log::info("CSV import completed and cache cleared", [
                'import_id' => $updatedImport->id
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $this->csvImport->markAsFailed();
        broadcast(new CsvImportProgressUpdated($this->csvImport))->toOthers();

        Log::error("Chunk {$this->chunkNumber} failed after {$this->tries} attempts", [
            'csv_import' => $this->csvImport,
            'exception' => $exception->getMessage()
        ]);
    }
}
