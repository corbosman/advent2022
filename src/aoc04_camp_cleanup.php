<?php namespace Advent;
use Lib\solver;

class aoc04_camp_cleanup extends solver
{
    public function solve() : array
    {
        $this->start_timer();
        $full_overlap = 0;
        $part_overlap = 0;

        foreach($this->input as $job) {
            preg_match('/^(\d+)-(\d+),(\d+)-(\d+)$/', $job, $m);
            if (($m[1]<=$m[3] && $m[2]>=$m[4]) || ($m[3]<=$m[1] && $m[4]>=$m[2])) $full_overlap++;
            if (($m[1]<=$m[3] && $m[2]>=$m[3]) || ($m[3]<=$m[1] && $m[4]>=$m[1])) $part_overlap++;
        }

        $this->solution('4a', $full_overlap);
        $this->solution('4b', $part_overlap);

        return $this->solutions;
    }
}
