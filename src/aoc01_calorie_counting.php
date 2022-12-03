<?php namespace Advent;
use Lib\solver;

class aoc01_calorie_counting extends solver
{
    public function solve() : array
    {
        $this->start_timer();

        $elves = [];
        $total = 0;

        foreach($this->input as $calories) {
            if ($calories === "") {
                $elves[] = $total;
                $total = 0;
            } else {
                $total += (int)$calories;
            }
        }

        rsort($elves);

        $this->solution('1a', $elves[0]);
        $this->solution('1b', $elves[0] + $elves[1] + $elves[2]);

        return $this->solutions;
    }
}
