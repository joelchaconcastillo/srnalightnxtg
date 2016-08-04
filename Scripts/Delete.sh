
#!/bin/bash
# Obtiene un listado desde una base de datos gestionada por MySQL.
# Joel Chacon Castillo
# $1<-BD
# $2<-Query
echo "   `date +%Y-%m-%d_%H:%M`  "
echo "  DataBase: sRNA3"
echo "  Query $1"
mysql -u joel -pchacon -D mydb \
        -e "$1" \
        -N
echo "Finished"
