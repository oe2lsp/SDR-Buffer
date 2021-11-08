#!/bin/bash
#
# calculate waterfall for buffered sample files
# 
# check if out folter there otherwise create
# get all files from folder
# check if files in meta file
# if not
#   waterfall   
#   add to meta file
# imagemagic 
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
bw=$2
gain=$3
today=$(date +'%Y%m%d')

bin=/opt/save/waterfall3 #fftw3
col=/opt/save/col.txt
dir=/opt/save/www/$band/$today
out=$dir/out
out_big=$dir/out_big
out_compact=$dir/out_comp
file_day=$out_compact/minutes.txt
png_out=$out_compact/minutes.png


if [ ! -d "$out" ]; then
	mkdir $out
fi

if [ ! -d "$out_big" ]; then
	mkdir $out_big
fi
if [ ! -d "$out_compact" ]; then
       mkdir $out_compact
fi
if [ ! -f "$file_day" ]; then
	touch $file_day
fi


cd $dir


skip=$(echo "scale=0; 120000/5*$bw" | bc)
skip=${skip%.*} # strip comma

#### create png from IQ data ####
for f in $(find . -maxdepth 1 -type f -mmin +1   | sort -n);
do 
	#check $out
	absf=$out/$f.png
	if [ ! -f "$absf" ]; then #&& $f no dir
		#malen
		$bin -i $f -o 800 -C -w $absf -x 1600 -j $skip -b $gain -d -c $col -z 85 
	fi

	absf=$out_big/$f.png
        if [ ! -f "$absf" ]; then #&& $f no dir
                #malen
                $bin -i $f -o 20 -C -w $absf -x 1600 -j $skip -b $gain -d -c $col
        fi


	echo "Processing $f file.."
done


####  combine minute png files to one png -> client performance ####
list_file=$(cat $file_day)
list_dir=$(ls $dir | grep -v out)
min2add=$(diff --changed-group-format='%<%>' --unchanged-group-format='' <(echo "$list_dir") <(echo "$list_file"))

## now loop through the above array
min2png=" "
for i in $min2add
do
	image=$out/$i.png
	if [ -f "$image" ]; then
		min2png="$min2png $out/$i.png"
	# or do whatever with individual element of the array
        echo $i >> $file_day
	fi

done

echo $min2png
echo $png_out

if [ -f "$png_out" ]; then
	convert $png_out $min2png -gravity center -append $png_out
else
	convert $min2png -gravity center -append $png_out
fi


#a=$(ls $dir | grep -v out)
#echo "$a"
#echo $dir

#
# get day-folders
# get minute pictures of day
#   print pictures, hours, frequency
# link
#   previous day, next day, minute
# with selected minute start GET-Request (ajax?)
#   to new openwebrx, source as parameter, kill openwebrx if get request not running
#   link to openwebrx in new tab
