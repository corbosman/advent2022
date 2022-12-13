<?php namespace day13_distress_signal;
use Lib\solver;
use Tightenco\Collect\Support\Collection;

class day13_distress_signal extends solver
{
    public function solve() : array
    {
        $this->start_timer();

        $signal = $this->parse($this->input);

        $this->solution('13a', $this->solve_a($signal->chunk(2)->map(fn($i)=>$i->values())->toArray()));
        $this->solution('13b', $this->solve_b($signal->push([[2]])->push([[6]])->toArray()));

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

    public function solve_b(array $signal) : int
    {
       usort($signal, [$this, 'compare']);
       [$div1, $div2] = $this->find_dividers($signal, ['[[2]]', '[[6]]']);
       return $div1 * $div2;
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

    public function find_dividers(array $signal, array $dividers) : array
    {
        $indices = [];
        $foo = collect($signal)->map(fn($i)=>json_encode($i))->dd();
        foreach($signal as $index => $s) {
            $s = json_encode($s);
            if (in_array($s, $dividers, true)) $indices[] = $index+1;
            if (count($indices) === 2) break;
        }
        return $indices;
    }

    /* parse the input, just use json_decode even though it's slow */
    public function parse(Collection $input) : Collection
    {
        return $input
            ->filter(fn($i)=>$i!=='')
            ->map(fn($i)=>json_decode($i, false, 512, JSON_THROW_ON_ERROR));
    }
 }
