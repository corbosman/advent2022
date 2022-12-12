<?php namespace day11_monkey_in_the_middle;
use Lib\solver;
use Tightenco\Collect\Support\Collection;

class day11_monkey_in_the_middle extends solver
{

    public function solve() : array
    {
        $this->start_timer();

        $monkeys_a = $this->parse_input($this->input->filter(fn($i)=>$i!=="")->chunk(6)->map(fn($i)=>$i->values()));
        $monkeys_b = array_map(fn($i)=>clone($i), $monkeys_a);

        $monkeys = $this->keep_away($monkeys_a, 20);
        $this->solution('11a', $monkeys[0] * $monkeys[1]);

        $monkeys = $this->keep_away($monkeys_b, 10000);
        $this->solution('11b', $monkeys[0] * $monkeys[1]);

        return $this->solutions;
    }

    private function keep_away(array $monkeys, int $rounds) : array
    {
        for($i=0; $i<$rounds; $i++) {
            foreach($monkeys as $monkey) {
                foreach($monkey->items as $item) {
                    $monkey->inspected++;

                    /* perform the operation */
                    $item = ($monkey->operation)($item);

                    /* only in part A do we divide the result, just abuse the rounds */
                    if ($rounds === 20) $item = (int)floor($item / 3);

                    $item %= $monkey->lcm;

                    /* now perform the actual test */
                    $test = ($item % $monkey->mod === 0) ? 'true' : 'false';
                    $throw_to = $monkey->$test;

                    /* throw the item to the other monkey */
                    $monkeys[$throw_to]->items[] = $item;
                }
                $monkey->items = [];
            }
        }

        return collect($monkeys)->map(fn($i)=>$i->inspected)->sort()->reverse()->values()->toArray();
    }

    private function parse_input($input) : array
    {
        $monkeys = [];
        $lcm = 1;
        foreach($input as $m) {
            /* get the items */
            preg_match_all('!\d+!', $m[1], $matches);
            $items = $matches[0];

            /* get the divisible */
            $mod = (int)substr($m[3], 21);

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

            $monkeys[] = new Monkey($items, $operation, $mod, 1, $true, $false);
            $lcm = gmp_lcm($lcm, $mod);
        }

        /* assign the LCM to each monkey */
        foreach($monkeys as $monkey) {
            $monkey->lcm = (int)$lcm;
        }

        output($lcm);

        return $monkeys;
    }
}
