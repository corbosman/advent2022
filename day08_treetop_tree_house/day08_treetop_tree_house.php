<?php namespace day08_treetop_tree_house;
use Lib\solver;

class day08_treetop_tree_house extends solver
{
    public function solve() : array
    {
        $this->start_timer();

        $trees = $this->input->map(fn($m)=>str_split($m));
        $size  = count($trees[0]);

        $visible = $this->solve_a($trees, $size);
        $this->solution('8a', array_sum(array_map(fn($t)=>array_sum($t), $visible)));

        $score = $this->solve_b($trees, $size);
        $this->solution('8b', max(array_map(fn($t)=>max($t), $score)));

        return $this->solutions;
    }

    public function solve_a($trees, $size) : array
    {
        $visible = array_fill(0, $size, array_fill(0, $size, 0));

        for($i=0; $i<$size; $i++) {
            foreach([[$i,0,0,1],[$i,$size-1,0,-1],[0,$i,1,0],[$size-1,$i,-1,0]] as [$x, $y, $dx, $dy]) {
                $visible = $this->line_of_sight($trees, $visible, $size, $x, $y, $dx, $dy);
            }
        }
        return $visible;
    }

    public function line_of_sight($trees, $visible, $size, $row, $col, $dx, $dy) : array
    {
        $highest = -1;
        for($i=0; $i<$size; $i++) {
            if ($highest === 9) break;
            $cur = $trees[$row][$col];
            if ($cur > $highest) {
                $visible[$row][$col] = 1;
                $highest = $cur;
            }
            $row+=$dx;
            $col+=$dy;
        }
        return $visible;
    }

    public function solve_b($trees, $size) : array
    {
        $score = array_fill(0, $size, array_fill(0, $size, 1));

        for($row=1; $row<$size-1; $row++)
            for($col=1; $col<$size-1; $col++)
                foreach([[0,-1],[0,1],[-1,0],[1,0]] as [$dx, $dy])
                    $score[$row][$col] *= $this->scenic_score($trees, $row, $col, $size, $dx, $dy);
        return $score;
    }

    public function scenic_score($trees, $row, $col, $size, $dx, $dy) : int
    {
        $score = 0;
        $highest = $trees[$row][$col];
        while(true) {
            $row+=$dx;
            $col+=$dy;
            if ($row < 0 || $col < 0 || $row >= $size || $col >= $size) break;
            $score++;
            if ($trees[$row][$col] >= $highest) break;
        }
        return $score;
    }

    public function print_tree($tree, $size)
    {
        for($i=0; $i<$size; $i++) {
            for($j=0; $j<$size; $j++) {
                echo($tree[$i][$j]) . " ";
            }
            echo "\n";
        }
    }

}
