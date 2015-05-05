#!/bin/sh

FILES=$(ls /tmp/pppoe-up.*)
IFS="
"
for  i in $FILES ; do
	rpl "\"" "'" $i
	/bin/sh $i
done
