# CalidadCicloR
 
args <- commandArgs(TRUE)
library(ShortRead)
File=args[1]
destino=args[2]
enco=args[3]
linker= args[4]
Percent= as.numeric(args[5])
cont=0
long=0
maxQ=0
minQ=0
meanQ=0
File=paste(destino,File,sep="")
streamer = FastqStreamer(File, n=1000000)
Distribution=NULL
while (length({ObjectShortRead = yield(streamer,qualityType=enco)})) {
cont=cont+1
abc = alphabetByCycle(sread(ObjectShortRead), alphabet=c("A","T","G","C","N"))
numQuals = as(quality(ObjectShortRead), "matrix") 
numQuals[is.na(numQuals)] <- 0
long=long+length(ObjectShortRead)
if(cont==1)
{
abcFreq = abc / length(ObjectShortRead)
maxQ  = apply(numQuals,2,max)
minQ  = apply(numQuals,2,min)
meanQ  = apply(numQuals,2,sum)
}
else
{
abcFreq2= abc / length(ObjectShortRead)
while(dim(abcFreq)[2]< dim(abcFreq2)[2])
{
abcFreq=cbind(abcFreq,0)
}
while(dim(abcFreq)[2] > dim(abcFreq2)[2])
{
abcFreq2=cbind(abcFreq2,0)
}
abcFreq = abcFreq +abcFreq2
maxQ2=apply(numQuals,2,max)
minQ2=apply(numQuals,2,min)
meanQ2=apply(numQuals,2,sum)
while(length(maxQ2)< dim(abcFreq)[2])
{
maxQ2=c(maxQ2,0)
minQ2=c(minQ2,0)
meanQ2=c(meanQ2,0)
}
while(length(maxQ) < dim(abcFreq)[2])
{
maxQ=c(maxQ,0)
minQ=c(minQ,0)
meanQ=c(meanQ,0)
}
maxQ=apply(rbind(maxQ,maxQ2),2,max)
minQ=apply(rbind(minQ,minQ2),2,min)
meanQ=apply(rbind(meanQ,meanQ2),2,sum)
}
dists = srdistance(ObjectShortRead, linker)
rest=max(width(ObjectShortRead))-width(linker)
linkerMod = paste(linker,paste(rep("N",rest),collapse=""),sep="")
maxRmm = round(seq_len(width(linker))*Percent)
maxRmm = c(maxRmm, rep(max(maxRmm),rest))
ObjectShortReadTrim  = trimLRPatterns(Lpattern = "", Rpattern = linkerMod, ObjectShortRead,
                  with.Rindels=FALSE, max.Rmismatch=maxRmm, Rfixed=FALSE)
final= paste(destino,"ObjectWithTrim.fq",sep="")
ObjectShortReadTrim = ObjectShortReadTrim[width(ObjectShortReadTrim) > 0]
writeFastq(ObjectShortReadTrim,final,mode="a")
size<-as.numeric(unlist(strsplit(system(paste("wc -l ",File,sep=""),intern = TRUE), " "))[1])
size<-size/4
percent<-((long*100)/size)
Distribution<-c(Distribution,width(ObjectShortReadTrim))
#write(paste("Proccesing with R ",percent,sep=""), stderr())
}
close(streamer)
meanQ<-meanQ/long
numcols=dim(abcFreq)[2]
cycles = 1:numcols
abcFreq=abcFreq/cont
cols = rainbow(5)
png(filename=paste(destino,"CalidadCiclo.png",sep=""), width=1000, height=1000)
plot(cycles, minQ, type="b", col="red", main="Quality by Cycle", las=3, ylim=c(1,max(maxQ)+10),
      xlab="cycle", ylab="quality")
lines(cycles, meanQ, type="b", col="green")
lines(cycles, maxQ, type="b", col="blue")
png(filename=paste(destino,"FrecuenciaCiclo.png",sep=""), width=1000, height=1000)
barplot(abcFreq, ylim=c(0,1.1), col=cols, beside=FALSE, space=0, names.arg=cycles, las=3,
     xlab="cycle", ylab="Frequency", main="Base frequency by Cycle"  ) 
legend("top",legend=rownames(abcFreq), fill=cols, bty="n", horiz=TRUE)
png(filename=paste(destino,"AfterLink.png",sep=""), width=1000, height=1000)
hist(Distribution, col="skyblue",xlab="(Witdh read) cleaned read ",ylab="number of sequences",main="Length distribution after linker removal", breaks=0:numcols)
