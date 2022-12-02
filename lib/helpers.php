<?php
use function Termwind\{render};

function output($str) : void
{
    print_r($str . "\n");
}

function input($path) : array
{
    return file(realpath(dirname($_SERVER['PHP_SELF'])) . '/' .  $path, FILE_IGNORE_NEW_LINES);
}


function map(callable $callback, array $array, array ...$arrays): array
{
    return array_map($callback, $array, ...$arrays);
}

function sum($array) : int|float
{
    return array_sum($array);
}

function reduce(array $array, callable $callback, mixed $initial) : mixed
{
    return array_reduce($array, $callback, $initial);
}

function reverse(array $array) : array
{
    return array_reverse($array);
}

function merge(...$arrays) : array {
    return array_merge(...$arrays);
}

function filter(array $array, callable $callback, $mode = 0) : array
{
    return array_filter($array, $callback, $mode);
}

function str_diff($str1, $str2) : string
{
    return strlen($str1) > strlen($str2) ?
        implode('', array_values(array_diff(str_split($str1), str_split($str2)))) :
        implode('', array_values(array_diff(str_split($str2), str_split($str1))));
}

function str_uniq($str) : string
{
    return implode('',array_unique(str_split($str)));
}

function render_output($solutions) : void
{
    $total_time = 0;
    $table = <<<HTML
    <div>
        <table>
            <thead>
                <tr>
                    <th class='text-center'>PUZZLE</th>
                    <th class='text-center'>TITLE</th>
                    <th class='text-center'>ANSWER</th>
                    <th class='text-center'>RUNTIME</th>
                </tr>
            </thead>
    HTML;

    foreach($solutions->all() as $solution) {
        [$puzzle, $title, $value, $time] = $solution;
        $total_time += $time;
        $time = round(($time)*1000,3);

        $table .= <<<HTML
        <tr class='text-center'>
          <td class='text-center text-yellow-400'>{$puzzle}</td>
          <td class='text-center text-yellow-400'>{$title}</td>
          <td class='text-center text-green-400'>{$value}</td>
          <td class='text-center text-blue-400'>{$time} ms</td>
        </tr>
        HTML;
    }

    $total_time = round(($total_time)*1000,3);

    $table .= <<<HTML
        <tr>
                          <td class='text-center text-yellow-400'></td>
                        <td class='text-center text-yellow-400'></td>
                        <td class='text-center text-green-400'></td>
                        <td class='text-center text-blue-400'>---------</td>
        </tr>
        <tr>
                          <td class='text-center text-yellow-400'></td>
                        <td class='text-center text-yellow-400'></td>
                        <td class='text-center text-green-400'></td>
                        <td class='text-center text-blue-400'>{$total_time} ms</td>
        </tr>
        </table>
    </div>
    HTML;

    render($table);

}

