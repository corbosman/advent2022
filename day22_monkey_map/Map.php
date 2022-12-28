<?php namespace day22_monkey_map;

class Map
{
    public const STEPS = [[1,0],[0,1],[-1,0],[0,-1]];
    public int $step_index = 0;
    public int $height;
    public int $width;
    public array $crumbs = [];

    public function __construct(
        public array $map,
        public array $path,
        public int $x,
        public int $y
    ) {
        $this->width  = count($this->map[0]);
        $this->height = count($this->map);
    }

    public function walk() : array
    {
        foreach($this->path as $i => $p) {
            match($p) {
                'L' => $this->turn_left(),
                'R' => $this->turn_right(),
                default => $this->forward($p)
            };
        }
        $this->print($this->map);
        return [$this->x, $this->y, $this->step_index];
    }

    public function forward(int $steps) : void
    {
        for($i=0; $i<$steps; $i++) {
            [$nx, $ny] = $this->step($this->x, $this->y);

            if ($this->map[$ny][$nx] === ' ') [$nx, $ny] = $this->portal($nx, $ny);

            switch($this->map[$ny][$nx]) {
                case '#':
                    break 2;
                case '.':
                    [$this->x, $this->y] = [$nx, $ny];
                    break;
                default:
                    die("should not happen!");
            }
            $this->crumbs[] = [$this->x, $this->y, $this->step_index];
        }
    }

    /* move from one edge to another edge */
    public function portal(int $x, int $y) : array
    {
        [$nx, $ny] = [$x, $y];
        while (true) {
            [$nx, $ny] = $this->step($nx, $ny);
            if ($this->map[$ny][$nx] !== ' ') break;
            $this->crumbs[] = [$nx, $ny, $this->step_index];
        }
        return [$nx, $ny];
    }

    public function turn_left() : void
    {
        $this->step_index = ($this->step_index - 1 + 4) % 4;
    }

    public function turn_right() : void
    {
        $this->step_index = ($this->step_index + 1 + 4) % 4;
    }

    /* take one step taking into account the direction we're facing */
    public function step(int $x, int $y) : array
    {
        return [($x + self::STEPS[$this->step_index][0] + $this->width) % $this->width,
                ($y + self::STEPS[$this->step_index][1] + $this->height) % $this->height];
    }

    public function print(array $map) : void
    {
        foreach($this->crumbs as [$x, $y, $step_index]) {
            $map[$y][$x] = match($step_index) {
                0 => '>', 1 => 'v', 2 => '<', 3 => '^', default => '@'
            };
        }
        print_grid($map,1);
    }
}
