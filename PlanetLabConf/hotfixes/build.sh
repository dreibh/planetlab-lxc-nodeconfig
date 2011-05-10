#!/bin/bash

# Look for all directories in the PlanetLabConf/hotfixes directory and 
# create tar files for hotfixes on nodegroups with the same name.

for dir in `find . -maxdepth 1 -type d  | grep -vE "^.$"` ; do 
    b=$dir.tar
    d=$dir
    tar -C $d --create --file $b .
done
