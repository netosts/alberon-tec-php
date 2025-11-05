<?php

namespace App\Utils;

class CsvHelper
{
  /**
   * Parse CSV file and return rows with headers mapped
   */
  public static function parse(string $filePath): array
  {
    $handle = fopen($filePath, 'r');

    if ($handle === false) {
      throw new \Exception('Could not open the CSV file.');
    }

    $header = fgetcsv($handle);
    if ($header === false) {
      fclose($handle);
      throw new \Exception('Invalid CSV file: no header found.');
    }

    $rows = [];
    while (($row = fgetcsv($handle)) !== false) {
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
    $map = [];
    $normalizedHeader = array_map('strtolower', array_map('trim', $header));

    foreach ($normalizedHeader as $index => $column) {
      if (in_array($column, $expectedColumns)) {
        $map[$column] = $index;
      }
    }

    return $map;
  }

  /**
   * Extract data from a CSV row using column map
   */
  public static function extractData(array $row, array $columnMap, array $requiredFields = []): ?array
  {
    // Check if required fields exist in column map
    foreach ($requiredFields as $field) {
      if (!isset($columnMap[$field])) {
        return null;
      }
    }

    $data = [];
    foreach ($columnMap as $field => $index) {
      $data[$field] = $row[$index] ?? null;
    }

    return $data;
  }
}
