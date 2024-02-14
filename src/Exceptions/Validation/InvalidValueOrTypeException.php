<?php

namespace Pkit\Validator\Exceptions\Validation;

use Pkit\Validator\Exceptions\ValidationException;

class InvalidValueOrTypeException extends ValidationException
{
    public function __construct(mixed $schema, array $path, mixed $value)
    {
        $textSchema = self::format($schema);
        parent::__construct(
            "invalid in schema $textSchema",
            $schema,
            $path,
            $value
        );
    }
}