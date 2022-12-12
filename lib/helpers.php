<?php
use function Termwind\{render};

function output($str) : void
{
    print_r($str);
    print_r("\n");
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

function print_grid(array $grid, $w)
{
    $width = count($grid[0]);
    $height = count($grid);

    for($i=0; $i<$height; $i++) {
        for($j=0; $j<$width; $j++) {
            echo sprintf("%{$w}s ", $grid[$i][$j]);
        }
        echo "\n";
    }
    echo "\n";
}

