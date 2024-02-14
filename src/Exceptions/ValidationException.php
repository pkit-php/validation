<?php

namespace Pkit\Validator\Exceptions;

class ValidationException extends ValidatorException
{
    public final static function formatPathValue(array $path, mixed $value)
    {
        $testFormat = self::format($value);
        $pathFormat = [...$path, $testFormat];
        return implode(" => ", $pathFormat);
    }
    public function __construct(public readonly string $description, mixed $schema, array $path, public readonly mixed $value)
    {
        $pathText = self::formatPathValue($path, $value);
        parent::__construct(
            "value $pathText $description",
            $schema,
            $path
        );
    }
}