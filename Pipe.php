<?php

class Pipe
{
    protected $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function get()
    {
        return $this->value;
    }

    public function __call($name, $arguments)
    {
        if (method_exists($this, $name)) {
            $this->$name(...$arguments);
            return $this;
        }

        if (function_exists($name)) {
            $r = new ReflectionFunction($name);

            if (empty($r->getParameters()))
                throw new BadMethodCallException('No arguments exist');

            $firstParameter = $r->getParameters()[0];

            if ($firstParameter->isPassedByReference()) {
                $name($this->value, ...$arguments);
                return $this;
            }

            $this->value = $name($this->value, ...$arguments);
            return $this;
        }

        throw new BadMethodCallException;
    }

    public function __toString()
    {
        if (is_array($this->value) || is_object($this->value)) {
            return serialize($this->value);
        }
        return (string) $this->value;
    }
}

// EXAMPLE of a Pipe class operating on a value in a functional way
$pipe = new Pipe("<script>alert('here comes the <boo>')</script>");
$value = $pipe->htmlentities()->md5();

echo $value;
