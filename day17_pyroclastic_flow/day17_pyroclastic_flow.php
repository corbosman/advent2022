<?php namespace day17_pyroclastic_flow;
use Lib\solver;
use Tightenco\Collect\Support\Collection;
use Ds\Map;

class day17_pyroclastic_flow extends solver
{
    public array $rocks = [[0,0,0,30],[0,8,28,8],[0,4,4,28],[16,16,16,16],[0,0,24,24]];
    public array $chamber = [];

    public function solve() : array
    {
        $this->start_timer();

        $chamber = new Chamber($this->parse_input($this->input));
        $chamber->drop_rocks(2022);

        $this->solution('17a', $chamber->height()+1);

        return $this->solutions;
    }

    public function parse_input(Collection $input)
    {
        return str_split(($input->first()));
    }
 }
