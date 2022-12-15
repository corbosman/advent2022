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
        $cross_sections = $this->find_diamond_cross_sections($sensors, $y);
        $total_points = $cross_sections->reduce(fn($c, $i) => $i[1] - $i[0] + 1,0);
        $beacons_on_cross_section = $beacons->merge($sensors)->filter(fn($b)=> $this->on_cross_section($b, $y, $cross_sections));
        return $total_points - $beacons_on_cross_section->count();
    }

    /* find all ranges of X of sensors where its Y overlaps */
    public function find_diamond_cross_sections(Collection $sensors, int $y) : Collection
    {
        $cross_sections = $sensors->reduce(function($c, $sensor) use ($y) {
            $dy = abs($sensor->y - $y);
            $dx = $sensor->beacon_distance - $dy;
            if ($dy > $sensor->beacon_distance) return $c;
            return $c->push([$sensor->x - $dx, $sensor->x + $dx]);
        }, collect())->sort(fn($a, $b) => $a[0] <=> $b[0])->values();

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

    /* check if a point falls within a range */
    public function on_cross_section(Point $point, int $y, Collection $overlaps) : bool
    {
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
