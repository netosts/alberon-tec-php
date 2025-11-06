<?php

use App\Models\CsvImport;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('csv-import.{importId}', function (CsvImport $csvImport, $importId) {
    return (int) $csvImport->id === (int) $importId;
});
