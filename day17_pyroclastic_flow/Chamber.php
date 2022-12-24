<?php namespace day17_pyroclastic_flow;

use Ds\Map;

class Chamber
{
    public array $rocks = [[0,0,0,30],[0,8,28,8],[0,4,4,28],[16,16,16,16],[0,0,24,24]];
    public array $sizes = [1,3,3,4,2];
    public int $rock_height = -1;       // height of the rock stack
    public int $rock = 0;               // index of current rock
    public array $chamber = [0];        // the chamber
    public int $jetstream_size = 0;     // jetstream size
    public int $jet = 0;                // jetstream index
    public const ROCKS = 5;
    public const PRINT = 15;
    public const KEYSIZE = 7;           // minimum key size necessary to have a unique fit

    public function __construct(public array $jetstream) {
        $this->jetstream_size = count($this->jetstream);
    }

    public function drop_rocks(int $count) : void
    {
        for($i=0; $i<$count;$i++) $this->drop_rock();
    }

    public function avalanche(int $count) : int
    {
        $cache = new Map();

        /* make sure we drop some rocks, so we match the key size */
        $this->drop_rocks(self::KEYSIZE-1);

        while(true) {
            $this->drop_rock();
            $key = $this->key();

            if (isset($cache[$key])) {
                [$cache_rock, $cache_rock_height] = $cache[$key];

                /* height at detection of repeat */
                $current_height = $this->rock_height;

                /* number of total drops still to do at start of detected repeat */
                $drop_count = $count - $cache_rock;

                /* numbers of rocks dropped during the repeat interval */
                $delta_rocks = $this->rock - $cache_rock;

                /* number of times this repeat can fit in the remaining drop count */
                $number_of_repeats = floor($drop_count / $delta_rocks);

                /* number of total rocks dropped at end of all repeats */
                $drop_count -= ($delta_rocks * $number_of_repeats);

                /* height at the end of all the repeated intervals */
                $total_height = $cache_rock_height +
                                (($current_height - $cache_rock_height) * $number_of_repeats);


                /* now drop some more rocks */
                for($i=0; $i<$drop_count; $i++) $this->drop_rock();

                /* how much height did we add in these last drops */
                $added_height = $this->rock_height - $current_height;

                /* return the total height at the end */
                return $total_height + $added_height;
            }
            $cache[$key] = [$this->rock, $this->rock_height];
        }
    }

    public function drop_rock() : void
    {
        $rock  = $this->next_rock();
        $rock_pos = $this->rock_height+4;
        $this->expand_chamber();
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
        $rock_top = $rock_pos-1 + $this->sizes[$this->rock % self::ROCKS];
        if ($this->rock_height < $rock_top) $this->rock_height = $rock_top;
        $this->rock++;
    }

    public function next_rock() : array
    {
        return $this->rocks[$this->rock % self::ROCKS];
    }

    public function expand_chamber() : void
    {
        for($i=$this->rock_height+1; $i<=$this->rock_height+7; $i++) {
            if (!isset($this->chamber[$i])) $this->chamber[$i] = 0;
        }
    }

    public function key(): string
    {
        $key = array_slice($this->chamber, $this->rock_height - (self::KEYSIZE - 1), self::KEYSIZE);
        $key[] = $this->jet % $this->jetstream_size;
        $key[] = $this->rock % self::ROCKS;
        return implode('_', $key);
    }

}

