<?php namespace day14_regolith_reservoir;
use Lib\solver;
use Tightenco\Collect\Support\Collection;

class day14_regolith_reservoir extends solver
{
    public array $map;
    public int  $part1_depth = 0;
    public bool $part1_reached = false;
    public int  $grains_part2 = 0;

    public function solve() : array
    {
        $this->start_timer();

        $this->map = $this->create_rock($this->input);
        $this->fill_sand(500, 0);
        // solution for 14a is set in the code below
        $this->solution('14b', $this->grains_part2);

        return $this->solutions;
    }

    public function fill_sand(int $x, int $y) : void
    {
        /* we can not continue on this point since it's already occupied */
        if (isset($this->map[$y][$x])) return;

        /* we reached the depth for part1, record it */
        if ($y === $this->part1_depth+1 && !$this->part1_reached) {
            $this->solution('14a', $this->grains_part2);
            $this->part1_reached = true;
        }

        $this->fill_sand($x,   $y+1); // try to fill below
        $this->fill_sand($x-1, $y+1); // try to fill to the left
        $this->fill_sand($x+1, $y+1); // try to fill to the right

        $this->map[$y][$x] = 'o';
        $this->grains_part2++;
    }

    public function create_rock(Collection $input) : array
    {
        $map = [];
        foreach($input as $line) {
            $rocks = array_map(fn($i)=>explode(',', $i) ,explode(' -> ', $line));
            $n = count($rocks);
            for($i=0; $i<$n-1;$i++) {
                foreach(range($rocks[$i][0],$rocks[$i+1][0]) as $x) {
                    foreach(range($rocks[$i][1], $rocks[$i+1][1]) as $y) {
                        $map[$y][$x] = '#';
                    }
                }
            }
        }

        /* set the maximum depth of the cave */
        $this->part1_depth = max(array_keys($map));

        /* now build the floor, values just hardcoded */
        for($x=325; $x<=675;$x++) {
            $map[$this->part1_depth+2][$x] = '#';
        }

        return $map;
    }
 }
