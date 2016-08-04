#! /bin/bash
#$1-->Text input
#$2-->Text Output
# Author: Joel Chacón Castillo
echo "Converting data in coords ..."
#grep _ $1 | cut -d "_" -f 3,4 | sort -n -t_ -k 2 | uniq -c | sed 's/w//g' |sed 's/_x/ /g' | awk '{ print $1*$3" "$2}'|sort -n -k2 -o $2
#grep _ $1 | cut -d "_" -f 3 | sed 's/w//g' | sort| uniq -c| sort -n -k 2 -o $2
grep _ $1 | cut -d "_" -f 3 | sed 's/w//g' | awk '{sal[$1]++;}END{ for(key in sal){print key " " sal[key]}}' > $2
