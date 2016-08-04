#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#include <ctype.h>
#define Longitud 300
char *strrev(char *);
void CSFasta_Qual(char *,char *, char *);
void Tabular_Fasta(char *,char *);
void Tabular_FastaQ(char *,char *);
void Fasta_FastaQ(char*,char*);
void FastaQ2H_Single(char*,char*);
void itoa(int n,char s[]);
int Match(char*);
void Encode(char *s);
void chomp(char *s);
char* Numero_char(char *s);
main(int argc,char *argv[])
{
	char c;
	char Qual[100],CSFasta[100],Out[100],Tabular[100],FastaQ2h[100],Fasta[100],Destino[100];
Qual[0]='\0';
CSFasta[0]='\0';
Tabular[0]='\0';
FastaQ2h[0]='\0';
Fasta[0]='\0';
Destino[0]='\0';
while(--argc>0)
{
	 if((*++argv)[0]=='-')
	 {
		while(c=*++argv[0])
		{
				switch(c)
				{
					case 'q':
						strcpy(Qual,*(argv+1));
					break;
					case 'c':
						strcpy(CSFasta,*(argv+1));
					break;
					case 'o':
						strcpy(Out,*(argv+1));
					break;
					case 't':
						strcpy(Tabular,*(argv+1));
					break;
					case '2':
					    strcpy(FastaQ2h,*(argv+1));	
					break;
					case 'f':
						strcpy(Fasta,*(argv+1));	
					break;
					case 'd':
						strcpy(Destino,*(argv+1));
					break;
				}
		}
	}
}
fprintf(stderr,"Preparing... ");

if((int)strlen(Qual)>0 && (int)strlen(CSFasta)>0 && (int)strlen(Out)>0) 
{
    CSFasta_Qual(CSFasta,Qual,Out);	
}
if((int)strlen(Tabular)>0 && (int)strlen(Out)>0) 
{
	(strcmp(Destino,"FastaQ"))?Tabular_Fasta(Tabular,Out):Tabular_FastaQ(Tabular,Out);
}
if((int)strlen(FastaQ2h)>0 && (int)strlen(Out)>0) 
{
	FastaQ2H_Single(FastaQ2h,Out);
}
 if((int)strlen(Fasta)>0 && (int)strlen(Out)>0) 
{
	Fasta_FastaQ(Fasta,Out);
}

puts("File Converted\n");
}
void CSFasta_Qual(char *CSFastaPath,char *QualPath, char *CSFastaQPath)
{
	FILE *CSFasta,*Qual, *CSFastaQ;
	CSFasta=fopen(CSFastaPath,"r");
	Qual=fopen(QualPath,"r");
	CSFastaQ=fopen(CSFastaQPath,"w+");
	char CadCS[Longitud],CadQ[Longitud];
	while(!feof(CSFasta))
	{
		fgets(CadCS,Longitud,CSFasta);
		fgets(CadQ,Longitud,Qual);
		chomp(CadCS);
		chomp(CadQ);
		if(!strcmp(CadCS,CadQ))
		{
			//Identificador
			CadCS[0]='@';
			strcat(CadCS,"\n");
			fputs(CadCS,CSFastaQ);
			//Valores referentes a T..
			fgets(CadCS,Longitud,CSFasta);
			chomp(CadCS);
			fputs(CadCS,CSFastaQ);
			fputs("\n+\n",CSFastaQ);
			fgets(CadQ,Longitud,Qual);
			chomp(CadQ);
			strcpy(CadQ,Numero_char(CadQ));
			strcat(CadQ,"\n");
			fputs(CadQ,CSFastaQ);
			
		}		
	}
	
	
}
void Tabular_FastaQ(char *Origen,char *Destino)
{

	FILE *Old,*New;
	Old=fopen(Origen,"r");
	New=fopen(Destino,"w+");
	int i=0,Tabs=0;
	while(!feof(Old))
	{
 char cad[Longitud],*p,cad2[Longitud],Id[Longitud];
			fgets(cad,Longitud,Old);
			//Revisa si el archivo tabular es de dos columnas o tres
				Tabs=	Match(cad);
			//Se realiza una copia de la cadena original y dividir la copia en tokens
		strcpy(cad2,cad);
	
		 p = strtok(cad2,"\t");
		 	char Temp[Longitud],Aux[Longitud];
		 		if(cad2[0]!='#')//Filtrar las líneas vacías)
		 	{
				if(Tabs==2)
				{
			    	chomp(p);
					strcpy(Aux,"@");
					strcat(Aux,p);
					strcat(Aux,"|");
					strcpy(Temp,strtok(NULL, "\t"));
					chomp(Temp);
					strcat(Aux,strtok(NULL, "\t"));
					chomp(Aux);
					strcat(Aux,"\n");
					fputs(Aux,New);
					fputs(Temp,New);
					fputs("\n+\n",New);
					Encode(Temp);
				        strcat(Temp,"\n");
					fputs(Temp,New);
					
	
				}
				else if(Tabs==1)
				{
					i++;
					itoa(i,Id);
					chomp(Temp);
					strcpy(Temp,"@");
					strcat(Temp,Id);
					strcat(Temp,"|");
					strcpy(Aux,p);
					chomp(Aux);
					strcat(Aux,"\n");
					fputs(Temp,New);
					strcpy(Temp,strtok(NULL, "\t"));
					chomp(Temp);
					strcat(Temp,"\n");
					//strcat(Temp,Aux);
					fputs(Temp,New);
					fputs(Aux,New);
					fputs("+\n",New);
					chomp(Aux);
					Encode(Aux);
					strcat(Aux,"\n");
					fputs(Aux,New);
				}
			}
				
	
	}
	
	
	fclose(Old);
	fclose(New);	
}
void Tabular_Fasta(char *Origen,char *Destino)
{
	FILE *Old,*New;
	Old=fopen(Origen,"r");
	New=fopen(Destino,"w+");
	int i=0,Tabs=0;
	while(!feof(Old))
	{
 char cad[Longitud],*p,cad2[Longitud],Id[Longitud];
			fgets(cad,Longitud,Old);
			//Revisa si el archivo tabular es de dos columnas o tres
				Tabs=	Match(cad);
			//Se realiza una copia de la cadena original y dividir la copia en tokens
		strcpy(cad2,cad);
	
		 p = strtok(cad2,"\t");
		 	char Temp[Longitud],Aux[Longitud];
		 	if(cad2[0]!='#')//Filtrar las líneas vacías)
		 	{
		 	
				if(Tabs==2)
				{
					strcpy(Aux,">");
					strcat(Aux,p);
					strcat(Aux,"|");
					fputs(Aux,New);
					strcpy(Temp,strtok(NULL, "\t"));
					fputs(strtok(NULL, "\t"),New);
					strcat(Temp,"\n");
					fputs(Temp,New);
	
				}
				else if(Tabs==1)
				{
					i++;
					itoa(i,Id);
					strcpy(Temp,"");
					strcat(Temp,">");
					strcat(Temp,Id);
					strcat(Temp,"|");
					strcpy(Aux,p);
					strcat(Temp,strtok(NULL, "\t"));
					strcat(Aux,"\n");
					strcat(Temp,Aux);
					fputs(Temp,New);
				}
			}
				
	
	}
	
	
	fclose(Old);
	fclose(New);
}
int Match(char *cad)
{
	int cont2=0;
	for(;*cad!='\0';cad++)(*cad=='\t')?cont2++:(int)true;
return cont2;
}
void Fasta_FastaQ(char* Origen,char* Destino)
{
	FILE *Old,*New;
	Old=fopen(Origen,"r");
	New=fopen(Destino,"w+");
	char Cad[Longitud],Temp[Longitud];
	char pr[Longitud];
	while(!feof(Old))
	{
		
		fgets(Cad,Longitud,Old);
		
		if(strcmp(Cad,"\n")&& Cad[0]!='#')//Filtrar las líneas vacías
		{
			chomp(Cad);
			Cad[0]='@';
			fputs(Cad,New);
			fputs("\n",New);
			fgets(Cad,Longitud,Old);
			chomp(Cad);
			fputs(Cad,New);
			fputs("\n+\n",New);
			int i;
			Encode(Cad);//for(i=0;Cad[i]!='\0' ;i++)Cad[i]=40;
			fputs(Cad,New);
			fputs("\n",New);
			Cad[0]='\n';//Se limpia la cadena
			Cad[1]='\0';
		}
		
	}
	fclose(Old);
	fclose(New);
	
}
void FastaQ2H_Single(char* Origen,char* Destino)
{
		FILE *Old,*New;
	Old=fopen(Origen,"r");
	New=fopen(Destino,"w+");
	char Cad[Longitud],Temp[Longitud];
	char pr[Longitud];
	while(!feof(Old))
	{
		fgets(Cad,Longitud,Old);
		if(strcmp(Cad,"\n") && Cad[0]!='#')//Filtrar las líneas vacías
		{
		    chomp(Cad);	
		    strcat(Cad,"\n");
			fputs(Cad,New);
			fgets(Cad,Longitud,Old);
			chomp(Cad);	
		    strcat(Cad,"\n");
			fputs(Cad,New);
			fgets(Cad,Longitud,Old);
			chomp(Cad);	
		    strcat(Cad,"\n");
			fputs(Cad,New);
			fgets(Cad,Longitud,Old);
			fgets(Cad,Longitud,Old);
			chomp(Cad);	
		    strcat(Cad,"\n");
			fputs(Cad,New);
			Cad[0]='\n';//Se limpia la cadena
			Cad[1]='\0';
			
		}
	}
	fclose(Old);
	fclose(New);
	
}
void itoa(int n,char s[])
{
	int i, sign;
	if((sign=n)<0)
		n=-n;
		i=0;
		do
		{
			s[i++]=n%10+'0';
		}while((n/=10)>0);
		if(sign<0)
			s[i++]='-';
			s[i]='\0';
			strrev(s);
}
char *strrev(char *str)
{
      char *p1, *p2;

      if (! str || ! *str)
            return str;
      for (p1 = str, p2 = str + strlen(str) - 1; p2 > p1; ++p1, --p2)
      {
            *p1 ^= *p2;
            *p2 ^= *p1;
            *p1 ^= *p2;
      }
      return str;
}
void chomp(char *s)
{
char *p;
while (NULL != s && NULL != (p = strrchr(s, '\n'))){
*p = '\0';
}
} 
void Encode(char *s)
{
	char *c=s;
				for(;*c!='\0';c++)*c=40;
}
char* Numero_char(char *s)
{
	char *p=s;
	static char t[100];
int signo=1,valor=0,i=0;
while(*p!='\0')
{
	if(*p=='-')
	{
		signo*=-1;
		p++;
	}
	for(;*p!=' ' && *p!='\n' && *p!='\0';p++)
	{
		valor=(10*valor)+(*p-'0');//Convierte el valor a un entero
	}
	valor= signo *valor;

	if(*p==' ' || *p=='\n' || *p=='\0')
	{
		char aux=valor+33;
		t[i]=aux;
		t[i+1]=NULL;
		(*p==' ')?p++:p;
		i++;
	}
	valor=0;
	signo=1;
}
return t;
} 
