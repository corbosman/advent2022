<?php namespace Lib;

class Solver
{
    public function puzzle() : string
    {
        return substr((new \ReflectionClass($this))->getShortName(), 6, 2);
    }

    public function title() : string
    {
        return trim(implode(' ', preg_split('/(?=[A-Z])/', substr((new \ReflectionClass($this))->getShortName(), 9))));
    }
}
