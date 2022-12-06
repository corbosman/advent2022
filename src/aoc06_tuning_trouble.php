<?php namespace Advent;
use Lib\solver;

/*
 * Started with the following collection to get the answer quick.
 * Below code is 10x faster.
 *
 * $first = collect(str_split($this->input[0]))
            ->sliding(4)
            ->map(fn($i)=>$i->unique())
            ->filter(fn($i)=>$i->count() === 4)
            ->keys()
            ->first();
 */

class aoc06_tuning_trouble extends solver
{
    public function solve() : array
    {
        $this->start_timer();

        $input = $this->input[0];
        $size  = strlen($input);

        $this->solution('6a', $this->window($input, $size, 4));
        $this->solution('6b', $this->window($input, $size, 14));

        return $this->solutions;
    }

    public function window(string $input, int $size, int $window) : int
    {
        for($i=0; $i<$size-$window; $i++) {
            $w = [];
            for($j=$window-1; $j>=0; $j--) {
                $letter = $input[$i+$j];
                if (isset($w[$letter])) {
                    $i+=$j;
                    break;
                }
                $w[$letter] = 1;
            }
            if (count($w) === $window) break;
        }
        return $i+$window;
    }
}
