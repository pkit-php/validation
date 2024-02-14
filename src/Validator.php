<?php

namespace Pkit\Validator;


use Pkit\Validator\Exceptions\SchemaBadStructured\EmptySchemaException;
use Pkit\Validator\Exceptions\SchemaBadStructured\KeysSchemaInvalidException;
use Pkit\Validator\Exceptions\SchemaBadStructured\UnsupportedKeyException;
use Pkit\Validator\Exceptions\SchemaBadStructured\InvalidTypeException;
use Pkit\Validator\Exceptions\Validation\CountKeysInvalidException;
use Pkit\Validator\Exceptions\Validation\InvalidValueOrTypeException;
use Pkit\Validator\Exceptions\Validation\MultiInvalidationException;
use Pkit\Validator\Exceptions\Validation\NonArrayValueException;
use Pkit\Validator\Exceptions\Validation\NotHaveKeyException;
use Pkit\Validator\Exceptions\ValidationException;

final class Validator
{
    private mixed $result = [];
    public function __construct(private mixed $schema)
    {
        $this->schema = $schema;
    }

    public function validate(mixed $value)
    {
        if (is_array($this->schema))
            $this->handleValidate($value, [], $this->schema);
        else
            $this->validateValueOrType($value, [], $this->schema);
        return $this->result;
    }

    private function set_value_in_result(array $levels, mixed $value)
    {
        if (empty($levels))
            $this->result = $value;
        else {
            $this->result = $this->set_value_in_level($levels, is_null($this->result) ? [] : $this->result, $value);
        }
    }

    private function set_value_in_level(array $levels, array $result, mixed $value): array
    {
        $first_level = array_shift($levels);

        if (empty($levels)) {
            $result[$first_level] = $value;
        } else {
            $result[$first_level] = $this->set_value_in_level($levels, is_null($result[$first_level]) ? [] : $result[$first_level], $value);
        }
        return $result;
    }

    private function handleValidate(mixed $test, array $level, array $schema)
    {
        if (empty($schema)) {
            throw new EmptySchemaException(
                $schema,
                $level,
            );
        }

        $is_int = null;
        $is_especial_key = null;
        array_map(function ($key) use (&$is_int, &$is_especial_key, $schema, $level) {
            if (is_string($key)) {
                if (is_null($is_especial_key))
                    $is_especial_key = substr($key, 0, 1) == "@";
                else if ($is_especial_key !== (substr($key, 0, 1) == "@") || $is_int) {
                    throw new KeysSchemaInvalidException(
                        $schema,
                        $level,
                    );
                }
            } else if (is_null($is_int)) {
                $is_int = is_int($key);
            } else if ($is_int !== is_int($key) || $is_int && $is_especial_key) {
                throw new KeysSchemaInvalidException(
                    $schema,
                    $level,
                );
            }
        }, array_keys($schema));

        if ($is_especial_key)
            return $this->validateEspecialKeys($test, $level, $schema);
        if ($is_int)
            return $this->validateOnlyValues($test, $level, $schema);
        return $this->validateKeysAndValues($test, $level, $schema);
    }

    private function validateEspecialKeys(mixed $test, array $level, array $schema)
    {
        $supportedSchemas = ["@array"];
        $usedArraySchema = false;
        foreach ($schema as $especialSchema => $subSchema) {
            if (!in_array($especialSchema, $supportedSchemas))
                throw new UnsupportedKeyException(
                    $especialSchema,
                    $schema,
                    $level,
                );

            if ($especialSchema == "@array") {

                if ($usedArraySchema)
                    throw new CountKeysInvalidException(
                        $schema,
                        $level,
                        $test
                    );

                if (!is_array($test))
                    throw new CountKeysInvalidException(
                        $schema,
                        $level,
                        $test
                    );

                foreach ($test as $key => $value) {
                    if (!is_integer($key)) {
                        return false;
                    }

                    if (is_array($subSchema)) {
                        if (!$this->handleValidate($value, [...$level, $key], $subSchema))
                            return false;
                    } else {
                        if (!$this->validateValueOrType($value, [...$level, $key], $subSchema))
                            return false;
                    }
                }
                $usedArraySchema = true;
            }
        }
        $this->set_value_in_result($level, $test);
        return true;
    }

    private function validateOnlyValues(mixed $test, array $level, array $schema)
    {
        $errors = [];
        foreach ($schema as $subSchema) {
            try {
                if (is_array($subSchema)) {
                    $result = $this->handleValidate($test, $level, $subSchema);

                    if ($result) {
                        $this->set_value_in_result($level, $test);
                        return true;
                    }
                    continue;
                }


                if ($this->validateValueOrType($test, $level, $subSchema)) {
                    $this->set_value_in_result($level, $test);
                    return true;
                }
            } catch (ValidationException $th) {
                $errors[] = $th->getMessage();
                $result = false;
            }
        }

        throw new MultiInvalidationException(
            $schema,
            $level,
            $test,
            $errors
        );
    }

    private function validateKeysAndValues(mixed $test, array $level, array $schema)
    {
        if (!is_array($test))
            throw new NonArrayValueException(
                $schema,
                $level,
                $test
            );

        if (count($test) !== count($schema))
            throw new CountKeysInvalidException(
                $schema,
                $level,
                $test
            );

        foreach ($schema as $keySubSchema => $subSchema) {
            if (!key_exists($keySubSchema, $test)) {
                throw new NotHaveKeyException(
                    $subSchema,
                    $level,
                    $test,
                    $keySubSchema
                );
            }

            if (is_array($subSchema)) {
                if ($this->handleValidate($test[$keySubSchema], [...$level, $keySubSchema], $subSchema))
                    continue;
                return false;
            }


            if (!$this->validateValueOrType($test[$keySubSchema], [...$level, $keySubSchema], $subSchema))
                return false;
        }
        $this->set_value_in_result($level, $test);
        return true;
    }

    public function validateValueOrType(mixed $test, array $level, mixed $subSchema)
    {
        if (!is_string($subSchema)) {
            if ($subSchema === $test) {
                $this->set_value_in_result($level, $test);
                return true;
            }
        } else {
            if (substr($subSchema, 0, 1) == ":") {
                if (substr($subSchema, 1) == $test) {
                    $this->set_value_in_result($level, $test);
                    return true;
                }
            } else {
                if ($this->validType($subSchema, $level, $test)) {
                    $this->set_value_in_result($level, $test);
                    return true;
                }
            }
        }

        throw new InvalidValueOrTypeException(
            $subSchema,
            $level,
            $test
        );

    }

    public function validType(string $schema, array $level, mixed $value)
    {
        $types = explode("|", $schema);
        $resultValidation = false;
        foreach ($types as $type) {
            try {
                $resultValidation = call_user_func("is_" . $type, $value);
            } catch (\Exception) {

            } catch (\Error $e) {
                throw new InvalidTypeException(
                    $type,
                    $schema,
                    $level,
                );
            }
            if ($resultValidation)
                break;
        }
        return $resultValidation;
    }

}