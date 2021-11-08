#!/bin/bash
#
#
# buffer data from network to hdd
# convert from float to uint8
#
# dependendcies: 
#   csdr (ha7ilm)
#   record_buffer (build it yourself!)
#   downsample 
#     (use version provided or compile from oe5dxl, see Readme)

#2*0.15=0.3

parent=/media/dump/80m
watchdog=/tmp/80m
echo start80m
#collects data from rPi with rtl_tcp
nc 172.16.0.81 2002 | ./downsample_x64 -a 0.15 -F u8 -f u8 -i /dev/stdin -l 64 -o /dev/stdout -r 0.15 | ./record_buffer -o $parent -w $watchdog

#output "service" of openwebRX is float, sdrbuffer uses uint8
#nc 127.0.0.1 9950 | csdr convert_f_u8 | ./record_buffer -o $parent

