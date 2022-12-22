<?php namespace day16_proboscidea_volcanium;
use Lib\solver;
use Tightenco\Collect\Support\Collection;

class day16_proboscidea_volcanium extends solver
{
    public array $valves_bitmap = [];               // an array of bit masks for every valve with a flowrate > 0
    public array $valves_distances = [];            // contains distances from every valve to every other valve
    public array $cache = [];                       // memoization cache
    public array $max_pressure = [];                // maximum pressure we see for each set of valve states

    public function solve() : array
    {
        $this->start_timer();

        [$flowrates, $tunnels] = $this->parse_input($this->input);
        $this->valves_distances = $this->build_graph($flowrates, $tunnels);
        $this->valves_bitmap = array_map(fn($f)=>1<<$f, array_flip(array_keys(array_filter($flowrates, fn($f) => $f!==0))));

        $pressure = $this->calc_flow(30, 'AA', 0b0, 0, $flowrates,  false);
        $this->solution('16a', $pressure);

        $pressure = $this->part2($flowrates);
        $this->solution('16b', $pressure);

        return $this->solutions;
    }

    /* during our run we maintained a max pressure for each possible set of open valves, part2 is simply finding the best combination */
    public function part2(array $valves)
    {
        $this->cache = [];
        $p = [];

        $this->calc_flow(26, 'AA', 0b0, 0, $valves, true);

        foreach($this->max_pressure as $opened => $pressure_player) {
            foreach($this->max_pressure as $unopened => $pressure_elephant) {
               if (($opened & $unopened) === 0) $p[] = $pressure_player + $pressure_elephant;
            }
        }
        return max($p);
    }

    public function calc_flow(int $time, string $valve, int $opened, int $total_pressure, array $valves, $part2)
    {
        if ($time === 0) return 0;

        $state = "{$time}_{$valve}_{$opened}";
        $cache_hit = $this->cache[$state] ?? null;
        if ($cache_hit) return $cache_hit;

        $max_pressure = 0;
        $valves_we_can_visit = $this->valves_distances[$valve];

        /* or move to other unopened valves */
        foreach($valves_we_can_visit as $v => $t) {
            $next_time = $time - $t - 1;
            if ($time - $t - 1 <= 0) continue;                    // cant reach it
            if ($opened & $this->valves_bitmap[$v]) continue;     // already open

            /* this is a trick to skip half the size of the tree. Jump AND open to a new valve, as we only care about valves we can open! */
            $next_open = $opened | $this->valves_bitmap[$v];
            $next_pressure = ($next_time) * $valves[$v];
            $max_pressure = max($max_pressure, $this->calc_flow($time-$t-1, $v, $next_open, $total_pressure+$next_pressure, $valves, $part2) + $next_pressure);
        }

        if ($part2) $this->max_pressure[$opened] = max($this->max_pressure[$opened] ?? 0, $total_pressure);

        $this->cache[$state] = $max_pressure;
        return $max_pressure;
    }

    public function build_graph(array $valves, array $tunnels) : array
    {
        foreach($valves as $valve => $rate)
            $travel_time[$valve] = (new Dijkstra)->distances($valve, $valves, $tunnels);
        return $travel_time;
    }

    public function parse_input(Collection $input) : array
    {
        foreach($input as $i) {
            preg_match('/Valve (.*) has flow rate=(\d+); tunnel.? lead.? to valve.? (.*)$/', $i, $matches);
            [$_, $valve, $flowrate, $t] = $matches;
            $valves[$valve] = (int)$flowrate;
            $tunnels[$valve] = explode(', ', $t);
        }
        return [$valves, $tunnels];
    }
 }
