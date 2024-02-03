<?php

use Pkit\Validator\Exceptions\SchemaBadStructured\EmptySchemaException;
use Pkit\Validator\Exceptions\SchemaBadStructured\KeysSchemaInvalidException;
use Pkit\Validator\Exceptions\SchemaBadStructured\UnsupportedKeyException;
use Pkit\Validator\Exceptions\SchemaBadStructured\InvalidTypeException;
use Pkit\Validator\Validator;


/* Validation with schema bad structured error */

test('Validator bad structured on schema is empty', function ($schema, $test) {
    expect(fn() => (
                new Validator($schema, true)
            )->validate($test))->toThrow(EmptySchemaException::class, "cannot be empty");
})->with([
        [[], []],
        [["array" => []], ["array" => []]]
    ]);

test('Validator bad structured on schema keys is invalid', function ($schema, $test) {
    expect(fn() => (
                new Validator($schema, true)
            )->validate($test))->toThrow(KeysSchemaInvalidException::class, "only values or keys");
})->with([
        [["array" => "array", true], ["array" => [], true]],
        [["a" => ["b" => "c", "d"]], ["a" => ["b" => "c", "d"]]],
        [["a" => "b", "@array" => "boolean"], ["a" => [true, false]]],
    ]);

test('Validator bad structured on especial key is unsupported', function ($schema, $test) {
    expect(fn() => (
                new Validator($schema, true)
            )->validate($test))->toThrow(UnsupportedKeyException::class, "unsupported especial key");
})->with([
        [["@list" => "bool"], [true, false]],
    ]);

test('Validator bad structured on type is unsupported', function ($schema, $test) {
    expect(fn() => (
                new Validator($schema, true)
            )->validate($test))->toThrow(InvalidTypeException::class, "unsupported validation type");
})->with([
        ["list", []],
    ]);