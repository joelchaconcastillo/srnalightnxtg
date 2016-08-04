#!/usr/bin/env bash
#$1 = Es la ruta del archivo con formato Fasta
#$2 = Es la ruta del archivo con formato GFF
#$3 = El direcrotio donde se almacenarán los archivos de configuración
# format the reference sequences
cd ./../
./bin/prepare-refseqs.pl $COMPRESS --fasta $1 --out $3;

# official ITAG2.3 gene models
./bin/flatfile-to-json.pl $COMPRESS \
    --out $3 \
    --gff $2 \
    --type mRNA \
    --autocomplete all \
    --trackLabel genes  \
    --key 'Gene models' \
    --getSubfeatures    \
    --className transcript \
    --subfeatureClasses '{"CDS": "transcript-CDS", "exon": "hidden"}' \
    --arrowheadClass arrowhead \
    --urltemplate  "http://solgenomics.net/search/quick?term={name}" \
    --sortMem 1000000000\
    ;


# index feature names
./bin/generate-names.pl --out $3 --sortMem 1000000000;
