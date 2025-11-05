<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ContactController extends Controller
{
    public function index()
    {
        $contacts = Contact::orderBy('created_at', 'desc')->paginate(20);

        return response()->json($contacts);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');

        // Skip header row
        $header = fgetcsv($handle);

        // Find column indices
        $columnMap = $this->mapColumns($header);

        $stats = [
            'total_rows' => 0,
            'imported' => 0,
            'duplicates' => 0,
            'errors' => 0,
        ];

        while (($row = fgetcsv($handle)) !== false) {
            $stats['total_rows']++;

            $data = $this->extractData($row, $columnMap);

            if (!$data) {
                $stats['errors']++;
                continue;
            }

            // Validate data
            $validator = Validator::make($data, [
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'nullable|string|max:50',
                'birthdate' => 'nullable|date',
            ]);

            if ($validator->fails()) {
                $stats['errors']++;
                continue;
            }

            // Check for duplicate
            if (Contact::where('email', $data['email'])->exists()) {
                $stats['duplicates']++;
                continue;
            }

            // Create contact
            Contact::create($data);
            $stats['imported']++;
        }

        fclose($handle);

        return response()->json([
            'success' => true,
            'stats' => $stats,
        ]);
    }

    private function mapColumns(array $header): array
    {
        $map = [];
        $normalizedHeader = array_map('strtolower', array_map('trim', $header));

        foreach ($normalizedHeader as $index => $column) {
            if (in_array($column, ['name', 'email', 'phone', 'birthdate'])) {
                $map[$column] = $index;
            }
        }

        return $map;
    }

    private function extractData(array $row, array $columnMap): ?array
    {
        if (!isset($columnMap['name']) || !isset($columnMap['email'])) {
            return null;
        }

        return [
            'name' => $row[$columnMap['name']] ?? null,
            'email' => $row[$columnMap['email']] ?? null,
            'phone' => $row[$columnMap['phone']] ?? null,
            'birthdate' => $row[$columnMap['birthdate']] ?? null,
        ];
    }
}
