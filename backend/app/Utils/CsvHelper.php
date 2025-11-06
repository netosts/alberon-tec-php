<?php

namespace App\Utils;

class CsvHelper
{
  /**
   * Open CSV file and return handle
   */
  private static function openFile(string $filePath)
  {
    $handle = fopen($filePath, 'r');

    if ($handle === false) {
      throw new \Exception('Could not open the CSV file.');
    }

    return $handle;
  }

  /**
   * Read and validate CSV header
   */
  private static function readHeader($handle, string $delimiter = ';'): array
  {
    $header = fgetcsv($handle, 0, $delimiter);

    if ($header === false) {
      throw new \Exception('Invalid CSV file: no header found.');
    }

    return $header;
  }

  /**
   * Parse CSV header only
   */
  public static function parseHeader(string $filePath, string $delimiter = ';'): array
  {
    $handle = self::openFile($filePath);
    $header = self::readHeader($handle, $delimiter);
    fclose($handle);

    return $header;
  }

  /**
   * Parse CSV file and return rows with headers mapped
   */
  public static function parse(string $filePath, string $delimiter = ';'): array
  {
    $handle = self::openFile($filePath);
    $header = self::readHeader($handle, $delimiter);

    $rows = [];
    while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
      $rows[] = $row;
    }

    fclose($handle);

    return [
      'header' => $header,
      'rows' => $rows,
    ];
  }

  /**
   * Map CSV header columns to expected field names
   */
  public static function mapColumns(array $header, array $expectedColumns): array
  {
    // Define column aliases for flexible matching
    $aliases = [
      'name' => ['name', 'full_name', 'fullname', 'full name', 'contact_name', 'contactname'],
      'email' => ['email', 'e-mail', 'email_address', 'emailaddress'],
      'phone' => ['phone', 'telephone', 'phone_number', 'phonenumber', 'tel', 'mobile'],
      'birthdate' => ['birthdate', 'birth_date', 'birthday', 'date_of_birth', 'dob'],
    ];

    $map = [];
    $normalizedHeader = array_map('strtolower', array_map('trim', $header));

    foreach ($normalizedHeader as $index => $column) {
      // Direct match
      if (in_array($column, $expectedColumns)) {
        $map[$column] = $index;
        continue;
      }

      // Check aliases
      foreach ($expectedColumns as $expectedColumn) {
        if (isset($aliases[$expectedColumn]) && in_array($column, $aliases[$expectedColumn])) {
          $map[$expectedColumn] = $index;
          break;
        }
      }
    }

    return $map;
  }

  /**
   * Extract data from a CSV row using column map
   */
  public static function extractData(array $row, array $columnMap): array
  {
    $data = [];
    foreach ($columnMap as $field => $index) {
      $data[$field] = $row[$index] ?? null;
    }

    return $data;
  }
}
