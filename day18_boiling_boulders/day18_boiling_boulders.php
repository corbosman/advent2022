<?php namespace day18_boiling_boulders;
use Ds\Set;
use Lib\solver;
use Tightenco\Collect\Support\Collection;

class day18_boiling_boulders extends solver
{
    public function solve() : array
    {
        $this->start_timer();

        [$cubes, $lookup] = $this->parse_input($this->input);
        $this->solution('18a', $this->part1($cubes, $lookup));

        return $this->solutions;
    }

    public function part1(Collection $cubes, Collection $lookup) : int
    {
        return $cubes->reduce(function($adjacent, $cube) use ($cubes, $lookup) {
            foreach([[1,0,0],[-1,0,0],[0,1,0],[0,-1,0],[0,0,1],[0,0,-1]] as [$dx, $dy, $dz]) {
                $nx = $cube->x+$dx; $ny = $cube->y+$dy; $nz=$cube->z+$dz;
                if (!isset($lookup["{$nx}_{$ny}_{$nz}"])) $adjacent++;
            }
            return $adjacent;
        }, 0);
    }

    public function parse_input(Collection $input) : array
    {
        $cubes = $input->map(function($c) {
            [$x,$y,$z] = explode(',', $c);
            return new Point($x, $y, $z);
        });
        $lookup = $cubes->mapWithKeys(fn($cube)=>["{$cube->x}_{$cube->y}_{$cube->z}" => 1]);
        return [$cubes, $lookup];
    }
 }
