<?php namespace day24_blizzard_basin;
use Ds\Map;
use Ds\PriorityQueue;
use Ds\Set;
use Ds\Vector;
use Lib\solver;
use Tightenco\Collect\Support\Collection;
use day24_blizzard_basin\BlizzardType as BLZ;

class day24_blizzard_basin extends solver
{
    public int $width;
    public int $height;
    public int $lcm;

    public function solve() : array
    {
        $this->start_timer();

        /* parse the map */
        $map = $this->parse_input($this->input);

        /* get a weather forecast */
        $forecast = $this->forecast($map);

        /* time to walk to the end */
        $minutes = $this->walk($map, $forecast, [1,0], [$this->width-2,$this->height-1]);
        $this->solution('24a', $minutes);

        return $this->solutions;
    }

    public function walk(Vector $map, array $forecast, array $start, array $end) : int
    {
        $q = new PriorityQueue;
        $q->push([$start, 0], 0);

        /* visited is a set of position and time */
        $visited = new Set;

        while($q->count() > 0) {
            [$position, $minutes] = $q->pop();

            /* we have reached our destination */
            if ($position === $end) return $minutes-1;

            /* get the weather forecast for this minute */
            $weather = $forecast[$minutes % $this->lcm];

            /* get all the possible positions we can visit while avoiding blizzards */
            $destinations = $this->avoid_blizzards($position, $weather);

            foreach($destinations as $d) {
                if ($visited->contains([$d[0], $d[1], $minutes+1])) continue;

                $priority = $this->priority($minutes, $position, $end);
                $q->push([$d, $minutes+1], $priority);
            }
            $visited->add([$position[0], $position[1], $minutes]);
        }
        die("should not happen!");
    }

    public function avoid_blizzards(array $position, Set $weather) : array
    {
        [$x, $y] = $position;
        $destinations = [];
        foreach([[0,-1],[-1,0],[1,0],[0,1]] as [$dx, $dy]) {
            $nx = $x+$dx;
            $ny = $y+$dy;

            /* out of bounds */
            if ($nx < 0 || $nx >= $this->width-1 || $ny < 1 || $ny > $this->height - 1) continue;

            /* position is taken by a blizzard or a wall */
            if ($weather->contains([$nx, $ny])) continue;

            $destinations[] = [$nx, $ny];
        }

        /* you can also just wait if a blizzard didn't move into our position */
        if (!$weather->contains([$x, $y])) $destinations[] = $position;

        return $destinations;
    }

    public function forecast(Vector $map) : array
    {
        $weathermaps = [];

        /* blizzards repeat horizontally and vertically, take the lcm */
        $this->lcm = (int)gmp_lcm($this->width-2, $this->height-2);

        /* initial weather map */
        $weather = new Set;
        foreach($map as $pos) {
            $weather->add([$pos->x,$pos->y]);
        }
        $weathermaps[0] = $weather;

        /* run the forecast */
        for($i=1; $i<$this->lcm+1; $i++) {
            $weather = new Set;
            foreach($map as $blizzard) {
                [$x, $y] = [$blizzard->x, $blizzard->y];
                [$nx, $ny] = match($blizzard->type) {
                    BLZ::BLIZZARD_NORTH => [$x, $y === 1 ? $this->height-2 : $y-1],
                    BLZ::BLIZZARD_WEST  => [$x === 1 ? $this->width-2 : $x-1, $y],
                    BLZ::BLIZZARD_EAST  => [$x === $this->width-2 ? 1 : $x+1, $y],
                    BLZ::BLIZZARD_SOUTH => [$x, $y === $this->height-2 ? 1 : $y+1],
                    BLZ::WALL => [$x, $y]
                };
                [$blizzard->x, $blizzard->y] = [$nx, $ny];
                $weather->add([$nx, $ny]);
            }
            // $this->print($map);
            $weathermaps[$i] = $weather;
        }
        return $weathermaps;
    }

    /* a weighted priority to prefer being closer to the end point, else we keep looping back to the start */
    public function priority(int $minutes, array $position, array $end) : int
    {
        $distance = 3 * (abs($end[0] - $position[0]) + abs($end[1] - $position[1]));
        return -1 * ($minutes + 1 + $distance);
    }

    public function parse_input(Collection $input) : Vector
    {
        $blizzards = new Vector;

        $input = $input->map(fn($i)=>str_split($i))->toArray();
        foreach($input as $y => $row) {
            foreach($row as $x => $c) {
                if ($c === '.') continue;
                $c = match($c) {
                    '^' => BLZ::BLIZZARD_NORTH,
                    '<' => BLZ::BLIZZARD_WEST,
                    '>' => BLZ::BLIZZARD_EAST,
                    'v' => BLZ::BLIZZARD_SOUTH,
                    '#' => BLZ::WALL
                };
                $blizzards->push(new Blizzard($x, $y, $c));
            }
        }
        $this->width = count($input[0]);
        $this->height = count($input);

        return $blizzards;
    }

    public function print(Vector $map) : void
    {
        $m = [[]];
        for($y=0; $y<$this->height; $y++) {
            for($x=0; $x<$this->width; $x++) {
                $m[$y][$x] = '.';
            }
        }
        $m[0][1] = '.';
        $m[$this->height-1][$this->width-2] = '.';
        foreach($map as $point) {
            $m[$point->y][$point->x] = $point->type->value;
        }
        print_grid($m,1);
    }

    public function print_forecast(Set $map, array $position)
    {
        $m = [];
        for($y=0; $y<$this->height; $y++) {
            for($x=0; $x<$this->width; $x++) {
                $m[$y][$x] = $map->contains([$x, $y]) ? 'x' : '.';
            }
        }
        $m[$position[1]][$position[0]] = 'E';
        print_grid($m,1);
    }
 }
