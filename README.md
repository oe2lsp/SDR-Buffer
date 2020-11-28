# SDR-Buffer	
Simple web based solution for
buffering SDR data for studying spectrum and searching for QRM 


**Example** https://websdr.iks.tugraz.at/sdrbuffer/

![Screenshot](screenshot/screenshot1.png)

consists out of following components:
- start script
	(record_buffer2)
- cronjobs
	- create waterfall (waterfall-x64)
	- cleanup
- web front end
	- displaying available data
	- starting and stopping openwebrx for replay
	- symlinks to waterfall data for each band
- openwebrx 
 (patched so that it listens to bash parameters instead of static config file)
- reverse proxy as entry point  (nginx)
	
the binary files and scripts are located in /opt/save

## record_buffer2
(buffering data to disk, each minute one file)
simple C program 
for help use parameter 
> `--help` 
> 
input is stdin/pipe

**building** record buffer2: 		
> gcc record_buffer.c -o record_buffer2

## waterfall-x64
based on waterfall tool from oe5dxl, will be published here:
https://github.com/oe5hpm/dxlAPRS 

## web front end
simple php7 web page, also controlling the sessions of openwebrx and killing ghost sessions after 20min

## reverse proxy
keeping the whole project in sub directories of an website to keep entrypoint compact (or to adapt to given limitations)


## copyright
**openwebrx** by HA7ILM [AGPL-3.0 License]

**waterfall-x64** as part of tools from OE5DXL  [GPL-2.0+]

other tools, scrips developed for this project are published under [GNU GPLv3]

## TODO:
- documentation of used ports
- migrate openwebrx to new version (maby using rtl_tcp interface) or own owrx connector???


**push requests welcome**
