<?php namespace Lib;
use ReflectionClass;
use Tightenco\Collect\Support\Collection;

class Solver
{
    protected float $timer;
    protected array $solutions;
    protected Collection $input;
    protected ReflectionClass $reflection;

    public function __construct()
    {
        $this->reflection = new ReflectionClass($this);
        $this->input = collect(input("inputs/{$this->puzzle()}.txt"));
        $this->timer = microtime(true);
    }

    /* add a solution */
    public function solution($puzzle, $value) : void
    {
        $time = microtime(true);
        $this->solutions[] = [$puzzle, $value, $this->title(), $time - $this->timer];
        $this->timer = $time;
    }

    /* get the puzzle number from the class name */
    public function puzzle() : string
    {
        return substr($this->reflection->getShortName(), 6, 2);
    }

    /* get the title from the class name */
    public function title() : string
    {
        return trim(implode(' ', preg_split('/(?=[A-Z])/', substr($this->reflection->getShortName(), 9))));
    }
}
