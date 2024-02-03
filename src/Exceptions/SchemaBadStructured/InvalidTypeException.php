<?php

namespace Pkit\Validator\Exceptions\SchemaBadStructured;

use Pkit\Validator\Exceptions\SchemaBadStructuredException;

class InvalidTypeException extends SchemaBadStructuredException
{
    public function __construct(string $type, mixed $schema, array $path)
    {
        parent::__construct(
            "'$type' is an unsupported validation type",
            $schema,
            $path
        );
    }
}