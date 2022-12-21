<?php namespace day16_proboscidea_volcanium;

class Dijkstra
{
    public function distances($valve, $valves, $tunnels) : array
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
}
