#!/bin/bash

IP=$5
INT=$1

if [ -z $1 ]; then
        exit
fi
if [ -z $5 ]; then
        exit
fi

cd /etc/nexus/forward

echo $0 \"$1\" \"$2\" \"$3\" \"$4\" \"$5\" > /tmp/pppoe-up.$1

#FILES=$(grep -He ${IP} nx_forward_pppoe.* |awk -F':' '{print $1}')

#for  i in $FILES ; do
#	cp -a $i /tmp/$i
#	rpl -f "INTERFACE" "$INT" /tmp/$i
#	sh /tmp/$i
#done


/etc/nexus/bin/nexus.sh "fastauth($5,$1)"