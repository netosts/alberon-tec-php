<?php

use App\Http\Controllers\Api\ContactController;
use Illuminate\Support\Facades\Route;

Route::prefix('contacts')->group(function () {
  Route::get('/', [ContactController::class, 'index'])->name('contacts.index');
  Route::put('/upload-csv', [ContactController::class, 'uploadCsv'])->name('contacts.uploadCsv');
});
