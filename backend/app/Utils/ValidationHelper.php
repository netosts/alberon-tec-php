<?php

namespace App\Utils;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ValidationHelper
{
  /**
   * Validate data and return validation result
   */
  public static function validate(array $data, array $rules): array
  {
    $validator = Validator::make($data, $rules);

    return [
      'valid' => !$validator->fails(),
      'errors' => $validator->errors()->all(),
    ];
  }

  /**
   * Validate or throw exception
   */
  public static function validateOrFail(array $data, array $rules): void
  {
    $validator = Validator::make($data, $rules);

    if ($validator->fails()) {
      throw new ValidationException($validator);
    }
  }
}
