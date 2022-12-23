<?php namespace day17_pyroclastic_flow;

class Chamber
{
    public array $rocks = [[0,0,0,30],[0,8,28,8],[0,4,4,28],[16,16,16,16],[0,0,24,24]];
    public array $chamber = [0];
    public int $chamber_height = 0;
    public int $jetstream_size = 0;
    public int $rock = 0;
    public int $jet = 0;

    public function __construct(public array $jetstream) {
        $this->jetstream_size = count($this->jetstream);
    }

    public function drop_rocks(int $count) : void
    {
        for($i=0; $i<$count;$i++) $this->drop_rock();
    }

    public function drop_rock() : void
    {
        $rock  = $this->next_rock();     // next rock
        $height = $this->height();       // get the highest rock position
        $rock_pos = $height+4;           // start 4 higher
        $this->expand_chamber($height);  // grow the chamber

        /* loop until we hit something */
        while(true) {
            $rock = match($this->jetstream[$this->jet++ % $this->jetstream_size]) {
                '<' => $this->left($rock, $rock_pos),
                '>' => $this->right($rock, $rock_pos)
            };
            $new_pos = $this->down($rock, $rock_pos);
            if ($new_pos === $rock_pos) break;
            $rock_pos = $new_pos;
        }

        $this->land($rock, $rock_pos);
    }

    public function left(array $rock, int $rock_pos) : array
    {
        for($i=0; $i<=3; $i++) {
            $row = $rock[3-$i];
            if (($row & 0b1000000) !== 0) return $rock;   // left wall
            $row <<= 1;
            if (($row & $this->chamber[$rock_pos+$i]) !== 0) return $rock;  // collision
        }

        /* we can move ! */
        for($i=0; $i<=3; $i++) $rock[$i] <<= 1;

        return $rock;
    }

    public function right(array $rock, int $rock_pos) : array
    {
        for($i=0; $i<=3; $i++) {
            $row = $rock[3-$i];
            if (($row & 0b0000001) !== 0) return $rock;  // right wall
            $row >>= 1;
            if (($row & $this->chamber[$rock_pos+$i]) !== 0) return $rock; // collision
        }

        /* we can move ! */
        for($i=0; $i<=3; $i++) $rock[$i] >>= 1;

        return $rock;
    }

    public function down(array $rock, int $rock_pos) : int | bool
    {
        if ($rock_pos === 0) return $rock_pos;
        $rock_pos--;
        for($i=0; $i<=3; $i++) {
           if (($rock[3-$i] & $this->chamber[$rock_pos+$i]) !== 0) return $rock_pos+1; // collision
        }
        return $rock_pos;
    }

    public function land(array $rock, $rock_pos) : void
    {
        for($i=0; $i<=3; $i++) {
            $r = $this->chamber[$rock_pos+3-$i] | $rock[$i];
            $this->chamber[$rock_pos+3-$i] = $r;
        }
    }

    /* return the floor of the chamber (highest rock position) */
    public function height() : int
    {
        $height = $this->chamber_height;
        while ($height > -1 && $this->chamber[$height] === 0) $height--;
        return $height;
    }

    public function next_rock() : array
    {
        $rock = $this->rock++ % 5;
        return $this->rocks[$rock];
    }

    public function expand_chamber(int $floor) : void
    {
        while($this->chamber_height <= $floor + 7) {
            ++$this->chamber_height;
            $this->chamber[] = 0;
        }
    }

    public function print_chamber(array $rock = [], int $rock_pos = -1) : void
    {
        $chamber = $this->chamber;

        if ($rock_pos !== -1) {
            for($i=0; $i<=3; $i++) {
                $chamber[$rock_pos+$i] |= $rock[3-$i];
            }
        }
        for($i=$this->chamber_height-1; $i>=0; $i--) {
            $this->print_row($chamber[$i]);
            if ($i< $this->chamber_height - 15) break;
        }
        echo "\n";
    }

    public function print_rock(array $rock) : void
    {
        for($i=0; $i<=3; $i++) $this->print_row($rock[$i]);
    }

    public function print_row(int $row) : void
    {
        $mask = 0b1000000;
        for ($i=0; $i<7; $i++) {
            echo(($row & $mask) !== 0 ? '#' : '.');
            $mask >>= 1;
        }
        echo "\n";
    }

}

