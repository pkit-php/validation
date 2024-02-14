<?php

namespace Pkit\Validator\Exceptions\Validation;

use Pkit\Validator\Exceptions\ValidationException;

class CountKeysInvalidException extends ValidationException
{
    public function __construct(mixed $schema, array $path, mixed $value)
    {
        $textSchema = self::format($schema);
        parent::__construct(
            "does not have the same amount of keys as schema $textSchema",
            $schema,
            $path,
            $value
        );
    }
}