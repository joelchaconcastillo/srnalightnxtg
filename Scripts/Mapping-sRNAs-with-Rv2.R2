#! /usr/bin/R
#########################################################################################
## Cargamos las librerías 
library(GenomicRanges)
library(Rsamtools)
library(Biostrings)
#library(RMySQL)


## Tomamos los argumentos que pasamos por línea de comandos
args <- commandArgs(TRUE)

## Preparamos los elementos para leer el archivo de mapeo
efile <- args[1]  # nombre del archivo sam de entrada sin el .sam
RutaRelativa <- args[2]
Anotacion <- args[3]
Id_Librerias<-args[4]	
samfile <- paste(efile, ".sam", sep="")
bamFile <- asBam(samfile, destination=efile)
what <- c("qname", "rname", "strand", "pos", "qwidth", "seq", "cigar")

## pedimos como tag el número de missmatches 
param <- ScanBamParam(what=what, tag="NM")  
bam <- scanBam(bamFile, param=param)

## Generamos el elemento con la información de las secuencias a anotar
## NOTA: necesitamos eliminar las secuencias menores a 18nt
mapGR = GRanges(
	seqnames <- bam[[1]]$rname,
	ranges <- IRanges(start=bam[[1]]$pos, width=1),
	strand <- bam[[1]]$strand
)
mcols(mapGR)$qwidth <- bam[[1]]$qwidth
mcols(mapGR)$qname <- bam[[1]]$qname # dejamos fusionado el score con el nombre de la secuencia
mcols(mapGR)$seq <- bam[[1]]$seq
mcols(mapGR)$miss <- unlist(bam[[1]]$tag)
mapGR <- mapGR[width(mcols(mapGR)$seq) >= 18,]
print ("BAM file and GRanges element ready!")

## Generamos nuestro marco de referencia, con la anotación del genoma
table <- read.table(Anotacion)
colnames(table) <- c("Chr", "start", "end", "iD", "strand", "gene_biotype", "gene_subtype")
Athaliana <- GRanges(
	seqnames <- table$Chr,
	ranges <- IRanges(start=c(table$start), end=c(table$end)),
	strand <- "*"
)
mcols(Athaliana)$gene_id <- table$iD
mcols(Athaliana)$gene_biotype <- table$gene_biotype 
mcols(Athaliana)$gene_subtype <- table$gene_subtype
mcols(Athaliana)$sstrand <- table$strand
print ("Arabidopsis genome ready")

## Query es la secuencia de sRNAs con un width de 1 y el subject es la anotación del genoma de Arabidopsis
## Generamos lo necesario para imprimir el archivo estilo GFF
Tablecols<-paste("Id_Moleculas","Chr","Inicio","Fin","Sentido","Missmatches","Secuencia","Loci","Elemento_Mapea","Numero_Moleculas","Id_SubDominio","Id_Librerias",sep =",")
SMolecula<-matrix(0, ncol = 1, nrow = 12)
query<-paste("INSERT INTO SMolecula(",Tablecols,") VALUES")
rownames(SMolecula)<-c("Id_Moleculas","Chr","Inicio","Fin","Sentido","Missmatches","Secuencia","Loci","Elemento_Mapea","Numero_Moleculas","Id_SubDominio","Id_Librerias")
val <- 1
overlap <- as.data.frame(findOverlaps(mapGR, Athaliana))
totelem <- dim(overlap)[1]#573
printelemen <- c()
numS <- overlap$subjectHits#573
numQ <- overlap$queryHits#573
strdelem <- as.character(mcols(Athaliana)$sstrand[numS])#573
strd <- as.character(strand(mapGR)[numQ])#573
seq <- as.character(mcols(mapGR)$seq[numQ])	
seq <- DNAStringSet(seq)
seq[strd == '-'] <- reverseComplement(seq[strd == '-'])
seq <- as.character(seq)#573
start <- as.character(start(mapGR)[numQ])#573
end <- as.character(start(mapGR)[numQ]+mcols(mapGR)$qwidth[numQ] - 1)#573
score <- table(seq)
gid <- as.character(mcols(Athaliana)$"gene_id"[numS])#573
gbtype <- as.character(mcols(Athaliana)$"gene_biotype"[numS])#573
gsbtype <- as.character(mcols(Athaliana)$"gene_subtype"[numS])#573
name <- as.character(unlist(mcols(mapGR)$qname[numQ]))#573
chr <- as.character(seqnames(mapGR)[numQ])#573
summ <- paste(strdelem, gid, gbtype, gsbtype, sep="|")#573
mmiss <- as.character(mcols(mapGR)$miss[numQ])


Destino<-paste(RutaRelativa,"FileMapping",sep="/")
#printelemen <- append(printelemen, paste("null",chr, start, end, strd, mmiss, seq, "loci",gid,score[seq],"4","1" sep="\t"))
write(paste("null",chr, start, end, strd, mmiss, seq, "loci",score[seq],gsbtype,Id_Librerias,gid, sep="\t"), file=Destino, append=T) # la secuencia se imprime en 5' - 3' con respecto a como mapeado 
Destino2<-paste(RutaRelativa,"FileMappingTODO",sep="/")
write(paste(gbtype,gsbtype,sep="\t"), file=Destino2, append=T)
#value<-function(dat)
#{
# paste("\'",dat,"\'",sep="")
#}
#Original printelemen <- append(printelemen, paste(name, seq, chr, start, strd, summ, mmiss, sep="\t"))
#cad<-paste("null",value(chr),start,end,value(strd),mmiss,value(seq),value("LOCI"),value(gid),score[seq],"4","1", sep=",")
#base<-paste(paste("(",cad,")"),collapse=",")	
#query<-paste(query,base)	
##############################################Conexión
#mydb = dbConnect(MySQL(), user='jchacon', password='9VcftEHVTDgVfdJ', dbname='Chacon')
##############################################Conexión
#mydb = dbConnect(MySQL(), user='root', password='nodescompongasmysql', dbname='Chacon')
## write(paste(name, seq, chr, start, strd, summ, mmiss, sep="\t"), file="file.name", append=T) # la secuencia se imprime en 5' - 3' con respecto a como mapea
	
	
	
	
#dbSendQuery(mydb, query)	

#dbDisconnect(mydb)
##PRUEBA
#cad<-"insert into SubDominio (Id_SubDominio,Tipo_SubDominio,Inicio,Fin,Id_Anotacion) values"
 #p1<-1
 #cad2<-"(null,'p1',1,10,1)"
 #while(p1<100)
 #{
 #p1=p1+1
  #cad2<-paste(cad2,"(null,'p1',1,10,1)",sep=",")
 #}
  #cad3<-paste(cad,cad2)

#mydb = dbConnect(MySQL(), user='jchacon', password='9VcftEHVTDgVfdJ', dbname='Chacon')
#dbSendQuery(mydb, cad3)
#dbDisconnect(mydb)
