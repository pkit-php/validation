<?php

namespace Pkit\Validator\Exceptions\Validation;

use Pkit\Validator\Exceptions\ValidationException;

class MultiInvalidationException extends ValidationException
{
    public function __construct(mixed $schema, array $path, mixed $value, public readonly array $errors)
    {
        $textSchema = self::format($schema);
        parent::__construct(
            "invalid in multiSchema $textSchema",
            $schema,
            $path,
            $value
        );
    }
}