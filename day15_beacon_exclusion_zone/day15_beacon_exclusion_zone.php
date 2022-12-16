<?php namespace day15_beacon_exclusion_zone;
use Lib\solver;
use Tightenco\Collect\Support\Collection;

class day15_beacon_exclusion_zone extends solver
{
    public const Y = 2000000;
    public const MAX = 4000000;

    public function solve() : array
    {
        $this->start_timer();
        [$sensors, $beacons] = $this->parse_input($this->input);
        $points = $this->part1($sensors, $beacons, self::Y);
        $this->solution('15a', $points);

        $point = $this->part2($sensors, $beacons);

        $this->solution('15b', 4000000 * $point[0] + $point[1]);

        return $this->solutions;
    }

    public function part1(Collection $sensors, Collection $beacons, $y) : int
    {
        $ranges = $this->get_ranges($sensors, $y);
        $total_points = $ranges->reduce(fn($c, $i) => $i[1] - $i[0] + 1,0);
        $beacons_on_cross_section = $beacons->merge($sensors)->filter(fn($b)=> $this->on_range($b, $y, $ranges));
        return $total_points - $beacons_on_cross_section->count();
    }

    public function part2(Collection $sensors, Collection $beacons)
    {
        /* first find all the sensors that have an edge close to another sensor */
        $sensors = $sensors->map(function($s1) use ($sensors) {
            foreach ($sensors as $s2) {
                if ($s1->distance($s2) === $s1->beacon_distance + $s2->beacon_distance + 2) {
                    $s1->close[] = $s2;
                    return $s1;
                }
            }
            return $s1;
        });

        /* now get all points around those edges and see if they fall within a beacon range */
        foreach($sensors as $s) {
            if (count($s->close) === 0) continue;

            $sx = $s->x; $sy = $s->y; $d = $s->beacon_distance;
            foreach(range(0, $s->beacon_distance+1) as $dx) {
                $dy = $s->beacon_distance - $dx + 1;
                foreach([[$sx+$dx, $sy+$dy], [$sx+$dx, $sy-$dy], [$sx-$dx, $sy+$dy], [$sx-$dx, $sy-$dy]] as [$x,$y]) {
                    if ($x<0 || $y<0 || $x>self::MAX || $y>self::MAX) continue;

                    foreach($sensors as $sensor2) {
                        if ($this->distance($x, $y, $sensor2->x, $sensor2->y) <= $sensor2->beacon_distance) continue 2;
                    }

                    return [$x, $y];
                }
            }
        }
        return -1;
    }

    /* find all ranges that cross sensors on line Y, and merge them into combined ranges */
    public function get_ranges(Collection $sensors, int $y) : Collection
    {
        return $this->merge_ranges($sensors->reduce(function($c, $sensor) use ($y) {
            $dy = abs($sensor->y - $y);
            $dx = $sensor->beacon_distance - $dy;
            if ($dy > $sensor->beacon_distance) return $c;
            return $c->push([$sensor->x - $dx, $sensor->x + $dx]);
        }, collect())->sort(fn($a, $b) => $a[0] <=> $b[0])->values());
    }

    /* take a set of ranges and merge them into larger ranges when they overlap */
    public function merge_ranges(Collection $ranges) : Collection
    {
        $stack = [];
        $i = 0;
        $stack[] = $ranges[0];

        foreach($ranges as $k => $range) {
            if ($range[0] <= $stack[$i][1]+1) {
                $stack[$i][1] = max($range[1], $stack[$i][1]);
            } else {
                $stack[++$i] = $range;
            }
        }
        return collect($stack);
    }

    /* check if a point falls within a range */
    public function on_range(Point $point, int $y, Collection $overlaps) : bool
    {
        if ($y !== $point->y) return false;
        foreach($overlaps as $overlap) {
            if ($y>= $overlap[0] && $y <= $overlap[1]) return true;
        }
        return false;
    }

    public function distance($x1, $y1, $x2, $y2) : int
    {
        return abs($x1 - $x2) + abs($y1 - $y2);
    }

    public function parse_input(Collection $input) : array
    {
        $sensors = collect();
        $beacons = collect();

        foreach($input as $k => $line) {
            preg_match('/^Sensor at x=(-?\d+), y=(-?\d+): closest beacon is at x=(-?\d+), y=(-?\d+)$/', $line, $matches);
            $beacon = new Beacon((int)$matches[3], (int)$matches[4]);
            $beacons->push($beacon);
            $sensor = new Sensor((int)$matches[1], (int)$matches[2], $beacon);
            $sensors->push($sensor);
        }

        return [$sensors, $beacons->unique()->values()];
    }
 }
