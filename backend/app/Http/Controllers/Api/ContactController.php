<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadCsvRequest;
use App\Services\Contracts\IContactService;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function __construct(
        protected IContactService $contactService
    ) {}

    public function index(Request $request)
    {
        $contacts = $this->contactService->getPaginated($request);
        return response()->json([
            'error' => false,
            'message' => 'Contacts retrieved successfully.',
            'data' => $contacts,
        ]);
    }

    public function uploadCsv(UploadCsvRequest $request)
    {
        $response = $this->contactService->importContactsFromCsv($request);

        return response()->json([
            'error' => false,
            'message' => 'Contacts imported successfully.',
            'data' => $response,
        ]);
    }
}
