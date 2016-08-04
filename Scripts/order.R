#! /usr/bin/R

args <- commandArgs(TRUE)

data <- read.table(args[1], sep="\t")
data <- data[order(data$V1, data$V3, decreasing=F),]
write.table(data, file=args[1], sep="\t", quote=F, row.names=F, col.names=F)

