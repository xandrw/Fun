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

    public function __call($functionName, $arguments)
    {
        if (function_exists($functionName)) {
            $r = new ReflectionFunction($functionName);

            if (empty($r->getParameters()))
                throw new BadMethodCallException('No arguments exist');

            $firstParameter = $r->getParameters()[0];

            if ($firstParameter->isPassedByReference()) {
                $functionName($this->value, ...$arguments);
                return $this;
            }

            $this->value = $functionName($this->value, ...$arguments);
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

$pipe = new Pipe("<script>alert('here comes the <boo>')</script>");
$value = $pipe->htmlentities()->strtoupper();

echo $value . PHP_EOL;
