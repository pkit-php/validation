<?php
use Pkit\Validator\Exceptions\SchemaBadStructured\EmptySchemaException;
use Pkit\Validator\Exceptions\SchemaBadStructured\KeysSchemaInvalidException;
use Pkit\Validator\Exceptions\SchemaBadStructured\UnsupportedKeyException;
use Pkit\Validator\Exceptions\SchemaBadStructured\InvalidTypeException;
use Pkit\Validator\Exceptions\Validation\CountKeysInvalidException;
use Pkit\Validator\Exceptions\Validation\InvalidValueOrTypeException;
use Pkit\Validator\Exceptions\Validation\MultiInvalidationException;
use Pkit\Validator\Exceptions\Validation\NonArrayValueException;
use Pkit\Validator\Exceptions\Validation\NotHaveKeyException;
use Pkit\Validator\Validator;

/* Validation effective */

test('Validator by type successfully', function ($schema, $test) {
    expect((new Validator($schema, true))
        ->validate($test))->toBeTrue();
})->with([
        ["array", []],
        ["bool", true],
        [false, false],
        ["countable", [1, 2, 3]],
        [["double", "float", "real"], 1.0],
        [1.1, 1.1],
        ["finite", 999_999_999_999],
        ["infinite", log(0)],
        ["int|integer|long", 1],
        [1, 1],
        ["numeric", "1234"],
        ["null", null],
        ["nan", acos(8)],
        [null, null],
        ["object", new stdClass],
        ["string", "string"],
        [":equal", "equal"],
    ]);

test('Validator by keys successfully', function ($key, $type, $test) {
    expect((new Validator([
            $key => $type
            ], true))->validate([$key => $test]))->toBeTrue();
})->with([

        ["array", "array", []],
        ["bool", "bool", true],
        ["bool", false, false],
        ["countable", "countable", [1, 2, 3]],
        ["double", ["double", "float", "real"], 1.0],
        ["double", 1.1, 1.1],
        ["finite", "finite", 999_999_999_999],
        ["infinite", "infinite", log(0)],
        ["int", "int|integer|long", 1],
        ["int", 1, 1],
        [
            "iterable",
            "iterable", (function () {
                yield 1; })()
        ],
        ["nan", "nan", acos(8)],
        ["null", "null", null],
        ["null", null, null],
        ["numeric", "numeric", "123"],
        ["object", "object", new stdClass],
        ["string", "string", ""],
        ["string", ":", ""],
    ]);

test('Validator with especial keys', function ($schema, $test) {
    expect((new Validator($schema, true))
        ->validate($test))->toBeTrue();
})->with([
        [["@array" => "bool"], [true, false]],
        [["@array" => ["bool", "numeric"]], [true, false, 0, "1"]],
        [["@array" => ["string" => ":"]], [["string" => ""], ["string" => ""]]],
    ]);

/* Validation with validation error */

test('Validator with false count', function ($test) {
    expect(fn() => (new Validator([
            "a" => "b",
            ], true))->validate($test))->toThrow(CountKeysInvalidException::class, "a => b");
})->with([
        [
            []
        ],
        [
            [
                "a" => "b",
                "c" => "d"
            ]
        ]
    ]);

test('Validator with invalid type or value', function ($schema, $test) {
    expect(fn() => (
                new Validator($schema, true)
            )->validate($test))->toThrow(InvalidValueOrTypeException::class, "invalid in schema");
})->with([
        ["string", 123],
        [["bool" => false], ["bool" => true]]
    ]);

test('Validator with multi invalidations', function ($schema, $test) {
    expect(fn() => (
                new Validator($schema, true)
            )->validate($test))->toThrow(MultiInvalidationException::class, "invalid in multiSchema");
})->with([
        [["string", "bool"], 123],
        [["key" => [false, "string", ["string" => ""]]], ["key" => true]],
    ]);

test('Validator on value is not array', function ($schema, $test) {
    expect(fn() => (
                new Validator($schema, true)
            )->validate($test))->toThrow(NonArrayValueException::class, "not is a array");
})->with([
        [["string" => ""], 123],
        [["bool" => false], ""],
        [["string" => ["" => ""]], ["string" => ""]]
    ]);

test('Validator on value not have key', function ($schema, $test) {
    expect(fn() => (
                new Validator($schema, true)
            )->validate($test))->toThrow(NotHaveKeyException::class, "does not have key");
})->with([
        [["a" => true], ["b" => false]],
    ]);

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