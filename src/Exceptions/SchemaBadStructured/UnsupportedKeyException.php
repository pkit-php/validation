<?php

namespace Pkit\Validator\Exceptions\SchemaBadStructured;

use Pkit\Validator\Exceptions\SchemaBadStructuredException;

class UnsupportedKeyException extends SchemaBadStructuredException
{
    public function __construct(string $especialKey, mixed $schema, array $path)
    {
        parent::__construct(
            "$especialKey is an unsupported especial key",
            $schema,
            $path
        );
    }
}