
#!/bin/bash
# Obtiene un listado desde una base de datos gestionada por MySQL.
# Joel Chacon Castillo
# $1<-BD
# $2<-Archivo
# $3<-Tabla
echo "   `date +%Y-%m-%d_%H:%M`  "
#echo "  DataBase: sRNA"
#echo "  File $1"
#echo "  Table $2"
echo "Saving data..."
mysql --local-infile=1 -u joel -pchacon -D mydb\
        -e "set UNIQUE_CHECKS=0;
            set FOREIGN_KEY_CHECKS=0;
	    set myisam_sort_buffer_size=1024*1024*1024;
	    set bulk_insert_buffer_size=1024*1024*1024;
 	    set myisam_repair_threads=10;
	    load data local infile '$1' into table $2 ;
	    set UNIQUE_CHECKS=1;
	    set FOREIGN_KEY_CHECKS=1;

" \
        -N
echo "Finished"
