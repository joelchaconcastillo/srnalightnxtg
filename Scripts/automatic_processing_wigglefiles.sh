#! /bin/bash
## script acepta argumentos:
## $1 La ruta relativa donde se encuentran los archivos wiggfiles
## $2 Ruta del genoma 
## conjunto de líneas de comando que generan archivos wiggfiles a partir de archivos de "reporte"
## para evitar problemas con los tamaños de los archivos, se genera un archivo de reporte por cada chr
## y cada uno de estos es procesado por el script que genera los archivos wiggle

echo "little script ready to start..."
echo "reading prewiggle files and transforme it into wiggle files"
for OUTPUT in $(ls $1/*.prewigglefile)
do
	perl ./../Scripts/Reporto_to_Wiggfile_v2.pl -i $OUTPUT -o $OUTPUT.wiggle
done

## posteriormente concatena cada uno de los archivos intermedios generados, tanto para la cadena plus como
## para la cadena negativa
## plus strand
echo "merging plus strand wiggle files"
for OUTPUT in $(ls $1/*.wiggle.plus_strand)
do
	cat $OUTPUT >> $1/Process.plus_strand
done
## negative strand
echo "merging negative strand wiggle files"
for OUTPUT in $(ls $1/*.wiggle.negative_strand)
do
	cat $OUTPUT >> $1/Process.negative_strand
done

## eliminamos todos los archivos intermedios creados/usados
echo "removing intermediate trash files"
#rm $1/*.wiggle.negative_strand
#rm $1/*.wiggle.plus_strand
#rm $1/*.prewigglefile

## a partir de los wiggle files creados corremos el script para obtener el bigwigfile
## debemos de tener una manera de renombrar al/los archivos de salida
## hay que tener el archivo con los tamaños de los cromosomas.. ?se podrá obtener de R?
## o se podrá tener un script que los saque a partir del gff de anotación?

## generamos los archivos bigwig
echo "from wigfiles to BigWig files!!"
./../bin/wigToBigWig $1/Process.negative_strand ./../$2/Anotacion/Sizes $1/BigWig
./../bin/wigToBigWig $1/Process.plus_strand ./../$2/Anotacion/Sizes $1/BigWig

echo "Removing temporary files..."
#rm $1/File_clean.bam
#rm $1/File_clean.bam.bai
#rm $1/File_clean.sam
#rm $1/File.sam
#rm $1/Process.negative_strand
#rm $1/Process.plus_strand
#rm $1/FileMapping

echo "Finished!!"

