#!/bin/bash
#
# calculate waterfall for buffered sample files
#   
#
#
#
#
#
#
#
#
#
#


#get date YYYYMMDD

#get all files, for all
#	if not waterfall/<file>.png 
#		malen
band=$1
today=$(date +'%Y%m%d')

bin=/opt/save/waterfall-x64
col=/opt/save/col.txt
dir=/var/www/html/$band/$today
out=$dir/out
out_big=$dir/out_big

if [ ! -d "$out" ]; then
	mkdir $out
fi

if [ ! -d "$out_big" ]; then
	mkdir $out_big
fi


cd $dir

for f in $(find . -maxdepth 1 -type f -mmin +1   | sort -n);
do 
	#check $out
	absf=$out/$f.png
	if [ ! -f "$absf" ]; then #&& $f no dir
		#malen
		#./waterfall-x64 -i /media/spel/daten/DUMP/20200915/1256 -o 400 -w test4.png -x 1600 -y 5 -j 65000 
	        $bin -i $f -o 800 -w $absf -x 1600 -j 65000 -b 25 -c $col 
	fi

	absf=$out_big/$f.png
        if [ ! -f "$absf" ]; then #&& $f no dir
                #maln
                $bin -i $f -o 20 -w $absf -x 1600 -j 65000 -b 25 -c $col
        fi


	echo "Processing $f file.."
done



#php
# get day-folders
# get minute pictures of day
#   print pictures, hours, frequency
# link
#   previous day, next day, minute
# with selected minute start GET-Request (ajax?)
#   to new openwebrx, source as parameter, kill openwebrx if get request not running
#   link to openwebrx in new tab
