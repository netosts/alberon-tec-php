<?php

namespace App\Services;

use App\Models\Contact;
use App\Repositories\Contracts\IContactRepository;
use Illuminate\Http\Request;

class ContactService implements Contracts\IContactService
{
  public function __construct(protected IContactRepository $contactRepository) {}

  public function getPaginated(Request $request)
  {
    $perPage = $request->input('per_page', 10);

    return Contact::orderBy('created_at', 'desc')
      ->paginate($perPage);
  }

  public function importContactsFromCsv(Request $request)
  {
    if (!$request->hasFile('file')) {
      throw new \Exception('CSV file is required.');
    }

    $file = $request->file('file');

    // Store the file temporarily
    $filePath = $file->store('csv-imports', 'local');
    $fullPath = \Illuminate\Support\Facades\Storage::disk('local')->path($filePath);

    $csvValidationRules = [
      'name' => ['required', 'string', 'max:255'],
      'email' => ['required', 'email', 'max:255'],
      'phone' => ['nullable', 'string', 'max:50'],
      'birthdate' => ['nullable', 'date'],
    ];

    // Create import record
    $csvImport = \App\Models\CsvImport::create([
      'file_path' => $fullPath,
      'status' => 'processing',
    ]);

    // Dispatch event to process CSV in background
    \App\Events\ContactsCsvImported::dispatch(
      $fullPath,
      $csvValidationRules,
      $csvImport->id
    );

    return [
      'message' => 'CSV file is being processed in the background.',
      'status' => 'processing',
      'import_id' => $csvImport->id
    ];
  }
}
