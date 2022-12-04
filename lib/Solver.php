<?php namespace Lib;
use ReflectionClass;
use Tightenco\Collect\Support\Collection;

class solver
{
    protected float $timer;
    protected array $solutions = [];
    protected Collection $input;
    protected ReflectionClass $reflection;

    public function __construct()
    {
        $this->reflection = new ReflectionClass($this);
        $this->input = collect(input("inputs/{$this->puzzle()}.txt"));
    }

    public function start_timer() : void
    {
        $this->timer = microtime(true);
    }

    /* add a solution */
    public function solution($puzzle, $value) : void
    {
        $time = microtime(true);
        $this->solutions[] = [$puzzle, $this->title(), $value, $time - $this->timer];
        $this->timer = $time;
    }

    /* get the puzzle number from the class name */
    public function puzzle() : string
    {
        return substr($this->reflection->getShortName(), 3, 2);
    }

    /* get the title from the class name */
    public function title() : string
    {
        return ucwords(str_replace('_', ' ', substr($this->reflection->getShortName(), 6)));
    }
}
