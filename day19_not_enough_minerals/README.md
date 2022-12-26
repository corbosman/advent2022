First iteration of 19a is pretty slow, 110s but correct answer. 

optimizations:

1. never build more robots than we can spend. For example, if a geode robot uses 7 obsidian, we dont need more than 7 obsidian robots
2. Always build a geode robot if we can. There seems to be no benefit to not building a geode robot. Output still valid after this rule. 
3. ~~Always build an obsidian robot if we can. This did not work out, output not valid.~~
