<?php namespace day09_rope_bridge;
use Lib\solver;
use Tightenco\Collect\Support\Collection;

class day09_rope_bridge extends solver
{
    public function solve() : array
    {
        $this->start_timer();

        $rope = $this->swing($this->input, 2);
        $this->solution('9a', count($rope->tail()->path()));

        $rope = $this->swing($this->input, 10);
        $this->solution('9b', count($rope->tail()->path()));

        return $this->solutions;
    }

    public function swing($input, $knots) : Rope
    {
        $rope = new Rope(0, 0, $knots);

        foreach($input as $line) {
            [$d, $n] = explode(' ', $line);
            $n = (int)$n;

            /* move the rope n times into a direction */
            for ($i=0; $i<$n; $i++) {
                $rope->move($d);
            }
        }

        return $rope;
    }
 }
