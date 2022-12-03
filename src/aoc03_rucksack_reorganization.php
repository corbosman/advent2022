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
        $doubles = collect();

        foreach($this->input as $rucksack) {
            [$first, $second] = str_split($rucksack, strlen($rucksack)/2);
            $doubles = $doubles->merge(array_unique(array_intersect(str_split($first),str_split($second))));
        }

        $this->solution('3a', $doubles->map(fn($item) => $this->priority($item))->sum());
    }

    public function solve_b() : void
    {
        $badges = collect();

        foreach($this->input->map(fn($r) => str_split($r))->chunk(3)->map(fn($r) => $r->values()) as $group) {
            $badges = $badges->merge(array_unique(array_intersect($group[0], $group[1], $group[2])));
        }

        $this->solution('3b', $badges->map(fn($item) => $this->priority($item))->sum());
    }

    public function priority($item) : int
    {
        return ctype_lower($item) ? ord($item)-96 : ord($item)-64+26;
    }
}
