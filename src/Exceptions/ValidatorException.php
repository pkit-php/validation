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
        return str_replace(["array ","\n", "  ", ",)"],["","", " ", " )"], var_export($schema, true));
        
    }
}