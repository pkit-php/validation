<?php

namespace Pkit\Validator\Exceptions\Validation;

use Pkit\Validator\Exceptions\ValidationException;

class NotHaveKeyException extends ValidationException
{
    public function __construct(mixed $schema, array $path, mixed $value, public readonly string $invalidKey)
    {
        $textSchema = self::format($schema);
        parent::__construct(
            "does not have key $invalidKey in schema $textSchema",
            $schema,
            $path,
            $value
        );
    }
}