<?php namespace day19_not_enough_minerals;
use Ds\Deque;
use Ds\Map;
use Lib\solver;
use Tightenco\Collect\Support\Collection;

class day19_not_enough_minerals extends solver
{
    public int $top_geodes = 0;    // keep score of the top number of geodes we've seen over all blueprints

    public function solve() : array
    {
        $this->start_timer();

        $blueprints = $this->parse_input($this->input);
        $this->solution('19a', $this->part1($blueprints));
        $this->solution('19b', $this->part2($blueprints));

        return $this->solutions;
    }

    public function part1(Collection $blueprints) : int
    {
        return $blueprints->map(fn($bp) => $this->mine_geodes($bp, 24))->map(fn($g,$k)=>$g*($k+1))->sum();
    }

    public function part2(Collection $blueprints) : int
    {
        return $blueprints->take(3)->map(fn($bp) => $this->mine_geodes($bp, 32))->reduce(fn($carry, $geode) => $carry*=$geode, 1);
    }

    /* 0=time 1=ore 2=clay 3=obsidian 4=geode 5=ore_robot 6=clay_robot 7=obsidian_robot 8=geode_robot */
    public function mine_geodes(Blueprint $bp, int $max_time) : int
    {
        $max_geodes = 0;
        $cache = new Map;
        $queue = new Deque;
        $queue->push([$max_time, 0, [0,0,0,1,0,0,0]]);
        $states = 1;

        while($queue->count() > 0) {
            $states++;
            [$time, $geodes, $state] = $queue->shift();

            if ($time <= 0)  {
                $max_geodes = max($max_geodes, $geodes);
                continue;
            }

            if ($cache->hasKey($state)) continue;

            // $this->print_state($state);
            $cache->put($state, 1);

            if ($this->can_build_geode_robot($state, $bp)) {
                $next_state = $this->mine($state, $bp);
                $next_state = $this->build_geode_robot($next_state, $bp);
                $queue->push([$time-1, $geodes+$state[6], $next_state]);
                continue;
            }

            if ($this->can_build_obsidian_robot($state, $bp)) {
                $next_state = $this->mine($state, $bp);
                $next_state = $this->build_obsidian_robot($next_state, $bp);
                $queue->push([$time-1, $geodes+$state[6], $next_state]);

            }

            if ($this->can_build_clay_robot($state, $bp)) {
                $next_state = $this->mine($state, $bp);
                $next_state = $this->build_clay_robot($next_state, $bp);
                $queue->push([$time-1, $geodes+$state[6], $next_state]);
            }

            if ($this->can_build_ore_robot($state, $bp)) {
                $next_state = $this->mine($state, $bp);
                $next_state = $this->build_ore_robot($next_state, $bp);
                $queue->push([$time-1, $geodes+$state[6], $next_state]);
            }

            /* dont build anything, just mine resources */
            $state = $this->mine($state, $bp);
            $queue->push([$time-1, $geodes+$state[6], $state]);
        }
        output("geodes={$max_geodes} states={$states}");
        return $max_geodes;
    }

    /* 0=time 1=ore 2=clay 3=obsidian 4=geode 5=ore_robot 6=clay_robot 7=obsidian_robot 8=geode_robot */
    public function mine(array $state, Blueprint $bp) : array
    {
        $state[0] += $state[3];         // mine ore
        $state[1] += $state[4];         // mine clay
        $state[2] += $state[5];         // mine obsidian

        return $state;
    }

    /* 0=time 1=ore 2=clay 3=obsidian 4=geode 5=ore_robot 6=clay_robot 7=obsidian_robot 8=geode_robot */
    public function can_build_geode_robot(array $state, Blueprint $bp) : bool
    {
        /* not enough ore */
        if ($state[0] < $bp->geode_robot_ore) return false;

        /* not enough obsidian */
        if ($state[2] < $bp->geode_robot_obsidian) return false;

        return true;
    }

    /* 0=time 1=ore 2=clay 3=obsidian 4=geode 5=ore_robot 6=clay_robot 7=obsidian_robot 8=geode_robot */
    private function build_geode_robot(array $state, Blueprint $bp): array
    {
        $state[6]++;                                // geode robot
        $state[0] -= $bp->geode_robot_ore;          // ore
        $state[2] -= $bp->geode_robot_obsidian;     // obsidian
        return $state;
    }

    /* 0=time 1=ore 2=clay 3=obsidian 4=geode 5=ore_robot 6=clay_robot 7=obsidian_robot 8=geode_robot */
    public function can_build_obsidian_robot(array $state, Blueprint $bp) : bool
    {
        /* not enough ore */
        if($state[0] < $bp->obsidian_robot_ore) return false;

        /* not enough clay */
        if($state[1] < $bp->obsidian_robot_clay) return false;

        /* dont build more obsidian robots than the max cost of the geode robot */
        if ($state[5] >= $bp->max_obsidian_cost) return false;

        return true;
    }

    /* 0=time 1=ore 2=clay 3=obsidian 4=geode 5=ore_robot 6=clay_robot 7=obsidian_robot 8=geode_robot */
    public function build_obsidian_robot(array $state, Blueprint $bp) : array
    {
        $state[5]++;                                // obsidian robot
        $state[0] -= $bp->obsidian_robot_ore;       // ore
        $state[1] -= $bp->obsidian_robot_clay;      // clay
        return $state;
    }

    /* 0=time 1=ore 2=clay 3=obsidian 4=geode 5=ore_robot 6=clay_robot 7=obsidian_robot 8=geode_robot */
    public function can_build_clay_robot(array $state, Blueprint $bp) : bool
    {
        /* not enough ore */
        if ($state[0] < $bp->clay_robot_ore) return false;

        /* never build more clay robots than the max cost of the obsidian robot */
        if ($state[4] >= $bp->max_clay_cost) return false;

        return true;
    }

    /* 0=time 1=ore 2=clay 3=obsidian 4=geode 5=ore_robot 6=clay_robot 7=obsidian_robot 8=geode_robot */
    public function build_clay_robot(array $state, Blueprint $bp) : array
    {
        $state[4]++;                                // clay robot
        $state[0] -= $bp->clay_robot_ore;           // ore
        return $state;
    }

    /* 0=time 1=ore 2=clay 3=obsidian 4=geode 5=ore_robot 6=clay_robot 7=obsidian_robot 8=geode_robot */
    public function can_build_ore_robot(array $state, Blueprint $bp) : bool
    {
        /* not enough ore */
        if ($state[0] < $bp->ore_robot_ore) return false;

        /* never build more ore robots than the largest ore cost */
        if ($state[3] >= $bp->max_ore_cost) return false;

        return true;
    }

    /* 0=time 1=ore 2=clay 3=obsidian 4=geode 5=ore_robot 6=clay_robot 7=obsidian_robot 8=geode_robot */
    public function build_ore_robot(array $state, Blueprint $bp) : array
    {
        $state[3]++;                                // ore robot
        $state[0] -= $bp->ore_robot_ore;            // ore
        return $state;
    }

    /* 0=time 1=ore 2=clay 3=obsidian 4=geode 5=ore_robot 6=clay_robot 7=obsidian_robot 8=geode_robot */
    public function print_state($state) : void
    {
        output("time={$state[0]} ore={$state[1]} clay={$state[2]} obsidian={$state[3]} geode={$state[4]} ore_robot={$state[5]} clay_robot={$state[6]} obsidian_robot={$state[7]} geode_robot={$state[8]}");
    }

    public function parse_input(Collection $input) : Collection
    {
        return $input->map(function($blueprint) {
            preg_match_all('!(\d+)!', $blueprint, $m);
            return new Blueprint($m[0][1],$m[0][2],$m[0][3],$m[0][4],$m[0][5],$m[0][6]);
        });
    }
}
