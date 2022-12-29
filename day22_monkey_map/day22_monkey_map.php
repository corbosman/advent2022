<?php namespace day22_monkey_map;
use Lib\solver;
use Tightenco\Collect\Support\Collection;

class day22_monkey_map extends solver
{
    public function solve() : array
    {
        $this->start_timer();

        [$map, $path] = $this->parse_input($this->input);
        $this->solution('22a', $this->part1($map, $path));
        $this->solution('22b', $this->part2($map, $path));

        return $this->solutions;
    }

    public function part1(array $map, array $path) : int
    {
        [$x, $y] = $this->find_start($map);
        [$x, $y, $step_index] = (new Map($map, $path, $x, $y))->walk();
        return (1000*($y+1)) + 4 * ($x+1) + $step_index;
    }

    public function part2(array $map, array $path) : int
    {
        [$x, $y] = $this->find_start($map);
        [$x, $y, $step_index] = (new Cube($map, $path, $x, $y))->walk();
        return (1000*($y+1)) + 4 * ($x+1) + $step_index;
    }

    public function find_start(array $map) : array
    {
        foreach($map[0] as $i => $m) if ($m !== ' ') return [$i, 0];
    }

    public function parse_input(Collection $input) : array
    {
        $path = $input->pop(); $input->pop();
        preg_match_all('!(\d+[L,R]?)!', $path, $matches);
        $path = collect($matches[1])->reduce(function($carry, $item) {
            preg_match('!^(\d+)([L,R])?$!', $item, $matches);
            array_shift($matches);
            return $carry->merge(collect($matches));
        },collect())->toArray();

        $input = $input->map(fn($i)=>str_split($i));
        $width = $input->map(fn($i)=>count($i))->max();
        $map = [];

        foreach($input as $y => $line) {
            for($x=0; $x<$width; $x++) {
                $map[$y][$x] = $input[$y][$x] ?? ' ';
            }
        }

        return [$map, $path];
    }
 }
