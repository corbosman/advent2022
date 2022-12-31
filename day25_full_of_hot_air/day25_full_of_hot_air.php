<?php namespace day25_full_of_hot_air;
use Lib\solver;
use Tightenco\Collect\Support\Collection;

class day25_full_of_hot_air extends solver
{
    public function solve() : array
    {
        $this->start_timer();

        $digits = $this->parse_input($this->input)
                       ->map(fn($f)=>$f->reverse()->values())
                       ->map(fn($f) => $f->reduce(fn($c, $i, $k) => $c + (match($i) {
                            '='=>-2, '-'=>-1, '0'=>0, '1'=>1, '2'=>2} * (5 ** $k)),0))
                       ->sum();

        $this->solution('25a', $this->digits_to_snafu($digits));

        return $this->solutions;
    }

    public function digits_to_snafu(int $digits) : string
    {
        if ($digits < 1) return '';
        $digits+=2;
        $next_digit = floor($digits / 5);
        $snafu_char = ($digits % 5) - 2;
        return $this->digits_to_snafu($next_digit) . match ($snafu_char) { -2=>'=', -1=>'-', 0=>0, 1=>1, 2=>2 };
    }

    public function parse_input(Collection $input) : Collection
    {
        return $input->map(fn($i)=>collect(str_split($i)));
    }
 }
