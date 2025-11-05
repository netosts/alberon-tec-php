<?php

namespace App\Services;

use App\Models\Contact;
use App\Repositories\Contracts\IContactRepository;
use App\Utils\CsvHelper;
use App\Utils\ValidationHelper;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class ContactService implements Contracts\IContactService
{
  public function __construct(protected IContactRepository $contactRepository) {}

  public function getContactsPaginated(Request $request)
  {
    return Contact::paginate(15);
  }

  public function importContactsFromCsv(Request $request)
  {
    if (!$request->hasFile('file')) {
      throw new \Exception('CSV file is required.');
    }

    $file = $request->file('file');

    $csvValidationRules = [
      'name' => ['required', 'string', 'max:255'],
      'email' => ['required', 'email', 'max:255'],
      'phone' => ['nullable', 'string', 'max:50'],
      'birthdate' => ['nullable', 'date'],
    ];

    $stats = $this->_processContactCsvFile($file, $csvValidationRules);

    return $stats;
  }

  private function _processContactCsvFile(UploadedFile $file, $validationRules = []): array
  {
    $expectedColumns = array_keys($validationRules);

    $csvData = CsvHelper::parse($file->getRealPath());

    $columnMap = CsvHelper::mapColumns($csvData['header'], $expectedColumns);

    $stats = [
      'total_rows' => 0,
      'imported' => 0,
      'duplicates' => 0,
      'errors' => 0,
    ];

    foreach ($csvData['rows'] as $row) {
      $stats['total_rows']++;

      // Extract data from row
      $data = CsvHelper::extractData($row, $columnMap);

      // Validate data
      $validation = ValidationHelper::validate($data, $validationRules);

      if (!$validation['valid']) {
        $stats['errors']++;
        continue;
      }

      // Check for duplicate
      if ($this->contactRepository->isEmailDuplicate($data['email'])) {
        $stats['duplicates']++;
        continue;
      }

      // Create contact
      $this->contactRepository->create($data);
      $stats['imported']++;
    }

    return $stats;
  }
}
