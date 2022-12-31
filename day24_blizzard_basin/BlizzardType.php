<?php namespace day24_blizzard_basin;

enum BlizzardType : string
{
    case BLIZZARD_NORTH = '^';
    case BLIZZARD_WEST  = '<';
    case BLIZZARD_EAST  = '>';
    case BLIZZARD_SOUTH = 'v';
    case WALL = '#';
}
