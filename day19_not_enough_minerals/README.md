First iteration of 19a is pretty slow, 110s but correct answer. 19b couldn't be calculated. We need to reduce the options or think of a different method.

Thoughts as I work through making it faster:

1. never build more robots than we can spend. For example, if a geode robot uses 7 obsidian, we dont need more than 7 obsidian robots
2. Always build a geode robot if we can. There seems to be no benefit to not building a geode robot. Output still valid after this rule. 
3. ~~Always build an obsidian robot if we can. This did not work out, output not valid.~~
4. Time is not necessary for the state. This reduces the state by a factor of 24. 

Now I was able to run 19b, it took 5 minutes.


#### some future ideas

* Hmm, don't we just need to maximize the number of geode robots? In other words, we need to work towards building as many geode robots as we can? Maybe calculate the minimum time needed to build a geode robot and use that to throw away all states that dont reach that.
* But, maybe we dont even need to iterate? If we know the time needed to build a geode robot, can we just calculate the end result?? Explore this! The reason I thought of this was that I noticed some recipes have a geode count of 0. This means we were never ever able to build a single geode robot and have it produce 1 geode in the allotted time. Surely you can precalculate this from the recipe?
* We can probably throw out some state if we know we won't beat a previous state
* Maybe stop counting per minute. Like in Day 16, only think in terms of building robots and the time needed to build the next robot. This will probably be a lot faster but needs a big rewrite.
* There is probably a point where we have too many resources for the rest of the game and we can stop making them.
