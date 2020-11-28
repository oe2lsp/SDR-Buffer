#define _GNU_SOURCE //for fcloseall
#include <stdio.h>
#include <string.h>
#include <stdlib.h>
#include <dirent.h>
#include <time.h>
#include <sys/stat.h>
#include <sys/types.h>
//#include <stdafx.h>
//#include <io.h>
#include <fcntl.h>
//#include <complex.h>
//#include <limits.h>
//#include <math.h>

#define FALSE 0
#define TRUE 1


char * getDate() 
{
    char *date = malloc(9);
    time_t t = time(NULL);
    struct tm tm = *localtime(&t);
    sprintf(date, "%d%02d%02d", tm.tm_year + 1900, tm.tm_mon + 1, tm.tm_mday);
    return date;         
}
char * getTime()
{
    char *currenttime = malloc(9);
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

int main( int argc, char *argv[]) 
{
    char outputFolder[400];
    char outputFolderDay[500];
    char outputFile[600];
    FILE *outputFileH = NULL;
    char oldMinute = 255;
    char debug = FALSE;

    char rbuf[4096];
    size_t r;

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
        else if ((strcmp(argv[argc_cnt], "--help") == 0))
        {
            printf("tool for recording I/Q data\n\n");
            printf("possible parameters:\n");
            printf("-o <output folder>      /save/sdr\n");
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


    freopen(NULL, "rb", stdin);
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
        }

        // ====== input samples (I and Q) from stdin ===
        //int input_i = getchar(); 
        //int input_q = getchar();

        //fputc(input_i, outputFileH);
        //fputc(input_q, outputFileH);
        
        r = fread(rbuf, 1, sizeof(rbuf), stdin);
        if (r > 0)
        {
            size_t w;
            for (size_t nleft = r; nleft > 0; )
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
 


        if (feof(stdin)) 
        {
            fclose(outputFileH);
            return 1;
        }
    }
} 

