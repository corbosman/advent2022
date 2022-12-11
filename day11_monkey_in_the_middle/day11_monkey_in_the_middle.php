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
        $top_monkeys = collect($monkeys)->map(fn($i)=>$i->inspected)->sort()->reverse()->take(2)->values();
        $this->solution('11a', $top_monkeys[0] * $top_monkeys[1]);

        $monkeys = $this->keep_away($monkeys_b, 10000, 'b');
        $top_monkeys = collect($monkeys)->map(fn($i)=>$i->inspected)->sort()->reverse()->take(2)->values();
        $this->solution('11b', $top_monkeys[0] * $top_monkeys[1]);

        return $this->solutions;
    }

    private function keep_away(array $monkeys, int $rounds) : array
    {
        for($i=0; $i<$rounds; $i++) {
            foreach($monkeys as $monkey) {
                foreach($monkey->items as $item) {
                    $monkey->inspected++;

                    /* perform the operation, but divide by the LCM */
                    $item = ($monkey->operation)($item) % $monkey->lcm;

                    /* only in part A do we divide the result, just abuse the rounds */
                    if ($rounds === 20) $item = (int)floor($item / 3);

                    /* now perform the actual test */
                    $mod = ($item % $monkey->mod === 0) ? 'true' : 'false';
                    $throw_to = $monkey->$mod;

                    $monkeys[$throw_to]->items[] = $item;
                }
                $monkey->items = [];
            }
        }
        return $monkeys;
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

        return $monkeys;
    }
}
