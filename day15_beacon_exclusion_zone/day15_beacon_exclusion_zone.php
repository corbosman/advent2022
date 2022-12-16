<?php namespace day15_beacon_exclusion_zone;
use Lib\solver;
use Tightenco\Collect\Support\Collection;

class day15_beacon_exclusion_zone extends solver
{
    public const Y = 2000000;
    public const MIN = -2;
    public const MAX = 4000000;
    public array $map = [];

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
        $points = [];

        foreach($sensors as $s1) {
            foreach($sensors as $s2) {
                /* if this sensor is at this precise distance, it's a candidate */
                if ($s1->distance($s2) === $s1->beacon_distance + $s2->beacon_distance + 2) {

                    /* since it's always a pair, just pick one of the 2 at random, the other one comes later */
                    if($s1->x < $s2->x) {

                        /* create the 2 intersection points p1/p2 where the 2 diamonds start "touching" */
                        if ($s1->x > $s2->x - $s2->beacon_distance - 1) {
                            $p1 = [$s1->x, $s1->y - $s1->beacon_distance - 1];
                        } else {
                            $p1 = [$s2->x - $s2->beacon_distance - 1, $s2->y];
                        }

                        if ($s2->x < $s1->x + $s1->beacon_distance + 1) {
                            $p2 = [$s2->x, $s2->y + $s2->beacon_distance + 1];
                        } else {
                            $p2 = [$s1->x + $s1->beacon_distance + 1, $s1->y];
                        }

                        /* build all points between p1 and p1 */
                        $points = $this->build_line($p1, $p2, $points);
                    }
                }
            }
        }

        /* now we have a set of point candidates, just try them */
        foreach($points as [$x, $y]) {
            foreach($sensors as $sensor2) {
                if ($this->distance($x, $y, $sensor2->x, $sensor2->y) <= $sensor2->beacon_distance) continue 2;
            }
            return [$x, $y];
        }

        die("should not happen!");
    }

    public function build_line(array $p1, array $p2, array $points) : array
    {
        $m = ($p1[1] - $p2[1]) / ($p1[0] - $p2[0]);
        $b = $p1[1] - $m * $p1[0];

        for ($i = $p1[0]; $i <= $p2[0]; $i++)
            $points[] = array($i, $m * $i + $b);

        return $points;
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
