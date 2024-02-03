<?php

namespace Pkit\Validator\Exceptions;


class ValidatorException extends \Exception
{

    public function __construct(string $message, public readonly mixed $schema, public readonly array $path)
    {
        parent::__construct($message);
    }

    public static final function format(mixed $schema)
    {
        if (!is_array($schema))
            return "$schema";
        $textSchemaBase = array_map(function ($key, $value) {
            if (is_array($value))
                $value = self::format($value);
            if (is_numeric($key))
                return "$value";
            if (is_object($value))
                return "{$key} => (object)";
            return "{$key} => $value";
        }, array_keys($schema), $schema);
        return "[ " . implode(", ", $textSchemaBase) . " ]";
    }
}