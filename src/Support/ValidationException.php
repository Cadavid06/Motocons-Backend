<?php

namespace App\Support;

use RuntimeException;

final class ValidationException extends RuntimeException
{
    /**
     * @param  array<string, array<int, string>>  $errors
     */
    public function __construct(public readonly array $errors)
    {
        parent::__construct('Los datos proporcionados no son válidos.');
    }
}
