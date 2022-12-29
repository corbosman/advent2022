<?php namespace day22_monkey_map;

class Cube extends Map
{
    public function __construct($map, $path, $x, $y)
    {
        parent::__construct($map, $path, $x, $y);
    }

    /* we are on an edge, portal to its cube neighbor */
    public function portal(int $nx, int $ny) : array
    {
        $face = $this->get_face($this->x, $this->y);
        $new_face = $this->get_face($nx, $ny);

        return $this->teleport($nx, $ny, $face, $new_face);
    }

    /* conversions between areas. These are not based on the cube but on the grid */
    public function teleport($nx, $ny, int $face, int $new_face) : array
    {
        return match([$face, $new_face]) {

            [1,0]  => [0,              149 - $this->y,   0],
            [1,10] => [0,              $this->x + 100,   0],
            [2,11] => [$this->x - 100, 199,              3],
            [2,0]  => [99,             149 - $this->y,   2],
            [2,5]  => [99,             $this->x - 50,    2],
            [4,3]  => [$this->y -  50, 100,              1],
            [4,5]  => [$this->y +  50, 49,               3],
            [6,3]  => [50,             $this->x + 50,    0],
            [6,8]  => [50,             149 - $this->y,   0],
            [7,10] => [49,             $this->x + 100,   2],
            [7,8]  => [149,            149 - $this->y,   2],
            [9,11] => [$this->y - 100, 0,                1],
            [9,0]  => [$this->x + 100, 0,                1],
            [9,10] => [$this->y - 100, 149,              3],

            default => die("should not happen x={$this->x}, y={$this->y} nx={$nx} ny={$ny} face={$face} new_face={$new_face}")
        };
    }

    /* return a face number based on its area
     *    0  1  2
     *    3  4  5
     *    6  7  8
     *    9 10 11
    */
    public function get_face(int $x, int $y) : int
    {
        return floor($y / 50) * 3 + floor($x / 50);
    }
}
