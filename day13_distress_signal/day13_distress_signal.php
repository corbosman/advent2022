<?php namespace day13_distress_signal;
use Lib\solver;
use Tightenco\Collect\Support\Collection;

class day13_distress_signal extends solver
{
    public function solve() : array
    {
        $this->start_timer();

        $signal = $this->parse($this->input);
        $sum = $this->solve_a($signal);

        $this->solution('13a', $sum);

        return $this->solutions;
    }

    public function solve_a(array $signal) : int
    {
        $sum = 0;
        foreach($signal as $index => [$left, $right]) {
            if ($this->compare($left, $right) === -1) $sum += $index+1;
        }
        return $sum;
    }

    /* -1 = left < right, 0 = left == right, 1 = left > right */
    public function compare($left, $right) : int
    {
        /* both are integers, return their spaceship operator */
        if (is_int($left) && is_int($right)) return $left <=> $right;

        /* one of them might not be an array, make them both arrays */
        if (is_int($left)) $left = [$left];
        if (is_int($right)) $right = [$right];

        /* go through each array and compare them */
        for($i=0; $i<min(count($left), count($right)); $i++) {
            $c = $this->compare($left[$i], $right[$i]);
            if ($c !== 0) return $c;
        }

        /* we are done with the smallest array, now check their sizes */
        return count($left) <=> count($right);
    }

    /* parse the input, just use json_decode even though it's slow */
    public function parse(Collection $input) : array
    {
        return $input
            ->filter(fn($i)=>$i!=='')
            ->map(fn($i)=>json_decode($i, false, 512, JSON_THROW_ON_ERROR))
            ->chunk(2)
            ->map(fn($i)=>$i->values())
            ->toArray();
    }
 }
