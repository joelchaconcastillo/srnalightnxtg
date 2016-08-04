#! /usr/bin/R
#########################################################################################
## Cargamos las librerías 
library(GenomicRanges)
library(Rsamtools)
library(Biostrings)

## Tomamos los argumentos que pasamos por línea de comandos
args <- commandArgs(TRUE)

## Preparamos los elementos para leer el archivo de mapeo
efile <- args[1]  # nombre del archivo sam de entrada sin el .sam
RutaRelativa <- args[2] 
Anotacion <- args[3] #Es el nombre del archivo con formato Biomart del genoma
Id_Librerias<-args[4] #El identificador referente a cada librería
Version<-args[5] #La versión del organismo
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
	ranges <- IRanges(start=bam[[1]]$pos, width=width(bam[[1]]$seq)-1),
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
print ("Genome ready")

## Query es la secuencia de sRNAs con un width de 1 y el subject es la anotación del genoma
## Generamos lo necesario para imprimir el archivo estilo GFF
val <- 1
overlap <- as.data.frame(findOverlaps(mapGR, Athaliana))
totelem <- dim(overlap)[1]
printelemen <- c()
numS <- overlap$subjectHits
numQ <- overlap$queryHits
##########################################################
strdelem <- as.character(mcols(Athaliana)$sstrand[numS])
strd <- as.character(strand(mapGR)[numQ])
start <- as.numeric(start(mapGR)[numQ])
seq <- as.character(mcols(mapGR)$seq[numQ])	
seq <- DNAStringSet(seq)
seq[strd == '-'] <- reverseComplement(seq[strd == '-'])
seq <- as.character(seq)
end <- as.character(start(mapGR)[numQ]+mcols(mapGR)$qwidth[numQ] - 1)
score <- table(seq)
gid <- as.character(mcols(Athaliana)$"gene_id"[numS])
gbtype <- as.character(mcols(Athaliana)$"gene_biotype"[numS])
gsbtype <- as.character(mcols(Athaliana)$"gene_subtype"[numS])
name <- as.character(unlist(mcols(mapGR)$qname[numQ]))
chr <- as.character(seqnames(mapGR)[numQ])
summ <- paste(strdelem, gid, gbtype, gsbtype, sep="|")
mmiss <- as.character(mcols(mapGR)$miss[numQ])


Destino<-paste(RutaRelativa,"FileMapping",sep="/") # Es el archivo de destino con la información entre el genoma de referencia y la muestra secuencial
write(paste("null",chr, start, end, strd, mmiss, seq, "loci",score[seq],gsbtype,Id_Librerias,gid,Version, sep="\t"), file=Destino, append=T) # la secuencia se imprime en 5' - 3' con respecto a como mapeado 

############################################################
name <- as.character(unlist(mcols(mapGR)$qname[numQ]))
nametmp <- do.call("rbind", strsplit(name, "|", fixed=TRUE))
name <- nametmp
rm(nametmp)
data <- GRanges(
	seqnames <- as.character(seqnames(mapGR)[numQ]),
	ranges <- IRanges(start=c(start), end=c(nchar(seq) + start - 1)),
	strand <- strd
)
mcols(data)$names <- name[,1]
mcols(data)$chr <- as.character(seqnames(mapGR)[numQ])
mcols(data)$seq <- seq
mcols(data)$score <- score[seq] #name[,2] se necesita el score de la muestra con el genoma de referencia
mcols(data)$gsbtype <- as.character(mcols(Athaliana)$"gene_subtype"[numS])

#######################################################################################3
##############################################################################################################
##############################################################################################################
## vamos a optar por trabajar con el arreglo que pertenece a genomicRanges, ya que su velocidad de ensamblado
## es mucho más rápida
#######################################################################################################

## para tomar la máxima región de un elemento dentro de una anotación
## tenemos una secuencia de 24 nucleótidos y generamos dos puntos 
## desde el inicio del elemento hasta la mitad mas uno
## y el otro desde la mitad menos uno al final del elemento
## y buscamos cual de las dos nuevas coordenadas se ajusta al sistema anotación existente 

## obtenemos los identificadores de las secuencias que poseen al menos un hit con algun elemento estructural
data2 <- mcols(data[mcols(data)$gsbtype == "rRNA" | mcols(data)$gsbtype == "tRNA" | mcols(data)$gsbtype == "snRNA" | mcols(data)$gsbtype == "snoRNA",])$names
data2 <- unique(data2)
## las eliminamos del análisis
data2 <- data[!(mcols(data)$names %in% data2)]
data <- data2
rm(data2)

## podría ser interesante guardar la información que CONTIENE los elementos estructurales
## write.table(data, file=paste(args[3], ".total_elemt", sep=""), quote=F, row.names=F, col.names=F)

print("all structural RNA were removed")


########################################################################################################################
########################################################################################################################
## Calculamos los valores normalizados en transcritos por millón
## Y transcritos por millón ponderados por el número de hit de cada secuencia

## creamos subdata para calcular RPM y el número de veces que mapea cada secuencia en el genoma (con la función table)
data$score <- as.character(data$score)
data$score <- as.numeric(data$score)
subdata <- data.frame(names = data$names, score = data$score)
subdata <- unique(subdata)
ftpm <- 1000000/sum(subdata$score) # ahora tenemos nuestro valor de normalización de RPM
hit <- table(data$names)

## agregamos los valores de rpm y rpm-hits al arreglo
data$rpm <- data$score * ftpm
data$rpmh <- data$score * ftpm / hit[data$names]
data$rpm <- as.numeric(data$rpm)
data$rpmh <- as.numeric(data$rpmh)

## para poder imprimir los decimales como en teoría debemos: 
data$rpm <- sprintf("%.6f",data$rpm)
data$rpmh <- sprintf("%.6f",data$rpmh)
Destino<-paste(RutaRelativa,"report_",sep="/")
## Generación de los archivos prewiggfile para generar el archivo wigfile
chrs <- unique(data$chr) ## creamos un archivo para cada uno de los cromosomas
tmp <- data.frame(seq = as.character(data$seq), chr = data$chr, start = start(data), strand = as.character(strand(data)), rpm = data$rpm)
for(i in 1:length(chrs)){
	write.table(tmp[tmp$chr == chrs[i], c("seq", "chr", "start", "strand", "rpm")], file=paste(Destino, chrs[i], ".prewigglefile", sep=""), quote=F, row.names=F, col.names=F, sep="\t")
}


write.table(data, file = args[3], sep = "\t", quote=F, row.names=F, col.names=F)
## a partir de aquí vamos a procesar el archivo que obtuvimos con perl para generar el wigfile
