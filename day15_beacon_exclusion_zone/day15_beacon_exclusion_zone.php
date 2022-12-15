<?php namespace day15_beacon_exclusion_zone;
use Lib\solver;
use Tightenco\Collect\Support\Collection;

class day15_beacon_exclusion_zone extends solver
{
    public function solve() : array
    {
        $this->start_timer();

        [$sensors, $beacons] = $this->parse_input($this->input);

        // $positions = $this->part1($sensors, $beacons, 10);
        $positions = $this->part1($sensors, $beacons, 2000000);

        $this->solution('15a', $positions);

        return $this->solutions;
    }

    public function part1(Collection $sensors, Collection $beacons, $y) : int
    {
        /* get all the diamond cross-sections */
        $cross_sections = $this->find_diamond_cross_sections($sensors, $y);

        /* total points that are on the cross-sections, just add up the ranges */
        $total_points = $cross_sections->reduce(fn($c, $i) => $i[1] - $i[0] + 1,0);

        /* we may have some beacons or sensors on the cross-sections */
        $beacons_on_cross_section = $beacons->merge($sensors)->filter(fn($b)=> $this->on_cross_section($b, $y, $cross_sections));

        return $total_points - $beacons_on_cross_section->count();
    }

    /* find all ranges of X of sensors where its Y overlaps */
    public function find_diamond_cross_sections(Collection $sensors, int $y) : Collection
    {
        $cross_sections = $sensors->map(fn($s)=>$this->cross_section($s, $y))
                                  ->filter(fn($s)=>count($s) > 0)
                                  ->sort(fn($a, $b) => $a[0] <=> $b[0])
                                  ->values();

        return $this->merge_cross_sections($cross_sections);

    }

    /* take a set of ranges and merge them into larger ranges when they overlap */
    public function merge_cross_sections(Collection $cross_sections) : Collection
    {
        $stack = [];
        $i = 0;
        $stack[] = $cross_sections[0];

        foreach($cross_sections as $k => $overlap) {
            if ($overlap[0] <= $stack[$i][1]+1) {
                $stack[$i][1] = max($overlap[1], $stack[$i][1]);
            } else {
                $stack[++$i] = $overlap;
            }
        }

        return collect($stack);
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
        return [$sensor->x - $dx, $sensor->x + $dx];
    }

    /* check if a point falls within a range */
    public function on_cross_section(Point $point, int $y, Collection $overlaps) : bool
    {
        /* y is not the same, not on this cross-section */
        if ($y !== $point->y) return false;

        foreach($overlaps as $overlap) {
            if ($y>= $overlap[0] && $y <= $overlap[1]) return true;
        }

        return false;
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
