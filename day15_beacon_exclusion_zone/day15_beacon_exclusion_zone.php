<?php namespace day15_beacon_exclusion_zone;
use Lib\solver;
use Tightenco\Collect\Support\Collection;

class day15_beacon_exclusion_zone extends solver
{
    public function solve() : array
    {
        $this->start_timer();

        [$sensors, $beacons] = $this->parse_input($this->input);
        //$positions = $this->part1($sensors, 10);
        $positions = $this->part1($sensors, 2000000);

        $this->solution('15a', $positions);

        return $this->solutions;
    }

    public function part1(array $sensors, $y) : int
    {
        /* for each sensor get the X range that overlaps */
        $overlaps = $this->find_overlapping_sensors($sensors, $y);
        /* combine them into unique array */
        $unique_overlaps = array_unique(array_reduce($overlaps, 'array_merge', []));
        /* sort for visual inspection */
        sort($unique_overlaps);

        /* now we have to remove the beacons from this array */
        $beacons = array_map(fn($s)=>$s->beacon, $sensors);
        $beacons_x = $this->get_x($beacons, $y);

        $overlap = array_filter($unique_overlaps, fn($x)=>!in_array($x, $beacons_x, true));
        return count($overlap);
    }

    /* find all ranges of X of sensors where its Y overlaps */
    public function find_overlapping_sensors(array $sensors, int $y)
    {
        $overlaps = [];
        foreach($sensors as $sensor) {
            // output("\nsensor at {$sensor->x},{$sensor->y} with beacon {$sensor->beacon->x},{$sensor->beacon->y} at distance {$sensor->beacon_distance}");
            $overlaps[] = $this->overlaps($sensor, $y);
        }
        return $overlaps;
    }

    /* which X points does this sensor overlap with on line y */
    public function overlaps(Sensor $sensor, $y) : array
    {
        /* beacon distance */
        $distance = $sensor->beacon_distance;

        /* vertical distance from sensor to the line */
        $dy = abs($sensor->y - $y);

        /* first check if an overlap is even possible! If the Y coordinate is more than manhattan distance away it's never possible */
        if ($dy > $sensor->beacon_distance) return [];

        /* calculate the dx through manhattan distance */
        $dx = $distance - $dy;

        /* now return all the overlapping points */
        return range($sensor->x - $dx , $sensor->x + $dx);
    }

    /* get all the x coordinates of a set of points at a specific y */
    public function get_x(array $points, int $y) : array
    {
        return array_unique(array_reduce($points, fn($c, $p)=>$p->y === $y ? array_merge($c, [$p->x]) : $c, []));
    }

    /* manhattan distance between 2 points */
    public function distance(Point $p1, Point $p2) : int
    {
        return abs($p1->x - $p2->x) + abs($p1->y - $p2->y);
    }

    public function parse_input(Collection $input) : array
    {
        $sensors = [];
        $beacons = [];

        foreach($input as $k => $line) {
            preg_match('/^Sensor at x=(-?\d+), y=(-?\d+): closest beacon is at x=(-?\d+), y=(-?\d+)$/', $line, $matches);
            $beacon = new Beacon((int)$matches[3], (int)$matches[4]);
            $sensors[] = new Sensor((int)$matches[1], (int)$matches[2], $beacon);
        }

        return [$sensors, $beacons];
    }

 }
