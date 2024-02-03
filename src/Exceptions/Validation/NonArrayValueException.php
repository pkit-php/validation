<?php

namespace Pkit\Validator\Exceptions\Validation;

use Pkit\Validator\Exceptions\ValidationException;

class NonArrayValueException extends ValidationException
{
    public function __construct(mixed $schema, array $path, mixed $value)
    {
        $textSchema = self::format($schema);
        parent::__construct(
            "not is a array in $textSchema",
            $schema,
            $path,
            $value
        );
    }
}