<?php namespace Advent;
use Lib\solver;

class aoc05_supply_stacks extends solver
{
    public function solve() : array
    {
        $this->start_timer();

        /* parse the input in an array of stacks and an array of moves */
        [$stacks_a, $stacks_b, $moves] = $this->parse_input($this->input);

        /* move all the crates */
        foreach($moves as $move) {
            $stacks_a = $this->move_crates($stacks_a, $move[0], $move[1], $move[2]);
            $stacks_b = $this->move_crates($stacks_b, $move[0], $move[1], $move[2], false);
        }

        $this->solution('5a', $this->top_crates($stacks_a));
        $this->solution('5b', $this->top_crates($stacks_b));

        return $this->solutions;
    }

    public function move_crates(array $stacks, int $num, int $from, int $to, bool $reverse = true) : array
    {
        $crates = array_splice($stacks[$from-1], -$num, $num);
        $stacks[$to-1] = array_merge($stacks[$to-1], $reverse ? array_reverse($crates) : $crates);
        return $stacks;
    }

    public function top_crates(array $stacks) : string
    {
        return array_reduce($stacks, fn($c, $i) => $c . end($i), '');
    }

    public function parse_input($input) : array
    {
        $stacks = [];
        $num_stack = (int)ceil(strlen($input[0])/4);

        for($i=0; $i<$num_stack; $i++) $stacks[$i] = [];

        foreach($input as $num => $line) {
            if (is_numeric($line[1])) break;

            for($s=0; $s<$num_stack; $s++) {
                $i = ($s*4)+1;
                if(ctype_alpha($line[$i])) {
                    array_unshift($stacks[$s], $line[$i]);
                }
            }
        }

        $moves = $input->slice($num+2)->map(function($move) {
            preg_match('/^move (\d+) from (\d+) to (\d+)$/', $move, $m);
            return [(int)$m[1], (int)$m[2], (int)$m[3]];
        });

        return [$stacks, $stacks, $moves];
    }
}
