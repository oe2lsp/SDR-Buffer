#!/bin/bash
#
#
# clean old buffered SDR data
#
# dependencies: 
#   bc
#


dir80m="/opt/save/www/80m"

#tree -fitra --noreport -L 2  $parent | tail -n+2160 | xargs -I '{}' rm {}
#| xargs -I '{}' rm $parent/{}
function clean_old {
	parent=$1
	delay=$2
	cd $parent
	a=$(pwd)


	if [ $parent == $a ]; then
		echo "correct directory"
	else
		echo "wrong directory"
		exit
	fi
	#find . -type f -mmin +1440 | xargs -I '{}' rm -r  {} 
	find . -mindepth 1 -maxdepth 1 -mmin +$delay | xargs -I '{}' rm -r  {} 
	find . -mindepth 1 -maxdepth 1 -mmin +$delay  
}

keep=$(echo "60*24*14" | bc)
clean_old $dir80m $keep

