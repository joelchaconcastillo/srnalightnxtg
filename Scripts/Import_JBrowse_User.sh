##Script for import config file of JBrowse with seqref..
##$1-->Nombre del usuario
##$2-->Ruta JBrowse del Genoma
##$3-->Nombre y version del genoma
echo "Preparing JBrowse..."
cp ./../JBrowse/index.html ./../Users/$1/JBrowse/$3index.html
sed -i 's/genome.css/..\/..\/..\/JBrowse\/genome.css/g' ./../Users/$1/JBrowse/$3index.html 
sed -i "s,queryParams.data,\"../../$2\"; //,g" ./../Users/$1/JBrowse/$3index.html
sed -i "s,trackList.json,$1trackList.json,g" ./../Users/$1/JBrowse/$3index.html

