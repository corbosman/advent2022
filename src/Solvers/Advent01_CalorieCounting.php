<?php namespace Advent\Solvers;
use Advent\Solutions;
use Advent\Solver;
use Tightenco\Collect\Support\Collection;

class Advent01_CalorieCounting extends Solver
{
    public function solve(Collection $input, Solutions $solutions) : void
    {
        $solutions->start_timer();

        $elves = $input->chunkWhile(fn ($value) => $value !== "")
                        ->map(fn ($elf) => $elf->filter(fn($cal) => $cal !== ""))
                        ->map(fn ($elf) => $elf->sum());

        $solutions->add('1a', $this->title(), $elves->max());
        $solutions->add('1b', $this->title(), $elves->sort()->take(-3)->sum());
    }
}