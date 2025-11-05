<?php

namespace App\Services\Contracts;

use Illuminate\Http\Request;

interface IContactService
{
  public function getContactsPaginated(Request $request);
  public function importContactsFromCsv(Request $request);
}
