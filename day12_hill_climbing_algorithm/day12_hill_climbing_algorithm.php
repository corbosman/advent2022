<?php namespace day12_hill_climbing_algorithm;
use Lib\solver;

class day12_hill_climbing_algorithm extends solver
{
    public function solve() : array
    {
        $this->start_timer();

        $map        = $this->input->map(fn($i)=>str_split($i))->toArray();
        $heights    = $this->heights($map);
        $map_width  = count($map[0]);
        $map_height = count($map);
        $start      = $this->find_start($map, $map_width, $map_height);

        $distance = $this->dijkstra($map, $heights, $map_width, $map_height, $start);

        $this->solution('12a', $distance);

        return $this->solutions;
    }

    public function dijkstra(array $map, array $heights, int $map_width, int $map_height, array $start) : int
    {
        $q = new Heap;
        $q->insert($start, 0);

        $visited   = array_fill(0, $map_height, array_fill(0, $map_width, 0));
        $distances = array_fill(0, $map_height, []);
        $distances[$start[0]][$start[1]] = 0;

        while($q->count() > 0) {
            [$x, $y] = $q->extract();

            /* we found the best location */
            if ($map[$x][$y] === 'E') {
                return $distances[$x][$y];
            }

            $neighbors = $this->neighbors($heights, $x, $y, $map_width, $map_height, $visited);

            foreach($neighbors as [$nx, $ny]) {
                $distance = $distances[$x][$y] + 1;
                $distance_n = $distances[$nx][$ny] ?? 99999999999999999;
                if ($distance < $distance_n) {
                    $distances[$nx][$ny] = $distance;
                    $q->insert([$nx, $ny], $distance);
                }
            }

            $visited[$x][$y] = 1;
        }
        /* this should not happen */
        return -1;
    }

    /**
     * Get all the neighbors or our current position that we haven't seen yet and are able to visit.
     */
    protected function neighbors(array $map, int $x, int $y, int $map_width, int $map_height, $visited) : array
    {
        $neighbors = [];
        $height = $map[$x][$y];

        foreach ([[1,0], [0,1], [-1,0], [0,-1]] as [$dx, $dy]) {
            /* out of bounds */
            if ($x+$dx < 0 || $x+$dx >= $map_height || $y+$dy<0 || $y+$dy >= $map_width) continue;

            /* already visited this position */
            if ($visited[$x+$dx][$y+$dy] === 1) continue;

            /* the next position is too high! */
            if ($map[$x+$dx][$y+$dy] > $height + 1) continue;

            $neighbors[] = [$x+$dx, $y+$dy];
        }

        return $neighbors;
    }

    /**
     * Convert the map to a map of heights
     */
    public function heights(array $map) : array
    {
        return array_map(fn($i)=>array_map(fn($j) => match($j) { 'S' => ord('a')-97, 'E' => ord('z')-97, default => ord($j)-97}, $i), $map);
    }

    /**
     * Find the start of the map
     */
    private function find_start($map, $map_width, $map_height) : array
    {
        for($i=0; $i<$map_height; $i++) {
           for($j=0; $j<$map_width; $j++) {
               if ($map[$i][$j] === 'S') return [$i, $j];
           }
        }
        return [-1,-1];
    }
}
