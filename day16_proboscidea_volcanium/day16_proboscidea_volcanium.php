<?php namespace day16_proboscidea_volcanium;
use Lib\solver;
use Tightenco\Collect\Support\Collection;

class day16_proboscidea_volcanium extends solver
{
    public array $valves_that_release_flow = [];    // contains only those valves that actually release flow, anything else is not a useful travel target
    public array $valve_distances = [];             // contains distances from every valve to every other valve
    public array $cache = [];                       // memoization cache
    public array $max_pressure = [];                // maximum pressure we see for each set of valve states

    public function solve() : array
    {
        $this->start_timer();

        [$flowrates, $tunnels] = $this->parse_input($this->input);
        $this->valve_distances = $this->build_graph($flowrates, $tunnels);
        $this->valves_that_release_flow = array_map(fn($f)=>1<<$f, array_flip(array_keys(array_filter($flowrates, fn($f) => $f!==0))));

        $pressure = $this->calc_flow(30, 'AA', 0b0, 0, $flowrates,  false);
        $this->solution('16a', $pressure);

        $pressure = $this->part2($flowrates);
        $this->solution('16b', $pressure);

        return $this->solutions;
    }

    /* semi brute force. Calculate a normal 26/AA path, and for each possibility calculate the best score with the unopened valves */
    public function part2(array $flowrates)
    {
        $this->max_pressure = [];
        $this->cache = [];
        $this->calc_flow(26, 'AA', 0b0, 0, $flowrates, true);

        $max = 0;
        foreach($this->max_pressure as $opened => $pressure) {
            $max = max($max, $this->calc_flow(26, 'AA', $opened, 0, $flowrates, false) + $pressure);
        }
        return $max;
    }

    public function calc_flow(int $time, string $valve, int $opened, int $total_pressure, array $flowrates, $part2)
    {
        if ($time === 0) return 0;

        $state = serialize("{$time}_{$valve}_{$opened}");
        $cache_hit = $this->cache[$state] ?? null;
        if ($cache_hit) return $cache_hit;

        $max_pressure = 0;
        $valves_we_can_visit = $this->valve_distances[$valve];

        /* try to open the valve */
        if ($valve !== 'AA' && ($opened & $this->valves_that_release_flow[$valve]) === 0 ) {
            $next_open = $opened | $this->valves_that_release_flow[$valve];
            $pressure = ($time-1) * $flowrates[$valve];
            $max_pressure = max($max_pressure, ($this->calc_flow($time-1, $valve, $next_open, $total_pressure+$pressure, $flowrates, $part2) + $pressure));
        }

        /* or move to other unopened valves */
        foreach($valves_we_can_visit as $v => $t) {
            if ($time - $t - 1 <= 0) continue;                               // cant reach it
            if ($opened & $this->valves_that_release_flow[$v]) continue;     // already open

            $max_pressure = max($max_pressure, $this->calc_flow($time-$t, $v, $opened, $total_pressure, $flowrates, $part2));
        }

        if ($part2) $this->max_pressure[$opened] = max($this->max_pressure[$opened] ?? 0, $total_pressure);

        $this->cache[$state] = $max_pressure;
        return $max_pressure;
    }

    public function build_graph(array $flowrates, array $tunnels) : array
    {
        foreach($flowrates as $valve => $rate)
            $travel_time[$valve] = (new Dijkstra)->distances($valve, $flowrates, $tunnels);
        return $travel_time;
    }

    public function parse_input(Collection $input) : array
    {
        foreach($input as $i) {
            preg_match('/Valve (.*) has flow rate=(\d+); tunnel.? lead.? to valve.? (.*)$/', $i, $matches);
            [$_, $valve, $flowrate, $t] = $matches;
            $flowrates[$valve] = (int)$flowrate;
            $tunnels[$valve] = explode(', ', $t);
        }
        return [$flowrates, $tunnels];
    }
 }
