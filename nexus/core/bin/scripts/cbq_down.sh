#!/bin/bash

IP=$5
INT=$1

echo $INT

rm -fr /tmp/pppoe-up.$1

#cd /etc/shaper/
#FILES=$(grep -H $INT *|awk -F':' '{print $1}')
#for  i in $FILES ; do
#        rm -fr $i
#done


/etc/nexus/bin/nexus.sh "pppoedisconnect($5,$1)"

