<?php namespace day18_boiling_boulders;
use Ds\Deque;
use Ds\Map;
use Ds\Queue;
use Lib\solver;
use Tightenco\Collect\Support\Collection;

class day18_boiling_boulders extends solver
{
    public function solve() : array
    {
        $this->start_timer();

        [$cubes, $lookup] = $this->parse_input($this->input);
        $this->solution('18a', $this->part1($cubes, $lookup));
        $this->solution('18b', $this->part2($cubes, $lookup));
        return $this->solutions;
    }

    public function part1(Collection $cubes, Map $lookup) : int
    {
        return $cubes->reduce(function($surface_area, $cube) use ($lookup) {
            foreach([[1,0,0],[-1,0,0],[0,1,0],[0,-1,0],[0,0,1],[0,0,-1]] as [$dx, $dy, $dz]) {
                if (!$lookup->hasKey([$cube->x+$dx, $cube->y+$dy, $cube->z+$dz])) $surface_area++;
            }
            return $surface_area;
        }, 0);
    }

    public function part2(Collection $cubes, Map $lookup) : int
    {
        /* create a box around the shape */
        [$min, $max] = $this->christmas_present($cubes);

        /* flood the exterior, this exposes all outside air */
        $exterior = $this->flood($lookup, $min, $max);

        /* go through the cubes and only count those sides that are in the exterior air */
        return $cubes->reduce(function($surface_area, $cube) use ($lookup, $exterior) {
            foreach([[1,0,0],[-1,0,0],[0,1,0],[0,-1,0],[0,0,1],[0,0,-1]] as [$dx, $dy, $dz]) {
                $neighbor = [$cube->x+$dx, $cube->y+$dy, $cube->z+$dz];
                if ($lookup->hasKey($neighbor)) continue;           // has a cube next to it, ignore
                if (!$exterior->hasKey($neighbor)) continue;        // not in the outside air, ignore

                $surface_area++;
            }
            return $surface_area;
        }, 0);
    }

    public function flood(Map $cubes, Point $min, Point $max) : Map
    {
        $exterior = new Map;
        $queue = new Deque;

        $exterior->put([$min->x, $min->y, $min->z],1);
        $queue->push([$min->x, $min->y, $min->z]);

        while($queue->count() > 0) {
            [$x, $y, $z] = $queue->shift();

            foreach([[1,0,0],[-1,0,0],[0,1,0],[0,-1,0],[0,0,1],[0,0,-1]] as [$dx, $dy, $dz]) {
                $nx = $x+$dx; $ny = $y+$dy; $nz = $z+$dz;

                // dont think outside the box
                if ($nx < $min->x || $nx > $max->x) continue;
                if ($ny < $min->y || $ny > $max->y) continue;
                if ($nz < $min->z || $nz > $max->z) continue;

                $next_point = [$nx, $ny, $nz];

                // already counted
                if ($exterior->hasKey($next_point)) continue;

                // this point is a cube, skip.
                if ($cubes->hasKey($next_point)) continue;

                $exterior->put($next_point,1);
                $queue->push($next_point);
            }
        }
        return $exterior;
    }

    public function christmas_present(Collection $cubes) : array
    {
        $min_x = $min_y = $min_z = 1000;
        $max_x = $max_y = $max_z = -1000;
        foreach($cubes as $cube) {
            $min_x = min($cube->x, $min_x);
            $min_y = min($cube->y, $min_y);
            $min_z = min($cube->z, $min_z);
            $max_x = max($cube->x, $max_x);
            $max_y = max($cube->y, $max_y);
            $max_z = max($cube->z, $max_z);
        }
        return [new Point($min_x-1,$min_y-1,$min_z-1), new Point($max_x+1, $max_y+1, $max_z+1)];
    }

    public function parse_input(Collection $input) : array
    {
        $cubes = $input->map(function($c) {
            [$x,$y,$z] = explode(',', $c);
            return new Point($x, $y, $z);
        });
        $lookup = new Map;
        $cubes->each(fn($cube)=>$lookup->put([$cube->x, $cube->y, $cube->z], 1));
        return [$cubes, $lookup];
    }
 }
