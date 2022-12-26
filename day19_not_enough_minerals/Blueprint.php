<?php namespace day19_not_enough_minerals;

class Blueprint
{
    public int $max_ore_cost;
    public int $max_clay_cost;
    public int $max_obsidian_cost;

    public function __construct(
        public int $ore_robot_ore,
        public int $clay_robot_ore,
        public int $obsidian_robot_ore,
        public int $obsidian_robot_clay,
        public int $geode_robot_ore,
        public int $geode_robot_obsidian)
    {
        $this->max_ore_cost = max($this->ore_robot_ore, $this->clay_robot_ore, $this->obsidian_robot_ore,  $this->geode_robot_ore);
        $this->max_clay_cost = $this->obsidian_robot_clay;
        $this->max_obsidian_cost = $this->geode_robot_obsidian;
    }
}
