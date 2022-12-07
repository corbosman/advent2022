<?php namespace Advent;
use Lib\solver;
use Tightenco\Collect\Support\Collection;

class aoc07_no_space_left_on_device extends solver
{
    public Collection $directories;     // all directories we encounter
    public array $path = [];            // current path while traversing

    public function solve() : array
    {
        $this->start_timer();
        $this->directories = collect();

        /* play back the logfile */
        $this->play($this->input);

        $this->solution('7a', $this->directories->filter(fn($d)=>$d<=100000)->sum());

        $space_needed = 30000000 - (70000000 - $this->directories->first());
        $this->solution('7b', $this->directories->filter(fn($dir) => $dir > $space_needed)->sort()->first());

        return $this->solutions;
    }

    public function play($input) : void
    {
        foreach($input as $line) {
            if ($line[0] === '$') {
                [$cmd, $args]  = $this->get_command($line);
                $this->$cmd($args);
            } else {
                [$size, $filename] = explode(' ', $line);

                if ($size === 'dir') continue;

                foreach($this->path as $dir) {
                    $this->directories[$dir] += (int)$size;
                }
            }
        }
    }

    private function get_command($line) : array
    {
        $cmd = explode(' ', substr($line, 2));
        return isset($cmd[1]) ? [$cmd[0], $cmd[1]] : [$cmd[0], null];
    }

    private function cd($args) : void
    {
        if ($args === '..') {
            array_pop($this->path);
        } else {
            $cwd = end($this->path) . $args . '/';
            $this->path[] = $cwd;
            $this->directories[$cwd] = 0;
        }
    }

    public function ls($args) : void {}
 }
