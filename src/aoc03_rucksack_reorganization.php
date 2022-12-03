<?php namespace Advent;
use Lib\solver;

class aoc03_rucksack_reorganization extends solver
{
    public function solve() : array
    {
        $this->start_timer();

        $this->solve_a();
        $this->solve_b();

        return $this->solutions;
    }

    public function solve_a() : void
    {
        $solution = $this->input
                    ->map(fn($r) => str_split($r, strlen($r)/2))
                    ->flatMap(fn($r) => array_unique(array_intersect(str_split($r[0]), str_split($r[1]))))
                    ->map(fn($item) => $this->priority($item))
                    ->sum();

        $this->solution('3a', $solution);
    }

    public function solve_b() : void
    {
        $solution = $this->input
                    ->map(fn($r) => str_split($r))
                    ->chunk(3)
                    ->map(fn($r) => $r->values())
                    ->flatMap(fn($group) => array_unique(array_intersect($group[0], $group[1], $group[2])))
                    ->map(fn($item) => $this->priority($item))
                    ->sum();

        $this->solution('3b', $solution);
    }

    public function priority($item) : int
    {
        return ctype_lower($item) ? ord($item)-96 : ord($item)-64+26;
    }
}
