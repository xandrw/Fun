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

            $parameter = $r->getParameters()[0];

            if ($parameter->isPassedByReference()) {
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

// EXAMPLE
$pipe = new Pipe("<script>alert('here comes the <boo>')</script>");
$value = $pipe->htmlentities()->md5();

echo $value;
