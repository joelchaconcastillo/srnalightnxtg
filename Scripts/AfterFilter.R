# CalidadCicloR
 
args <- commandArgs(TRUE) 
library(ShortRead)
destino=args[1]
enco=args[2]
Minv= args[3]
Maxv= args[4]
BasesI= as.numeric(args[5])
BasesA= as.numeric(args[6])
Redundancia=args[7]
Rango=args[8]
CalidadSeq=as.numeric(args[9])
Minvalue=as.numeric(args[10])
#Formato=args[11]
long=0;
File=paste(destino,"/ObjectWithTrim.fq",sep="")
streamer = FastqStreamer(File, n=1000000)
while (length({ObjectTrimn = yield(streamer,qualityType=enco)})) {
long=long+length(ObjectTrimn)
  ObjectTrimn<-ObjectTrimn[apply(as(quality(ObjectTrimn),"matrix"),1,function(x)mean(x[!is.na(x)]))>=CalidadSeq]
  ObjectTrimn<-ObjectTrimn[apply(as(quality(ObjectTrimn),"matrix"),1,function(x)min(x[!is.na(x)]))>=Minvalue]
  
  
 
if(Rango)
{
	ObjectTrimn = ObjectTrimn[width(ObjectTrimn) >= Minv & width(ObjectTrimn) <= Maxv] 
	
}
if(Redundancia)
{	
	solexaUnique = ObjectTrimn[!srduplicated(ObjectTrimn)]

}
pfilt = polynFilter(threshold=BasesI) ##Bases idénticas
nfilt = nFilter(threshold=BasesA) ##Bases ambiguas
ObjectClean = ObjectTrimn[pfilt(ObjectTrimn)]
ObjectClean = ObjectClean[nfilt(ObjectClean)]
final= paste(destino,"/PureLecture.fq",sep="")
writeFastq(ObjectClean,final,mode="a")
size<-as.numeric(unlist(strsplit(system(paste("wc -l ",File,sep=""),intern = TRUE), " "))[1])
size<-size/4
percent<-((long*100)/size)
write(paste("Proccesing with R ",percent,sep=""), stderr())
}
close(streamer)
