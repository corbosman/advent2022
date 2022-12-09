<?php namespace day09_rope_bridge;

class Rope
{
    public array $knots = [];

    public function __construct(int $x, int $y, int $knots = 1)
    {
        for($i=0; $i<$knots; $i++) {
            $this->knots[$i] = new Knot($x, $y);
        }
    }

    /*
     * move the head of the rope a single position into any direction
     */
    public function move(string $d) : void
    {
        /* transform the direction into deltas */
        match ($d) {
            'R' => $this->head()->move(1,0),
            'L' => $this->head()->move(-1,0),
            'U' => $this->head()->move(0,1),
            'D' => $this->head()->move(0,-1)
        };

        /* now move the tail */
        $this->move_knots();
    }

    /**
     *  Move all the remaining knots with the head, each knot acts as a head for the knot before it
     */
    public function move_knots() : void
    {
        $n = count($this->knots);

        for($k=1; $k<$n; $k++) {
            $head = $this->knots[$k-1];
            $tail = $this->knots[$k];

            /* if the segment is still adjacent to the previous segment, stop all moves from here on out as the tail cant continue moving */
            if (abs($head->x - $tail->x)<= 1 && abs($head->y - $tail->y) <= 1) {
                break;
            }

            /* find the delta for the move towards the knot before it, aka the new head */
            [$dx, $dy] = $this->move_toward($head, $tail);

            /* now move this knot */
            $this->knots[$k]->move($dx, $dy);
        }

    }

    /* move a tail towards the head according to the rules */
    public function move_toward(Knot $head, Knot $tail) : array
    {
        /* the delta to the head can be 0,1,2.  If it's 2, it means we want to move 1 diagonally */
        $dx = match($head->x - $tail->x) { 0=>0, -1,-2=>-1, 1,2=>1 };
        $dy = match($head->y - $tail->y) { 0=>0, -1,-2=>-1, 1,2=>1 };

        return [$dx, $dy];
    }

    public function head() : Knot
    {
        return $this->knots[0];
    }

    public function tail() : Knot
    {
        return end($this->knots);
    }
}
