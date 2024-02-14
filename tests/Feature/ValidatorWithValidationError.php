<?php

use Pkit\Validator\Exceptions\Validation\CountKeysInvalidException;
use Pkit\Validator\Exceptions\Validation\InvalidValueOrTypeException;
use Pkit\Validator\Exceptions\Validation\MultiInvalidationException;
use Pkit\Validator\Exceptions\Validation\NonArrayValueException;
use Pkit\Validator\Exceptions\Validation\NotHaveKeyException;
use Pkit\Validator\Validator;

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