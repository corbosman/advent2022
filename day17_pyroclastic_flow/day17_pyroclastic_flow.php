<?php namespace day17_pyroclastic_flow;
use Lib\solver;
use Tightenco\Collect\Support\Collection;
use Ds\Map;

class day17_pyroclastic_flow extends solver
{
    public function solve() : array
    {
        $this->start_timer();

        $chamber = new Chamber($this->parse_input($this->input));
        $chamber->drop_rocks(2022);
        $this->solution('17a', $chamber->rock_height+1);

        $chamber = new Chamber($this->parse_input($this->input));
        $this->solution('17b', $chamber->avalanche(1000000000000)+1);

        return $this->solutions;
    }

    public function parse_input(Collection $input) : array
    {
        return str_split(($input->first()));
    }
 }
