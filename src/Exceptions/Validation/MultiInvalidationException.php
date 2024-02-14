<?php

namespace Pkit\Validator\Exceptions\Validation;

use Pkit\Validator\Exceptions\ValidationException;

class MultiInvalidationException extends ValidationException
{
    public function __construct(mixed $schema, array $path, mixed $value, public readonly array $errors)
    {
        $textSchema = self::format($schema);
        $errorsText = implode(" | ", $errors);
        parent::__construct(
            "invalid in multiSchema $textSchema : $errorsText",
            $schema,
            $path,
            $value
        );
    }
}