#$1-->File
#$2-->Out
#$3-->Cut of ThreeP
#$4-->Cut of FiveP
#######################
##Cut elements of Three prime and Five prime
cat $1 | awk "{\
cont++;\
if(match(\$1,/^(\+)/) || match(\$1,/^(\@)/))\
{\
	print \$1\
}\
else\
{\
Three=$3;Three++;\
 Five=$4; Five--;\
 print substr(\$1,Three,length(\$1)-Five-Three)\
}\
              }" > $2
