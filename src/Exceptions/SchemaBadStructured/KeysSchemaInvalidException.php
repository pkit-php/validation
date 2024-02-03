<?php

namespace Pkit\Validator\Exceptions\SchemaBadStructured;

use Pkit\Validator\Exceptions\SchemaBadStructuredException;

class KeysSchemaInvalidException extends SchemaBadStructuredException
{
    public function __construct(mixed $schema, array $path)
    {
        parent::__construct(
            "array can contain only values or keys and values or especial keys",
            $schema,
            $path
        );
    }
}