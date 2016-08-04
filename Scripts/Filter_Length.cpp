#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#define maxcad 1000
void Convertir(char *,char *);
int Num_tabs(char *);
main(int argc, char *argv[])
{
Convertir(argv[1],argv[2]);//argv[1]);

}
void Convertir(char *Origen,char *Destino)
{
     FILE *file1,*file2;
     file1=fopen(Origen,"r");
     file2=fopen(Destino,"w+");
     char cadena[maxcad],cadena2[maxcad],*p; 
    int cont=0;//Para contar el número de campos por fila 
          
while(!feof(file1))
    {
     cont=0;     
      fgets(cadena, maxcad, file1);
      int tabs=Num_tabs(cadena);
      strcpy(cadena2,cadena);
      fputs(">",file2);  
     
       p = strtok(cadena2,"\t");
       
       while (cont<=tabs)
       {
       if(cont<=tabs-2)
       {  
          fputs(p,file2);  
          fputs("|",file2);  
       }
       else if(cont==tabs-1)
       {
           fputs(p,file2); 
         fputs("\n",file2);
       }
   	else
   	{
   	 fputs(p,file2);
   	
  		 }
       p = strtok(NULL, "\t");
       cont++;
       }  
     // fputs(p,file2);
     }
   
    fclose(file1);
    fclose(file2);  
}
int Num_tabs(char *cad)
{
	int cont=0;
	char *s =cad;
	
while(*s!='\0')
{
	if(*s=='\t')
	cont++;
	s++;
}
return cont;
}

