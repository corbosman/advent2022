<?php namespace day20_grove_positioning_system;
use Lib\solver;
use Tightenco\Collect\Support\Collection;

class day20_grove_positioning_system extends solver
{
    public function solve() : array
    {
        $this->start_timer();

        $this->solution('20a', $this->part1($this->input));

        return $this->solutions;
    }

    public function part1(Collection $input)
    {
        $encrypted = $input->map(fn($v, $k)=>new Number($k, $v));
        $decrypted = $input->map(fn($v, $k)=>new Number($k, $v));

        $size = $encrypted->count() - 1;
        foreach($encrypted as $n) {
            if ($n->value === 0) continue;

            /* find this element and remove it */
            $position =  $decrypted->search(fn($x)=>$x->id === $n->id);
            $decrypted->splice($position, 1);

            /* place it in the new spot */
            $new_position = (($position + $n->value) + $size) % $size;   // php quirkiness, you can get negative modulo
            $decrypted->splice($new_position, 0, [$n]);
        }

        $zero_index = $decrypted->search(fn($s)=>$s->value === 0);
        $size = $decrypted->count();

        return $decrypted[($zero_index+1000)%$size]->value + $decrypted[($zero_index+2000)%$size]->value + $decrypted[($zero_index+3000)%$size]->value;
    }
}

