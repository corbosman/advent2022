<?php namespace day09_rope_bridge;

class Knot
{
    public array $path = [];

    public function __construct(public int $x = 0, public int $y = 0) {
        $this->path[] = [$x,$y];
    }

    public function move($dx, $dy) : void
    {
        $this->x += $dx;
        $this->y += $dy;
        $this->path["{$this->x}_{$this->y}"] = 1;
    }

    public function path() : array
    {
        return $this->path;
    }
}
