<?php namespace Advent;
use Lib\Solver;

class Advent01_CalorieCounting extends Solver
{
    public function solve() : array
    {
        $this->start_timer();

        $elves = $this->input->chunkWhile(fn ($value) => $value !== "")
                        ->map(fn ($elf) => $elf->filter(fn($cal) => $cal !== ""))
                        ->map(fn ($elf) => $elf->sum());

        $this->solution('1a', $elves->max());
        $this->solution('1b', $elves->sort()->take(-3)->sum());

        return $this->solutions;
    }
}
