<?php

namespace App\Listeners;

use App\Events\ContactsCsvImported;
use App\Events\CsvImportProgressUpdated;
use App\Jobs\ProcessContactsCsvChunk;
use App\Models\CsvImport;
use App\Utils\CsvHelper;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class ProcessContactsCsv implements ShouldQueue
{
    use InteractsWithQueue;

    public $timeout = 120; // 2 minutes to chunk the file

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ContactsCsvImported $event): void
    {
        $chunkSize = 50;
        $delimiter = ';'; // CSV delimiter

        try {
            // Parse CSV header
            $header = CsvHelper::parseHeader($event->filePath, $delimiter);

            // Map columns
            $expectedColumns = array_keys($event->validationRules);
            $columnMap = CsvHelper::mapColumns($header, $expectedColumns);

            // Count total rows and chunks
            $totalRows = 0;
            $handle = fopen($event->filePath, 'r');

            if ($handle === false) {
                throw new \Exception('Could not open CSV file for chunking');
            }

            // Skip header
            fgetcsv($handle, 0, $delimiter);

            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                if (!empty(array_filter($row))) {
                    $totalRows++;
                }
            }
            fclose($handle);

            $totalChunks = (int) ceil($totalRows / $chunkSize);

            // Update import record with total chunks
            $event->csvImport->update(['total_chunks' => $totalChunks]);

            // Reopen file for chunking
            $handle = fopen($event->filePath, 'r');
            fgetcsv($handle, 0, $delimiter); // Skip header again

            $rows = [];
            $chunkNumber = 0;

            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                $rows[] = $row;

                // Dispatch chunk when we reach chunk size
                if (count($rows) >= $chunkSize) {
                    $chunkNumber++;
                    ProcessContactsCsvChunk::dispatch(
                        $rows,
                        $columnMap,
                        $event->validationRules,
                        $chunkNumber,
                        $event->csvImport
                    );

                    $rows = [];
                }
            }

            // Dispatch remaining rows
            if (!empty($rows)) {
                $chunkNumber++;
                ProcessContactsCsvChunk::dispatch(
                    $rows,
                    $columnMap,
                    $event->validationRules,
                    $chunkNumber,
                    $event->csvImport
                );
            }

            fclose($handle);
        } catch (\Exception $e) {
            $event->csvImport->markAsFailed();
            broadcast(new CsvImportProgressUpdated($event->csvImport))->toOthers();

            Log::error('Failed to process CSV file', [
                'csv_import' => $event->csvImport,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(ContactsCsvImported $event, \Throwable $exception): void
    {
        $event->csvImport->markAsFailed();
        broadcast(new CsvImportProgressUpdated($event->csvImport))->toOthers();

        Log::error('Failed to process CSV file', [
            'csv_import' => $event->csvImport,
            'error' => $exception->getMessage()
        ]);
    }
}
