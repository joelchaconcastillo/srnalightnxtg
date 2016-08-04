#!/bin/bash
#Se sustituyen los colores que se encuantran vacíos
#$1 es el archivo que se limpia
#$2 es el archivo destino con los colores limpios
sed 's/ /I/g' $1 > $2
