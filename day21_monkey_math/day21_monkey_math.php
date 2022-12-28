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
        $this->solution('21b', $this->part2($monkeys));

        return $this->solutions;
    }

    public function part1(Collection $monkeys) : int
    {
        [$left, $right] = $this->solve_root($monkeys, 'root');
        return $left+$right;
    }

    public function part2(Collection $monkeys) : int
    {
        /* one of these two values is correct, the other is wrong because humn is wrong */
        [$left_num, $right_num] = $this->solve_root($monkeys, 'root');
        [$left_monkey, $_, $right_monkey] = $monkeys['root'];

        /* find the path to humn */
        $path = $this->find_lucy($monkeys, 'root', collect())->reverse()->values();

        /* the correct value is the one that's not towards humn */
        $value = ($path[1] === $left_monkey) ? $right_num : $left_num;

        /* now backtrace the calculation */
        return $this->trace_human($monkeys, $path, $value, 1);
    }

    public function find_lucy(Collection $monkeys, string $search, Collection $path) : ?Collection
    {
        if ($search === 'humn') return $path->push($search);

        if (is_array($monkeys[$search])) {
            [$left,,$right] = $monkeys[$search];
            if (($this->find_lucy($monkeys, $left, $path)) !== null)  return $path->push($search);
            if (($this->find_lucy($monkeys, $right, $path)) !== null) return $path->push($search);
        }
        return null;
    }

    public function trace_human(Collection $monkeys, Collection $path, int $root_val, $index) : int
    {
        if ($path[$index] === "humn") return $root_val;

        [$left, $operation, $right] = $monkey = $monkeys[$path[$index]];
        [$left_val, $right_val] = $this->solve_root($monkeys, $path[$index]);

        if ($left === $path[$index+1]) {
            $new_value = match($operation) {
                '+' => $root_val - $right_val,
                '-' => $root_val + $right_val,
                '*' => intdiv($root_val, $right_val),
                '/' => ($root_val ** 1) * $right_val,
            };
        } else {
            $new_value = match($operation) {
                '+' => $root_val - $left_val,
                '-' => $left_val - $root_val,
                '*' => intdiv($root_val, $left_val),
                '/' => ($root_val ** 1) * $left_val,
            };
        }
        return $this->trace_human($monkeys, $path, $new_value, $index+1);
    }
    public function solve_root(Collection $monkeys, string $root) : array
    {
        $unsolved = new Deque;
        $solved = new Map;

        /* split jobs into solved and unsolved */
        foreach($monkeys as $monkey => $job) {
            if (is_int($job)) $solved->put($monkey, $job);
            else $unsolved->push([$monkey, $job]);
        }

        while($unsolved->count() > 0) {
            [$monkey, [$left, $operation, $right]] = $unsolved->shift();

            /* try to solve either side */
            if (!is_int($left)  && $solved->hasKey($left))  $left = $solved[$left];
            if (!is_int($right) && $solved->hasKey($right)) $right = $solved[$right];

            /* if both our solved, perform operation and move to solved */
            if (is_int($left) && is_int($right)) {
                $solved[$monkey] = match ($operation) {
                    '+' => $left + $right,
                    '*' => $left * $right,
                    '-' => $left - $right,
                    '/' => $left / $right,
                };
                if ($monkey === $root) return [$left, $right];
            } else {
                $unsolved->push([$monkey, [$left, $operation, $right]]);
            }
        }
        die("should not happen");
    }

    public function parse_input(Collection $input)
    {
        return $input->mapWithKeys(fn($m) => [substr($m,0,4) => substr($m, 6)])
                     ->map(fn($i)=>is_numeric($i) ? (int)$i : [substr($i, 0, 4), substr($i, 5, 1), substr($i, 7)]);
    }
 }
