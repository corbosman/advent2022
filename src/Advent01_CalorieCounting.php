<?php namespace Advent;
use Lib\Solutions;
use Lib\Solver;

class Advent01_CalorieCounting extends Solver
{
    public function solve($input, Solutions $solutions) : void
    {
        $elves = $input->chunkWhile(fn ($value) => $value !== "")
                        ->map(fn ($elf) => $elf->filter(fn($cal) => $cal !== ""))
                        ->map(fn ($elf) => $elf->sum());

        $solutions->add('1a', $this->title(), $elves->max());
        $solutions->add('1b', $this->title(), $elves->sort()->take(-3)->sum());
    }
}
