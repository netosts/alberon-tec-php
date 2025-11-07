<?php

namespace App\Services;

use App\Events\ContactsCsvImported;
use App\Models\Contact;
use App\Models\CsvImport;
use App\Repositories\Contracts\IContactRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ContactService implements Contracts\IContactService
{
  public function __construct(protected IContactRepository $contactRepository) {}

  public function getPaginated(Request $request)
  {
    $perPage = $request->input('per_page', 10);
    $page = $request->input('page', 1);

    return Cache::remember("contacts:page:{$page}:per_page:{$perPage}", 600, function () use ($perPage) {
      return Contact::orderBy('id', 'desc')
        ->paginate($perPage);
    });
  }

  public function importContactsFromCsv(Request $request)
  {
    if (!$request->hasFile('file')) {
      throw new \Exception('CSV file is required.');
    }

    $file = $request->file('file');

    // Store the file temporarily
    $filePath = $file->store('csv-imports', 'local');
    $fullPath = Storage::disk('local')->path($filePath);

    $csvValidationRules = [
      'name' => ['required', 'string', 'max:255'],
      'email' => ['required', 'email:rfc,dns', 'max:255'],
      'phone' => ['nullable', 'string', 'max:50'],
      'birthdate' => ['nullable', 'date', 'before:today'],
    ];
    $csvImport = CsvImport::create([
      'file_path' => $fullPath,
      'status' => 'processing',
    ]);

    ContactsCsvImported::dispatch(
      $fullPath,
      $csvValidationRules,
      $csvImport
    );

    return [
      'message' => 'CSV file is being processed in the background.',
      'status' => 'processing',
      'import_id' => $csvImport->id
    ];
  }
}
