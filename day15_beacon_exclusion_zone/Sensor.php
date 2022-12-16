<?php

namespace day15_beacon_exclusion_zone;

class Sensor extends Point
{
    public Beacon $beacon;
    public int $beacon_distance;
    public array $close = [];

    public function __construct($x, $y, Beacon $beacon)
    {
        $this->beacon = $beacon;
        parent::__construct($x, $y);
        $this->beacon_distance = $this->distance($beacon);
    }
}
