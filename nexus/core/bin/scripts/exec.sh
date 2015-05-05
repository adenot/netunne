#!/bin/sh

. /etc/nexus/path

if [ "$1" == "ex" ]
then

	echo executando $1 $2 $3 $4 > /tmp/exec.tmp
	$2 $3 $4 $5 $6 $7 > /tmp/exec.out
	echo > /tmp/exec.end

else 
	rm -fr /tmp/exec.out
	rm -fr /tmp/exec.end
	screen -d -m $0 ex $1 $2 $3 $4 $5 $6 $7 

	while [ ! -f /tmp/exec.end ]
	do
		sleep 1
	done
	cat /tmp/exec.out

fi
