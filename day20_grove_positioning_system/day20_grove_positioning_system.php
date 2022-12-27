<?php namespace day20_grove_positioning_system;
use Lib\solver;
use teewurst\Prs4AdvancedWildcardComposer\Di\FactoryInterface;
use teewurst\Prs4AdvancedWildcardComposer\tests\Unit\Di\InvokableFactoryTest;
use Tightenco\Collect\Support\Collection;

class day20_grove_positioning_system extends solver
{
    public const KEY = 811589153;

    public function solve() : array
    {
        $this->start_timer();

        $this->solution('20a', $this->part1($this->input));
        $this->solution('20b', $this->part2($this->input));

        return $this->solutions;
    }

    public function part1(Collection $input)
    {
        $encrypted = $input->map(fn($v, $k)=>new Number($k, $v));
        $decrypted = $input->map(fn($v, $k)=>new Number($k, $v));

        return $this->coordinates($this->mix($encrypted, $decrypted));
    }

    public function part2(Collection $input) : int
    {
        $encrypted = $input->map(fn($v, $k)=>new Number($k, $v * self::KEY));
        $decrypted = $input->map(fn($v, $k)=>new Number($k, $v * self::KEY));

        for($i=0; $i<10; $i++) $decrypted = $this->mix($encrypted, $decrypted);

        return $this->coordinates($decrypted);
    }

    public function mix(Collection $encrypted, Collection $decrypted) : Collection
    {
        $size = $encrypted->count() - 1;

        foreach($encrypted as $n) {
            if ($n->value === 0) continue;

            /* find this element and remove it */
            $position =  $decrypted->search($n);
            $decrypted->splice($position, 1);

            /* place it in the new spot */
            $new_position = (($position + $n->value) + $size) % $size;   // php quirkiness, you can get negative modulo
            $decrypted->splice($new_position, 0, [$n]);
        }
        return $decrypted;
    }

    public function coordinates(Collection $decrypted) : int
    {
        $size = $decrypted->count();
        $zero_index = $decrypted->search(fn($s)=>$s->value === 0);
        return $decrypted[($zero_index+1000)%$size]->value + $decrypted[($zero_index+2000)%$size]->value + $decrypted[($zero_index+3000)%$size]->value;
    }
}

