<?php

namespace day11_monkey_in_the_middle;

use Closure;

class Monkey
{
    public function __construct(
        public array $items,
        public Closure $operation,
        public int $test,
        public int $true,
        public int $false,
        public int $inspected = 0) {}
}
