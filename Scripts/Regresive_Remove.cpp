#include <stdio.h>
#include <unistd.h>
#include <string.h>
#include <stdlib.h>
main(int argc,char *argv[])
{
char Path[100];
sleep(atoi(argv[2]));
strcpy(Path,"rm -fr ");
strcat(Path,argv[1]);
system(Path);
printf("%s",Path);
}

