<?php namespace day10_cathode_ray_tube;
use Lib\solver;
use Tightenco\Collect\Support\Collection;

class day10_cathode_ray_tube extends solver
{
    public function solve() : array
    {
        $this->start_timer();

        $cycles = $this->execute($this->input);
        $this->solution('10a', collect([20,60,100,140,180,220])->map(fn($s)=>$s*$cycles[$s])->sum());

        $this->render($cycles);
        $this->solution('10b', 'see output');

        return $this->solutions;
    }

    public function execute(Collection $input) : Collection
    {
        $cycles = collect([1]);
        $x = 1;
        foreach($input as $line) {
            [$cmd, $arg] = $this->get_command($line);
            $cycles->push($x);
            if ($cmd === 'addx') {
                $cycles->push($x);
                $x+=$arg;
            }
        }
        return $cycles;
    }

    public function render(Collection $cycles) : void
    {
        echo "\n=========== output for 10b ============\n";
        foreach (range(0, $cycles->count()-2) as $i) {
            $cycle = $i%40;
            echo $cycles[$i+1] === $cycle || $cycles[$i+1] === $cycle-1 || $cycles[$i+1] === $cycle+1 ? "\033[31m#" : "\033[0m ";
            if (($cycle%40)===39) echo "\n";
        }
        echo "=======================================\n\n";
    }

    private function get_command($line) : array
    {
        $cmd = explode(' ', $line);
        return [$cmd[0], isset($cmd[1]) ? (int)$cmd[1] : null];
    }
 }
