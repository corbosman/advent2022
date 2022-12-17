<?php namespace day16_proboscidea_volcanium;
use day12_hill_climbing_algorithm\Heap;
use Ds\Queue;
use Lib\solver;
use Tightenco\Collect\Support\Collection;

class day16_proboscidea_volcanium extends solver
{
    public function solve() : array
    {
        $this->start_timer();

        [$flowrates, $tunnels] = $this->parse_input($this->input);
        $pressure = $this->part1($flowrates, $tunnels);

        $this->solution('16a', $pressure);

        return $this->solutions;
    }

    public function part1(array $flowrates, array $tunnels)
    {
        /* a graph of travel times between all nodes, but skip nodes where we wont open valves */
        $travel_time = $this->build_graph($flowrates, $tunnels);

        return $this->calc_flow(30, 'AA', [], $travel_time, $flowrates);
    }

    public function build_graph(array $flowrates, array $tunnels) : array
    {
        foreach($flowrates as $valve => $rate) {
            $travel_time[$valve] = $this->dijkstra($valve, $flowrates, $tunnels);
        }

        return $travel_time;
    }


    public function dijkstra($valve, $valves, $tunnels)
    {
        $q = new Heap;
        $q->insert($valve, 0);
        $visited[$valve] = 1;
        $distances[$valve] = 0;

        while($q->count() > 0) {
            $valve = $q->extract();

            $neighbors = $tunnels[$valve];

            foreach($neighbors as $neighbor) {

                if (isset($visited[$neighbor])) continue;

                $distance = $distances[$valve] + 1;
                $distance_n = $distances[$neighbor] ?? INFINITE;

                if ($distance < $distance_n) {
                    $distances[$neighbor] = $distance;
                    $q->insert($neighbor, $distance);
                }
            }
            $visited[$neighbor] = 1;
        }

        /* remove all zero distances, we never go to ourselves */
        $distances = array_filter($distances, fn($v)=>$v !== 0);

        /* it's useless going to a neighbor where you cant open a valve */
        return array_filter($distances, fn($v)=>$valves[$v] !== 0, ARRAY_FILTER_USE_KEY);
    }

    public function calc_flow(int $time, string $valve, array $opened, array $travel_time, array $flowrates)
    {
        $flow = 0;
        $neighbors = $travel_time[$valve];

        foreach($neighbors as $neighbor => $t) {
            if (isset($opened[$neighbor])) continue;

            /* we have to travel to the next valve, subtract its distance */
            $time_to_neighbor = $time - $t;

            /* now we're opening a valve, subtract another minute */
            $time_to_neighbor--;

            if ($time_to_neighbor <= 0) continue;

            /* calculate flow to this neighbor */
            $neighbor_flow = $this->calc_flow($time_to_neighbor, $neighbor, $this->open($opened, $neighbor), $travel_time, $flowrates) + ( $time_to_neighbor * $flowrates[$neighbor]);

            /* take the largest flow */
            $flow = max($flow, $neighbor_flow);
        }

        return $flow;
    }

    public function open($opened, $valve)
    {
        $opened[$valve] = 1;
        return $opened;
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
