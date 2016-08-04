###$1 Archivo de entrada SAM
###$2 Archivo de salida
###$3 Archivo Biomart o GFF o BED
###$4 Id_Libreria
###$5 Id_Genoma
###$6 Ruta
echo "Converting file sam..."

echo "Fixing columns..."

 cat $1 | awk '{\
if( !match($1,/^(@HD)/) && !match($1,/^(@SQ)/) && !match($1,/^(@PG)/) && $14 != "" )\
	{\
  	 print $1"Secuencia="$10"\t"$2"\t"$3"\t"$4"\t"$5"\t"$6"\t"$7"\t"$8"\t"$9"\t"$10"\t"$11"\t"$12"\t"$13"\t"$14 \
	}\
	else\
	{\
	print; \
	}\
}'> $1_temp

echo "Filtering length >= 18..."
./../bin/sambamba view -S $1_temp -F "sequence_length >= 18" -f bam -p -t 1 -o $1_temp.bam

#rm $1_temp

echo "Shorting Bam..."

./../bin/sambamba sort $1_temp.bam -p -t 1 -o $1_temp_sorted.bam

#rm $1_temp.bam

echo "Indexing file bam..."

./../bin/sambamba index $1_temp_sorted.bam -t 1
 
echo "Converting Bam to Bed"
##Tag NM
./../bin/bedtools bamtobed -i $1_temp_sorted.bam -tag NM > $1_temp_sorted.bed

echo "Removing temp files..."

#rm $1_temp_sorted.bam
#rm $1_temp_sorted.bam.bai

echo "Checking intersections with overlap > 50% ..."

./../bin/bedtools intersect -a $1_temp_sorted.bed -b $3 -wa -wb -f 0.50 > $2
#./bin/bedtools intersect -a $3 -b $1_temp_sorted.bed -wa -wb -f 0.50 > $2

#rm $1_temp_sorted.bed

#echo "Checking score of the reads.."

#cut -f4 $2 | sort | uniq -c > $2_Score_Reads

echo "Unlinking reads of sequence-name"

sed -i 's/Secuencia=/ /g' $2

echo "Checking table frecuency"

./../bin/tally -i $2 -o $2_Reads_Scores -record-format '%I%t%I%t%I%t%I%b%R%#' --nozip -format %R%t%C%n 

echo "Parse fields to table of database"

#perl ./../Scripts/Prepare_Database3.pl --FileIn $2 --FileOut $2_BD --Ruta $6 --Id_Libreria $4 --Id_Genoma $5

php ./../Scripts/Score_sRNAs.php $2 $4 $5

echo "Finished press any key to continue..."
