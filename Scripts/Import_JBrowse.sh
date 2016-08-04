##Script for import config file of JBrowse with seqref..
##$1-->Path
##$2-->Ruta JBrowse del Genoma
##$3-->Identificador de la librería
echo "Preparing JBrowse..."
mkdir $1/JBrowse
cp $2/trackList.json $2/$3trackList.json
cp ./../JBrowse/index.html $1/JBrowse/.
sed -i 's/genome.css/..\/..\/..\/..\/JBrowse\/genome.css/g' $1/JBrowse/index.html 
sed -i "s,queryParams.data,\"../../../$2\"; //,g" $1/JBrowse/index.html
sed -i "s,trackList.json,$3trackList.json,g" $1/JBrowse/index.html
cp -r ./../JBrowse/src $1/JBrowse/.
cp -r ./../JBrowse/img $1/JBrowse/.
cp ./../JBrowse/jbrowse_conf.json $1/JBrowse/.
