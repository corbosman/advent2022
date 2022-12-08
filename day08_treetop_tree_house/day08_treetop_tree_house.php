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
        $visible = $this->look_horizontal($trees, $visible, $size, 0, 1);
        $visible = $this->look_horizontal($trees, $visible, $size, $size-1, -1);
        $visible = $this->look_vertical($trees, $visible, $size, 0, 1);
        $visible = $this->look_vertical($trees, $visible, $size, $size-1, -1);
        return $visible;
    }

    public function look_horizontal($trees, $visible, $size, $start_col, $delta)
    {
        for($row=0; $row<$size; $row++) {
            $highest = -1;
            $col = $start_col;
            while(true) {
                if ($col < 0 || $col >= $size) break;
                if ($highest === 9) break;
                $cur = $trees[$row][$col];
                if ($cur > $highest) {
                    $visible[$row][$col] = 1;
                    $highest = $cur;
                }
                $col += $delta;
            }
        }
        return $visible;
    }

    public function look_vertical($trees, $visible, $size, $start_row, $delta)
    {
        for($col=0; $col<$size; $col++) {
            $highest = -1;
            $row = $start_row;
            while(true) {
                if ($row < 0 || $row >= $size) break;
                if ($highest === 9) break;
                $cur = $trees[$row][$col];
                if ($cur > $highest) {
                    $visible[$row][$col] = 1;
                    $highest = $cur;
                }
                $row += $delta;
            }
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

}
