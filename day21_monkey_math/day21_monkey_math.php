<?php namespace day21_monkey_math;
use Ds\Deque;
use Ds\Map;
use Lib\solver;
use Tightenco\Collect\Support\Collection;

class day21_monkey_math extends solver
{
    public function solve() : array
    {
        $this->start_timer();

        $monkeys = $this->parse_input($this->input);
        $this->solution('21a', $this->part1($monkeys));

        return $this->solutions;
    }

    public function part1(Collection $monkeys) : int
    {
        $queue = new Deque;
        $done = new Map;

        foreach($monkeys as $monkey => $job) {
            if (is_int($job)) $done->put($monkey, $job);
            else $queue->push([$monkey, $job]);
        }

        while($queue->count() > 0) {
            [$monkey, $job] = $queue->shift();
            if (!is_int($job[0]) && $done->hasKey($job[0])) $job[0] = $done[$job[0]];
            if (!is_int($job[2]) && $done->hasKey($job[2])) $job[2] = $done[$job[2]];

            if (is_int($job[0]) && is_int($job[2])) {
                $done[$monkey] = match ($job[1]) {
                    '+' => $job[0] + $job[2],
                    '*' => $job[0] * $job[2],
                    '-' => $job[0] - $job[2],
                    '/' => $job[0] / $job[2],
                };
            } else {
                $queue->push([$monkey, $job]);
            }
            if ($done->hasKey('root')) return $done['root'];
        }
        return $done['root'];
    }

    public function parse_input(Collection $input)
    {
        return $input->mapWithKeys(fn($m) => [substr($m,0,4) => substr($m, 6)])
                     ->map(fn($i)=>is_numeric($i) ? (int)$i : [substr($i, 0, 4), substr($i, 5, 1), substr($i, 7)]);
    }
 }
