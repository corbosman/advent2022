<?php namespace day24_blizzard_basin;

class Blizzard
{
    public function __construct(
        public int $x,
        public int $y,
        public BlizzardType $type
    ) {}
}
