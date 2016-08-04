
#!/bin/bash
# Obtiene un listado desde una base de datos gestionada por MySQL.
# Joel Chacon Castillo
# $1<-Archivo
# $2<-Tabla
echo "   `date +%Y-%m-%d_%H:%M`  "
#echo "$1"
#echo "File $2"
echo "Table $2"
mysql -u joel -pchacon -D mydb -e "$1 ;" > $2
zip -j $2.zip $2
#	| (sleep 25s; kill echo $!) & 
echo "Finished"
