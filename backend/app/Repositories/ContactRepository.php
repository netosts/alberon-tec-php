<?php

namespace App\Repositories;

use App\Models\Contact;

class ContactRepository implements Contracts\IContactRepository
{
  protected Contact $model;

  public function __construct()
  {
    $this->model = new Contact();
  }

  public function create(array $data): array
  {
    $collectData = collect($data);

    $contact = $this->model->create([
      'name' => $collectData->get('name'),
      'email' => $collectData->get('email'),
      'phone' => $collectData->get('phone'),
      'birthdate' => $collectData->get('birthdate'),
    ]);

    return $contact->toArray();
  }

  public function isEmailDuplicate(string $email): bool
  {
    return $this->model->where('email', $email)->exists();
  }
}
