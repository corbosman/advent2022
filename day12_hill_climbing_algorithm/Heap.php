<?php namespace day12_hill_climbing_algorithm;

class Heap extends \SplPriorityQueue
{
    public function compare($priority1, $priority2) : int
    {
        return parent::compare($priority2,$priority1);
    }
}
