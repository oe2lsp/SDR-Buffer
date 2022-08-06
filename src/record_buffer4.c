#define _GNU_SOURCE //for fcloseall
#include <stdio.h>
#include <string.h>
#include <stdlib.h>
#include <dirent.h>
#include <time.h>
#include <sys/stat.h>
#include <sys/types.h>
#include <sys/select.h>
//#include <stdafx.h>
//#include <io.h>
#include <fcntl.h>
//#include <complex.h>
//#include <limits.h>
//#include <math.h>
#include <unistd.h>
#include <utime.h>

#define FALSE 0
#define TRUE 1


char *getDate() 
{
    char *date = malloc(120);
    time_t t = time(NULL);
    struct tm tm = *localtime(&t);
    sprintf(date, "%4d%02d%02d", tm.tm_year + 1900, tm.tm_mon + 1, tm.tm_mday);
    return date;         
}
char *getTime()
{
    char *currenttime = malloc(120);
    time_t t = time(NULL);
    struct tm tm = *localtime(&t);
    sprintf(currenttime, "%02d%02d", tm.tm_hour, tm.tm_min);
    return currenttime;         
}
int getMinute()
{
    time_t t = time(NULL);
    struct tm tm = *localtime(&t);
    return tm.tm_min;
}  
int checkFile(char *file)
{
  //chick if file exists or create it
  if ( access(file, F_OK) != 0)
  {	 
    int fd;
    fd = creat(file,S_IWUSR | S_IWGRP | S_IWOTH);
    close(fd);
    printf("created watchdog file\n");
  }
}
int updateFile(char *file)
{
  //update timestamp of file

  struct utimbuf new_times;
  new_times.modtime = time(NULL);
  new_times.actime = time(NULL);
  printf("updated watchdog\n ");
  if (utime(file, &new_times) < 0) 
    perror("update watchdogfile failed \n");
}

int main( int argc, char *argv[]) 
{
    char watchdogFile[400];
    int watchdog=0;
    char outputFolder[400];
    char outputFolderDay[500];
    char outputFile[600];
    FILE *outputFileH = NULL;
    char oldMinute = 255;
    char debug = FALSE;

    char rbuf[1024];//4096 maybe not possible with select
    fd_set set;
    struct timeval timeout;
    int rv, rvb;
    //int filedesc = open("/dev/stdin", O_RDWR );
    int filedesc = open("/dev/stdin", O_RDWR & ~O_NONBLOCK );
  
    //FD_ZERO(&set);
    //FD_SET(filedesc, &set);
    timeout.tv_sec = 10;
    timeout.tv_usec = 10000;
  

    //parse arguments
    int argc_cnt;
    for(argc_cnt = 1; argc_cnt < argc; argc_cnt++)
    {
        if (strcmp(argv[argc_cnt], "-d") == 0)
            debug = TRUE;

        if ((strcmp(argv[argc_cnt], "-o") == 0) && (argc > argc_cnt)) 
        {
            strcpy(outputFolder,argv[argc_cnt+1]);
        }
        if ((strcmp(argv[argc_cnt], "-w") == 0) && (argc > argc_cnt))
        {
            strcpy(watchdogFile,argv[argc_cnt+1]);
	    watchdog = 1;
        }
	if ((strcmp(argv[argc_cnt], "--help") == 0))
        {
            printf("tool for recording I/Q data\n\n");
            printf("possible parameters:\n");
            printf("-o <output folder>      /save/sdr\n");
            printf("-w <watchdog file>      /tmp/20m\n");
            printf("  saves files to <date>/<minute> file structure");
            printf("\nversion 0.2 build 202009\n");
            return 0;
        }
    }

    //check ouput folder
    DIR* dir = opendir(outputFolder);
    if (!dir)
    {
        printf("output folder cannot be opened\n");
        return 0;
    }   


    //freopen(NULL, "rb", filedesc);
    //_setmode(fileno(stdin), _O_BINARY);

    while(1) 
    {
	//if new minute close file
        if ((oldMinute != getMinute()) && (outputFileH != NULL))
        {
            oldMinute = getMinute();
            fclose(outputFileH);
            fcloseall();
            fcloseall();
            outputFileH = NULL;
            if(watchdog)
	    {
	      checkFile(watchdogFile);
              updateFile(watchdogFile);
            }
	}

        //if no file opened
        if (outputFileH == NULL)
        {
            char *day = getDate();
            sprintf(outputFolderDay, "%s/%s/", outputFolder, day);

            DIR* day_dir = opendir(outputFolderDay);
            if (!day_dir)
            {
                printf("outputFolderday %s\n",outputFolderDay);
                mkdir(outputFolderDay, 0777);
            }
            char *outputTime = getTime();
            sprintf(outputFile, "%s%s", outputFolderDay, outputTime);
            printf("outputFile %s\n",outputFile);
            outputFileH = fopen(outputFile,"a+");
	    free(day);
	    free(day_dir);
	    free(outputTime);
        }

        timeout.tv_sec = 10;
        timeout.tv_usec = 10000;
  
        FD_ZERO(&set);
        FD_SET(filedesc, &set);
        rv=select(filedesc + 1, &set, NULL, NULL, &timeout);
        //select(1, &set, NULL, NULL, &timeout);
	if(rv == -1)
        {
            perror("select\n"); /// an error accured 
            fclose(outputFileH);
            fcloseall();
            fcloseall();
	    close(filedesc);
	    return 0;
	    //close(filedesc);
        }
       	else if(rv == 0)
        {
            printf("timeout\n"); /// a timeout occured 
            fclose(outputFileH);
            fcloseall();
            fcloseall();
	    close(filedesc);
	    return 0;
	    //close(filedesc);
        }  /*  
        else    
	{*/
        if(FD_ISSET(filedesc, &set))
	{
            rvb=read(filedesc, rbuf, sizeof(rbuf)); /* there was data to read */
            //rvb= fread(rbuf, 1 , sizeof(rbuf), filedesc);
	    if (rvb > 0)
            {
	        size_t w;
                for (size_t nleft = rvb; nleft > 0; )
                {
                    w = fwrite(rbuf, 1, nleft, outputFileH);
		    if (w == 0)
		    {
                        printf("error: unable to write %s\n", outputFile);
                        exit(1);
                    }
	            nleft -= w;
                    fflush(outputFileH);
                }
            }
        }			       
    }
} 

