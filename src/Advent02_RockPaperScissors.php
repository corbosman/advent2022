<?php namespace Advent;
use Lib\Solutions;
use Lib\Solver;
use Tightenco\Collect\Support\Collection;

class Advent02_RockPaperScissors extends Solver
{
    public function solve(Collection $input, Solutions $solutions) : void
    {
        $score_a = $input->map(fn($game) => match($game) {
            'A X' => 4, 'A Y' => 8, 'A Z' => 3,
            'B X' => 1, 'B Y' => 5, 'B Z' => 9,
            'C X' => 7, 'C Y' => 2, 'C Z' => 6
        });

        $score_b = $input->map(fn($game) => match($game) {
            'A X' => 3, 'A Y' => 4, 'A Z' => 8,
            'B X' => 1, 'B Y' => 5, 'B Z' => 9,
            'C X' => 2, 'C Y' => 6, 'C Z' => 7
        });

        $solutions->add('2a', $this->title(), $score_a->sum());
        $solutions->add('2b', $this->title(), $score_b->sum());
    }
}
