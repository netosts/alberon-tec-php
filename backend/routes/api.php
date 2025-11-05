<?php

use App\Http\Controllers\Api\ContactController;
use Illuminate\Support\Facades\Route;

Route::prefix('contacts')->group(function () {
  Route::get('/', [ContactController::class, 'index']);
  Route::post('/upload', [ContactController::class, 'upload']);
});
