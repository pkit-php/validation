<?php

use Pkit\Validator\Validator;

/* Validation effective */

test('Validator by type successfully', function ($schema, $test) {
    expect((new Validator($schema, true))
        ->validate($test))->toBeTrue();
})->with("types");

test('Validator by keys successfully', function ($type, $test) {
    expect((new Validator([
        "key_$type" => $type
    ], true))->validate(["key_$type" => $test]))->toBeTrue();
})->with("types");

test('Validator with @array especial key', function ($type, $test) {
    expect((new Validator(["@array" => $type], true))
        ->validate([$test, $test]))->toBeTrue();
})->with("types");

dataset("types", [
    ["array", []],
    ["bool", true],
    [false, false],
    ["countable", [1, 2, 3]],
    [["double", "float", "real"], 1.0],
    [1.1, 1.1],
    ["finite", 999_999_999_999],
    ["infinite", log(0)],
    ["int|integer|long", 1],
    [
        "iterable",
        (function () {
            yield 1; })()
    ],
    [2, 2],
    ["numeric", "1234"],
    ["null", null],
    ["nan", acos(8)],
    [null, null],
    ["object", new stdClass],
    ["string", "string"],
    [":equal", "equal"],
]);