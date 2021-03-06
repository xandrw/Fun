<?php

abstract class Type
{
    private $value;
    private $type = null;
    private $types = ['boolean', 'integer', 'double', 'string', 'array', 'object', 'resource', 'NULL'];

    final public function __construct($value)
    {
        $this->makeTypeFromClassName();
        $this->checkType();
        $this->checkValueType($value);
        $this->value = $value;
    }

    final private function makeTypeFromClassName()
    {
        $className = strtolower(static::class);
        $className = preg_replace('/(type)+$/', '', $className);

        $this->type = $className;
    }

    final private function checkType()
    {
        if (! in_array($this->type, $this->types) || $this->type === null)
            throw new Exception("Invalid type given `{$this->type}`.");
    }

    final private function checkValueType($value)
    {
        $type = gettype($value);
        if ($type !== $this->type)
            throw new Exception("Value must be `{$this->type}`, `{$type}` given.");
    }

    final public static function make($value)
    {
        return new static($value);
    }

    final public function __invoke()
    {
        return $this->value;
    }

    final public function __toString()
    {
        if (is_array($this->value) || is_object($this->value))
            return serialize($this->value);

        return (string) $this->value;
    }
}

class StringType extends Type
{
    public static function parseInt(IntegerType $int)
    {
        return (string) $int();
    }
}

class IntegerType extends Type {}

class DoubleType extends Type {}

class BooleanType extends Type {}

class ArrayType extends Type {}

$stringType  = new StringType('Alex');
$integerType = new IntegerType(1);
$doubleType  = new DoubleType(1.1);
$booleanType = new BooleanType(true);
$arrayType   = new ArrayType([1, 2, 3]);

$stringTypeMake = StringType::make('Andrei');

var_dump(
    $stringType(),
    $integerType(),
    $doubleType(),
    $booleanType(),
    $arrayType(),
    (string) $arrayType,
    $stringType::parseInt($integerType),
    $stringTypeMake()
);

/**
 * OUTPUT:
 * string(4) "Alex"
 * int(1)
 * float(1.1)
 * bool(true)
 * array(3) {
 *   [0]=>
 *   int(1)
 *   [1]=>
 *   int(2)
 *   [2]=>
 *   int(3)
 * }
 * string(30) "a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}"
 * string(1) "1"
 * string(6) "Andrei"
 */
