#!/bin/bash
base_dir="/opt/save"

check_file ()
{
  band=$1	
  #check if file exists, if not write warning
  #check if file is older than get pid and kill.sh pid
  file=/tmp/$band
  #echo $file
  if [ -f "$file" ]; then
    #echo "file there" 
    if [ `stat --format=%Y $file` -le $(( `date +%s` - 90 )) ]; then 
      date
      echo "file old, kill it!"
      pid=$(pgrep start$band.sh)
      echo  start$band.sh
      echo pid $pid
      if [ -n "$pid" ]; then
        $base_dir/kill.sh $pid
      else
        echo not running
      fi
    fi 
  
  else
    echo "$file does not exists"
  fi  
  
}

while true
do
  check_file 80m
  sleep 5
done



