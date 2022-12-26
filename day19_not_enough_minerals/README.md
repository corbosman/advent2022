First iteration of 19a is pretty slow, 110s but correct answer. 

Thoughts as I work through making it faster:

1. never build more robots than we can spend. For example, if a geode robot uses 7 obsidian, we dont need more than 7 obsidian robots
2. Always build a geode robot if we can. There seems to be no benefit to not building a geode robot. Output still valid after this rule. 
3. ~~Always build an obsidian robot if we can. This did not work out, output not valid.~~
4. Hmm, don't we just need to maximize the number of geode robots? In other words, we need to work towards building as many geode robots as we can? Maybe calculate the minimum time needed to build a geode robot and use that to throw away all states that dont reach that.
5. But, maybe we dont even need to iterate? If we know the time needed to build a geode robot, can we just calculate the end result?? Explore this! The reason I thought of this was that I noticed some recipes have a geode count of 0. This means we were never ever able to build a single geode robot and have it produce 1 geode in the allotted time. 
