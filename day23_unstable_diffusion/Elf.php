<?php namespace day23_unstable_diffusion;

class Elf
{
    public ?array $proposal = null;

    public function __construct() {}

    public function next() : void
    {
        $this->proposal = null;
    }
}
