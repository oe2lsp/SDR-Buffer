#!/bin/bash 
#
# read data (starting with date time from arguments) from hdd and pipe to stdout 
# limit to next 20 minutes (next 20 files)
# arguments <day> <time>
#   <day> e.g. 20200929
#   <time> e.g. 1959
#
#
#

band=$1
day=$2
ts=$3

for line in $(find /var/www/html/$band/$day/* -maxdepth 0  | grep -A 20 $ts |  grep -v out | sort -n); do
  cat $line
  #echo $line
done
