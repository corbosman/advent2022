<?php

namespace day15_beacon_exclusion_zone;

class Point
{
    public function __construct(
        public int $x,
        public int $y,
    ) {}

    /* calculate the manhattan distance to another point */
    public function distance(Point $p) : int
    {
        return abs($this->x - $p->x) + abs($this->y - $p->y);
    }
}
