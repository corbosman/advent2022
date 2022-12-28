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
        $this->solution('21a', $this->yell($monkeys, 'root'));
        $this->solution('21b', $this->part2($monkeys));

        return $this->solutions;
    }

    public function part2(Collection $monkeys) : int
    {
        /* one of these two values is correct, the other is wrong because humn is wrong */
        [$left_name, $_, $right_name] = $monkeys['root'];
        $left_num  = $this->yell($monkeys, $left_name);
        $right_num = $this->yell($monkeys, $right_name);

        /* find the path to humn */
        $path = $this->find_lucy($monkeys, 'root', collect())->reverse()->values();

        /* the correct value is the one that's not towards humn */
        $value = ($path[1] === $left_name) ? $right_num : $left_num;

        /* now backtrace the calculation */
        return $this->find_human_number($monkeys, $path, $value, 1);
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

    public function find_human_number(Collection $monkeys, Collection $path, int $num, $index) : int
    {
        if ($path[$index] === "humn") return $num;

        [$left_name, $operation, $right_name] = $monkeys[$path[$index]];
        $left_num  = $this->yell($monkeys, $left_name);
        $right_num = $this->yell($monkeys, $right_name);

        if ($left_name === $path[$index+1]) {
            $next_num = match($operation) {
                '+' => $num - $right_num,
                '-' => $num + $right_num,
                '*' => intdiv($num, $right_num),
                '/' => ($num ** 1) * $right_num,
            };
        } else {
            $next_num = match($operation) {
                '+' => $num - $left_num,
                '-' => $left_num - $num,
                '*' => intdiv($num, $left_num),
                '/' => ($num ** 1) * $left_num,
            };
        }
        return $this->find_human_number($monkeys, $path, $next_num, $index+1);
    }

    public function yell(Collection $monkeys, string $name) : int
    {
        $job = $monkeys[$name];

        if (is_int($job)) return $job;

        [$left_name, $operation, $right_name] = $job;
        $left_num  = $this->yell($monkeys, $left_name);
        $right_num = $this->yell($monkeys, $right_name);

        return match($operation) {
            '+' => $left_num + $right_num,
            '*' => $left_num * $right_num,
            '-' => $left_num - $right_num,
            '/' => $left_num / $right_num,
        };
    }

    public function parse_input(Collection $input)
    {
        return $input->mapWithKeys(fn($m) => [substr($m,0,4) => substr($m, 6)])
                     ->map(fn($i)=>is_numeric($i) ? (int)$i : [substr($i, 0, 4), substr($i, 5, 1), substr($i, 7)]);
    }
 }
