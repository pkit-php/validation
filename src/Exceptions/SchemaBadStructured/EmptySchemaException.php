<?php

namespace Pkit\Validator\Exceptions\SchemaBadStructured;

use Pkit\Validator\Exceptions\SchemaBadStructuredException;

class EmptySchemaException extends SchemaBadStructuredException
{
    public function __construct(mixed $schema, array $path)
    {
        parent::__construct(
            "array cannot be empty",
            $schema,
            $path
        );
    }
}