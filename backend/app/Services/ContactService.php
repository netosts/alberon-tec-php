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

  private const EXPECTED_COLUMNS = ['name', 'email', 'phone', 'birthdate'];
  private const REQUIRED_FIELDS = ['name', 'email'];

  private const VALIDATION_RULES = [
    'name' => 'required|string|max:255',
    'email' => 'required|email|max:255',
    'phone' => 'nullable|string|max:50',
    'birthdate' => 'nullable|date',
  ];

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
    $stats = $this->processCsvFile($file);

    return $stats;
  }

  private function processCsvFile(UploadedFile $file): array
  {
    // Parse CSV file
    $csvData = CsvHelper::parse($file->getRealPath());

    // Map columns
    $columnMap = CsvHelper::mapColumns($csvData['header'], self::EXPECTED_COLUMNS);

    // Initialize stats
    $stats = [
      'total_rows' => 0,
      'imported' => 0,
      'duplicates' => 0,
      'errors' => 0,
    ];

    // Process each row
    foreach ($csvData['rows'] as $row) {
      $stats['total_rows']++;

      // Extract data from row
      $data = CsvHelper::extractData($row, $columnMap, self::REQUIRED_FIELDS);

      if (!$data) {
        $stats['errors']++;
        continue;
      }

      // Validate data
      $validation = ValidationHelper::validate($data, self::VALIDATION_RULES);

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
