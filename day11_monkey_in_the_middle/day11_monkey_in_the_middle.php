<?php namespace day11_monkey_in_the_middle;
use Lib\solver;
use Tightenco\Collect\Support\Collection;

class day11_monkey_in_the_middle extends solver
{

    public function solve() : array
    {
        $this->start_timer();

        $monkeys = $this->parse_input($this->input->filter(fn($i)=>$i!=="")->chunk(6)->map(fn($i)=>$i->values()));

        $monkeys = $this->solve_a($monkeys);
        $top_monkeys = collect($monkeys)->map(fn($i)=>$i->inspected)->sort()->reverse()->take(2)->values();
        $this->solution('11a', $top_monkeys[0] * $top_monkeys[1]);

        return $this->solutions;
    }

    private function solve_a(array $monkeys) : array
    {
        for($i=0; $i<20; $i++) {
            foreach($monkeys as $n => $monkey) {
                foreach($monkey->items as $item) {
                    $monkey->inspected++;

                    $worry = (int)floor(($monkey->operation)($item) / 3);
                    $test = ($worry % $monkey->test === 0) ? 'true' : 'false';
                    $throw_to = $monkey->$test;
                    $monkeys[$throw_to]->items[] = $worry;
                }
                $monkey->items = [];
            }
        }
        return $monkeys;
    }

    private function parse_input($input) : array
    {
        $monkeys = [];
        foreach($input as $m) {
            /* get the items */
            preg_match_all('!\d+!', $m[1], $matches);
            $items = $matches[0];

            /* get the divisible */
            $test = (int)substr($m[3], 21);

            /* true */
            $true = (int)substr($m[4], 29);
            $false = (int)substr($m[5], 30);

            /* get the operation, if we're using old, replace the operation with *2 and ^2 */
            $operation = $m[2][23];
            $val = substr($m[2], 25);

            if ($val === 'old' && $operation === '+') {
                $operation = '*';
                $val = 2;
            }
            if ($val === 'old' && $operation === '*') {
                $operation = '^';
                $val = 2;
            }

            $val = (int)$val;

            $operation = match($operation) {
                '*' => fn($a) => $a * $val,
                '+' => fn($a) => $a + $val,
                '^' => fn($a) => $a ** $val,
            };

            $monkeys[] = new Monkey($items, $operation, $test, $true, $false);
        }
        return $monkeys;
    }
}
