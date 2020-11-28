#!/bin/bash
#
#
# buffer data from network to hdd
# convert from float to uint8
#
# dependendcies: 
#   csdr (ha7ilm)
#   record_buffer (build it yourself!)
#


parent=/media/dump1/2m

nc 127.0.0.1 9950 | csdr convert_f_u8 | ./record_buffer2 -o $parent
