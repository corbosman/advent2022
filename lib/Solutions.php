<?php namespace Lib;

class Solutions
{
    protected float $time;
    public array $solutions;

    public function start_timer() : void
    {
        $this->time = microtime(true);
    }

    public function add($puzzle, $value, $title) : void
    {
        $time = microtime(true);
        $this->solutions[] = [$puzzle, $value, $title, $time - $this->time];
        $this->time = $time;
    }

    public function all() : array
    {
        return $this->solutions;
    }
}
