<?php

namespace App\Repositories\Contracts;

interface IContactRepository
{
  public function create(array $data): array;
  public function isEmailDuplicate(string $email): bool;
  public function getExistingEmails(array $emails);
}
