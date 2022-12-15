<?php namespace day15_beacon_exclusion_zone;
use Lib\solver;
use Tightenco\Collect\Support\Collection;

class day15_beacon_exclusion_zone extends solver
{
    public function solve() : array
    {
        $this->start_timer();

        [$sensors, $beacons] = $this->parse_input($this->input);

        $positions = $this->part1($sensors, $beacons, 10);
        // $positions = $this->part1($sensors, 2000000);

        $this->solution('15a', $positions);

        return $this->solutions;
    }

    public function part1(Collection $sensors, Collection $beacons, $y) : int
    {
        $this->solution('1',1);

        /* for each sensor get the X range that overlaps */
        $overlaps = $this->find_diamond_cross_sections($sensors, $y);
        $this->solution('1',2);

        /* merge overlaps */
        $overlaps = $this->merge_overlaps($overlaps);

        /* there can be beacons or sensors on the same line, remove them */
        $overlaps = $this->remove_points_from_overlaps($overlaps, $beacons, $y);

        return count($overlap);
    }

//    public function remove_points_from_overlaps(array $overlaps, array $points, int $y) : array
//    {
//        $response = [];
//        foreach($overlaps as $overlap) {
//            foreach($points as $point) {
//                if ($point->y === $y && $overlap[0] <= $y && $overlap[$y] >= $y);
//            }
//        }
//    }

    /* find all ranges of X of sensors where its Y overlaps */
    public function find_diamond_cross_sections(Collection $sensors, int $y)
    {
        $overlaps = $sensors->map(fn($s)=>$this->cross_section($s, $y))
                            ->filter(fn($s)=>count($s) > 0)
                            ->sort(fn($a, $b) => $a[0] <=> $b[0]);
//        dd($overlaps);
//        $overlaps = [];
//        foreach($sensors as $sensor) {
//            // output("\nsensor at {$sensor->x},{$sensor->y} with beacon {$sensor->beacon->x},{$sensor->beacon->y} at distance {$sensor->beacon_distance}");
//            $overlaps[] = $this->overlaps($sensor, $y);
//        }
        dd($overlaps);
        return array_filter($overlaps, fn($i)=>count($i) > 0);
    }

    /* which X points does this sensor overlap with on line y */
    public function cross_section(Sensor $sensor, $y) : array
    {
        /* beacon distance */
        $distance = $sensor->beacon_distance;

        /* vertical distance from sensor to the line */
        $dy = abs($sensor->y - $y);

        /* first check if an overlap is even possible! If the Y coordinate is more than manhattan distance away it's never possible */
        if ($dy > $sensor->beacon_distance) return [];

        /* calculate the dx through manhattan distance */
        $dx = $distance - $dy;

        /* now return the range */
        // return range($sensor->x - $dx, $sensor->x + $dx);
        return [$sensor->x - $dx, $sensor->x + $dx];
    }

    /* take a set of ranges and merge them into larger ranges when they overlap */
    public function merge_overlaps($overlaps) : array
    {
        $stack = [];
        $i = 0;
        usort($overlaps, fn($a, $b) => $a[0] <=> $b[0]);
        $stack[] = $overlaps[0];
        foreach($overlaps as $k => $overlap) {
            if ($overlap[0] <= $stack[$i][1]+1) {
                $stack[$i][1] = max($overlap[1], $stack[$i][1]);
            } else {
                $stack[++$i] = $overlap;
            }
        }
        return $stack;
    }

    /* get all the x coordinates of a set of points at a specific y */
    public function get_x(array $points, int $y) : array
    {
        return array_unique(array_reduce($points, fn($c, $p)=>$p->y === $y ? array_merge($c, [$p->x]) : $c, []));
    }

    public function parse_input(Collection $input) : array
    {
        $sensors = collect();
        $beacons = collect();

        foreach($input as $k => $line) {
            preg_match('/^Sensor at x=(-?\d+), y=(-?\d+): closest beacon is at x=(-?\d+), y=(-?\d+)$/', $line, $matches);
            $beacon = new Beacon((int)$matches[3], (int)$matches[4]);
            $beacons->push($beacon);
            $sensors->push(new Sensor((int)$matches[1], (int)$matches[2], $beacon));
        }

        return [$sensors, $beacons->unique()->values()];
    }

 }
