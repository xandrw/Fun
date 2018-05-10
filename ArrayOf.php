
<?php

abstract class ArrayOf extends ArrayObject
{
    protected $type;
    
    function __construct($input = [], $flags = ArrayObject::ARRAY_AS_PROPS, $iterator_class = 'ArrayIterator')
    {
        if (!$this->type || !is_string($this->type))
            throw new Exception('Extending classes must specify a type.');
        
        parent::__construct([], $flags, $iterator_class);
        
        foreach ($input as $key => $value) $this->offsetSet($key, $value);
    }
    
    final protected function getTypes()
    {
        return ['boolean', 'integer', 'float', 'double', 'string', 'array', 'object', 'resource'];
    }
    
    function getType()
    {
        return $this->type;
    }
    
    function offsetSet($index, $value)
    {
        if (in_array(gettype($value), $this->getTypes()) && gettype($value) === $this->type) {
            parent::offsetSet($index, $value);
            return;
        }
        
        if ($value instanceof $this->type) {
            parent::offsetSet($index, $value);
            return;
        }
        
        $type = is_object($value) ? get_class($value) : gettype($value);
        throw new UnexpectedValueException(
            sprintf('Value must be of type %s, %s given.', $this->type, $type)
        );
    }
}

class ArrayOfBool extends ArrayOf { protected $type = 'boolean'; }
class ArrayOfBoolean extends ArrayOf { protected $type = 'boolean'; }
class ArrayOfInt extends ArrayOf { protected $type = 'integer'; }
class ArrayOfInteger extends ArrayOf { protected $type = 'integer'; }
class ArrayOfFloat extends ArrayOf { protected $type = 'double'; }
class ArrayOfDouble extends ArrayOf { protected $type = 'double'; }
class ArrayOfString extends ArrayOf { protected $type = 'string'; }
class ArrayOfArray extends ArrayOf { protected $type = 'array'; }
class ArrayOfObject extends ArrayOf { protected $type = 'object'; }
class ArrayOfResource extends ArrayOf { protected $type = 'resource'; }

$bools = new ArrayOfBool([true, false]);
var_dump($bools);
$ints = new ArrayOfInt([1, 2, 3]);
var_dump($ints);
$floats = new ArrayOfFloat([1.1, 2.2, 3.3]);
var_dump($floats);
$strings = new ArrayOfString(['Alex', 'Steluta', 'Andrei']);
var_dump($strings);
$arrays = new ArrayOfArray([[1], [2], [3]]);
var_dump($arrays);
$objects = new ArrayOfObject([new stdClass, new stdClass, new stdClass]);
var_dump($objects);
$resources = new ArrayOfResource([STDIN, STDOUT]);
var_dump($resources);

class Person
{
    protected $name;
    function __construct($name) { $this->name = $name; }
    function getName() { return $this->name; }
}

class ArrayOfPerson extends ArrayOf { protected $type = 'Person'; }

// $persons = new ArrayOfPerson([1, 2.1, 'string']); // Uncomment for Exception

$persons = new ArrayOfPerson([new Person('Alex'), new Person('Steluta'), 'andrei' => new Person('Andrei')]);

function printNames(ArrayOfPerson $persons)
{
    foreach ($persons as $person)
        echo 'FOREACH: ' .  $person->getName() . PHP_EOL;
    
    echo 'FIRST: ' . reset($persons)->getName() . PHP_EOL;
    echo 'COUNT: ' . count($persons) . PHP_EOL;
    echo 'ARRAY NUMERIC KEY: ' . $persons[0]->getName() . PHP_EOL;
    echo 'ARRAY STRING KEY: ' . $persons['andrei']->getName() . PHP_EOL;
    echo 'OBJECT PROPERTY: ' . $persons->andrei->getName() . PHP_EOL;
    
    print_r($persons);
}

printNames($persons);

$std = new stdClass;
$std->alex = new Person('Alex');
$std->steluta = new Person('Steluta');
$people = new ArrayOfPerson($std);

print_r($people);
