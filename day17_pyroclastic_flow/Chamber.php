<?php namespace day17_pyroclastic_flow;

class Chamber
{
    public array $rocks = [[0,0,0,30],[0,8,28,8],[0,4,4,28],[16,16,16,16],[0,0,24,24]];
    public array $sizes = [1,3,3,4,2];
    public int $rock_height = -1;       // height of the rock stack
    public int $rock = 0;               // index of current rock
    public array $chamber = [0];        // the chamber
    public int $jetstream_size = 0;     // jetstream size
    public int $jet = 0;                // jetstream index

    public function __construct(public array $jetstream) {
        $this->jetstream_size = count($this->jetstream);
    }

    public function drop_rocks(int $count) : void
    {
        for($i=0; $i<$count;$i++) $this->drop_rock();
    }

    public function drop_rock() : void
    {
        $rock  = $this->next_rock();
        $rock_pos = $this->rock_height+4;
        $this->expand_chamber();  // grow the chamber
        while(true) {
            $rock = match($this->jetstream[$this->jet++ % $this->jetstream_size]) {
                '<' => $this->left($rock, $rock_pos),
                '>' => $this->right($rock, $rock_pos)
            };
            $new_pos = $this->down($rock, $rock_pos);
            if ($new_pos === $rock_pos) break;
            $rock_pos = $new_pos;
        }
        $this->stack($rock, $rock_pos);
    }

    public function left(array $rock, int $rock_pos) : array
    {
        for($i=0; $i<=3; $i++) {
            $row = $rock[3-$i];
            if (($row & 0b1000000) !== 0) return $rock;
            $row <<= 1;
            if (($row & $this->chamber[$rock_pos+$i]) !== 0) return $rock;
        }

        for($i=0; $i<=3; $i++) $rock[$i] <<= 1;

        return $rock;
    }

    public function right(array $rock, int $rock_pos) : array
    {
        for($i=0; $i<=3; $i++) {
            $row = $rock[3-$i];
            if (($row & 0b0000001) !== 0) return $rock;
            $row >>= 1;
            if (($row & $this->chamber[$rock_pos+$i]) !== 0) return $rock;
        }

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

    public function stack(array $rock, $rock_pos) : void
    {
        for($i=0; $i<=3; $i++) $this->chamber[$rock_pos+3-$i] |= $rock[$i];
        $rock_top = $rock_pos-1 + $this->sizes[$this->rock % 5];
        if ($this->rock_height < $rock_top) $this->rock_height = $rock_top;
        $this->rock++;
    }

    public function next_rock() : array
    {
        $rock = $this->rock % 5;
        return $this->rocks[$rock];
    }

    public function expand_chamber() : void
    {
        for($i=$this->rock_height+1; $i<=$this->rock_height+7; $i++) $this->chamber[] = 0;
    }

    public function print_chamber(array $rock = [], int $rock_pos = -1) : void
    {
        $chamber = $this->chamber;

        if ($rock_pos !== -1) {
            for($i=0; $i<=3; $i++) {
                $chamber[$rock_pos+$i] |= $rock[3-$i];
            }
        }
        for($i=$this->rock_height+7; $i>=0; $i--) {
            $this->print_row($chamber[$i]);
            if ($i< $this->rock_height - 15) break;
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

