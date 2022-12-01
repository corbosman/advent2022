#!/usr/bin/env php
<?php
require __DIR__ . '/../../vendor/autoload.php';
$start = microtime(true);

$elves = collect(input('inputs/input.txt'))
    ->chunkWhile(fn ($value) => $value !== "")
    ->map(fn ($elf) => $elf->filter(fn($cal) => $cal !== ""))
    ->map(fn ($elf) => $elf->sum());

solution($elves->max(), $start, microtime(true), '1a');
solution($elves->sort()->reverse()->take(3)->sum(), $start, microtime(true), '1b');
