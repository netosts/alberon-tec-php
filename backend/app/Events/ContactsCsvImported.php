<?php

namespace App\Events;

use App\Models\CsvImport;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ContactsCsvImported
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public string $filePath,
        public array $validationRules,
        public CsvImport $csvImport
    ) {}
}
