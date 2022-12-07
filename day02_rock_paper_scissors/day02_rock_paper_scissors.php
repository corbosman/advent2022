<?php namespace day02_rock_paper_scissors;
use Lib\solver;

class day02_rock_paper_scissors extends solver
{
    public function solve() : array
    {
        $this->start_timer();

        $score_a = $this->input->map(fn($game) => match($game) {
            'A X' => 4, 'A Y' => 8, 'A Z' => 3,
            'B X' => 1, 'B Y' => 5, 'B Z' => 9,
            'C X' => 7, 'C Y' => 2, 'C Z' => 6
        });

        $this->solution('2a', $score_a->sum());

        $score_b = $this->input->map(fn($game) => match($game) {
            'A X' => 3, 'A Y' => 4, 'A Z' => 8,
            'B X' => 1, 'B Y' => 5, 'B Z' => 9,
            'C X' => 2, 'C Y' => 6, 'C Z' => 7
        });

        $this->solution('2b', $score_b->sum());

        return $this->solutions;
    }
}
