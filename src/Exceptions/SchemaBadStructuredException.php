<?php

namespace Pkit\Validator\Exceptions;

class SchemaBadStructuredException extends ValidatorException
{
    public function __construct(public readonly string $reason, mixed $schema, array $path)
    {
        $textSchema = self::format($schema);
        parent::__construct(
            "$textSchema bad structured ( $reason )",
            $schema,
            $path
        );
    }
}