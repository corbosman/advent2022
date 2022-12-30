<?php namespace day23_unstable_diffusion;
use Ds\Map;
use Lib\solver;
use Tightenco\Collect\Support\Collection;

class day23_unstable_diffusion extends solver
{
    public array $directions = [
        [[-1,-1],[0,-1],[1,-1]],  // NW, N, NE
        [[-1, 1],[0, 1],[1, 1]],  // SW, S, SE
        [[-1,-1],[-1,0],[-1,1]],  // NW, W, SW
        [[ 1,-1],[1, 0],[1, 1]]   // NE, E, SE
    ];

    public function solve() : array
    {
        $this->start_timer();

        $elves = $this->parse_input($this->input);
        $this->solution('23b', $this->rounds($elves));

        return $this->solutions;
    }

    public function rounds(Map $elves) : int
    {
        $rounds = 0;
        while(true) {
            [$elves, $proposals] = $this->first_half($elves, $rounds % 4);

            if ($rounds === 10) {
                $this->solution('23a', $this->count_empty_positions($elves));
            }

            if ($proposals->count() === 0) return $rounds + 1;

            $elves = $this->second_half($elves, $proposals);
            $rounds++;
        }
    }

    public function first_half(Map $elves, int $first_direction) : array
    {
        $proposals = new Map;

        /* @var Elf $elf */
        foreach($elves->keys() as $elf_position) {
            $elf = $elves->get($elf_position);
            [$x, $y] = $elf_position;

            /* look at every direction to see if we can move there */
            $possible_directions = 0;
            $next_proposal = null;
            for($i=$first_direction; $i<($first_direction+4); $i++) {
                if ($this->look_at_direction($elves, $x, $y, $i % 4) === false) {
                    $possible_directions++;
                    if ($next_proposal === null)  $next_proposal = [$x + $this->directions[$i % 4][1][0], $y + $this->directions[$i % 4][1][1]];
                }
            }

            /* we can't move */
            if ($possible_directions === 0 || $possible_directions === 4) continue;

            /* register the new proposed position */
            $proposals->put($next_proposal, $proposals->get($next_proposal, 0) + 1);
            $elf->proposal = $next_proposal;
        }

        return [$elves, $proposals];
    }

    public function second_half(Map $elves, Map $proposals) : Map
    {
        /* @var Elf $elf */
        foreach($elves->keys() as $key) {
            $elf = $elves->get($key);

            /* only move an elf if there is 1 elf moving to that position */
            if ($elf->proposal !== null && ($proposals->get($elf->proposal,0) === 1)) {
                $elves->put($elf->proposal, $elf);
                $elves->remove($key);
            }
            $elf->proposal = null;
        }
        return $elves;
    }

    public function look_at_direction(Map $elves, int $x, int $y, int $direction) : bool
    {
        foreach($this->directions[$direction] as [$dx, $dy]) {
            if ($elves->hasKey([$x+$dx, $y+$dy])) return true;
        }
        return false;
    }

    public function count_empty_positions(Map $elves) : int
    {
        $top_left     = [ INFINITE,  INFINITE];
        $bottom_right = [-INFINITE, -INFINITE];

        foreach($elves as $position => $elf) {
            [$x, $y] = $position;
            if ($x < $top_left[0]) $top_left = [$x, $top_left[1]];
            if ($x > $bottom_right[0]) $bottom_right = [$x, $bottom_right[1]];
            if ($y < $top_left[1]) $top_left = [$top_left[0], $y];
            if ($y > $bottom_right[1]) $bottom_right = [$bottom_right[0], $y];
        }
        $total_positions = (($bottom_right[0] - $top_left[0]) + 1) * (($bottom_right[1] - $top_left[1]) + 1);
        $number_of_elves = $elves->count();

        return $total_positions - $number_of_elves;
    }

    public function parse_input(Collection $input) : Map
    {
        $elves = new Map;
        foreach($input as $y => $row) {
            foreach(str_split($input[$y]) as $x => $chr) {
                if ($chr === '#') {
                    $elves->put([$x, $y], new Elf);
                }
            }
        }
        return $elves;
    }

    public function print(Map $elves) : void
    {
        for($y=-5; $y<15; $y++) {
            for($x=-5; $x<15; $x++) {
                echo $elves->hasKey([$x, $y]) ? '#' : '.';
            }
            echo "\n";
        }
        echo "\n";
    }
 }
