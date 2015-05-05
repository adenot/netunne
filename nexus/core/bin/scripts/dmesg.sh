#!/bin/sh

. /etc/nexus/path

if [[ -z "$1" ]]; then
        LOGFILE=/var/log/dmesg.log   
else
        LOGFILE=$1
fi

TMPFILE=/var/log/dmesg-tmp.log
DATADISK=`cat /etc/nexusdatadisk`

TMPARP=/tmp/nx_arp.tmp
ARPDAT=/tmp/nx_arp.dat

if [[ -z "$2" ]]; then
    DELAY=5
else
    DELAY=$2
fi

date > /var/log/dmesg-boot.log
echo >> /var/log/dmesg-boot.log
dmesg -c >> /var/log/dmesg-boot.log

while (true); do

	# verifico se o lighttpd caiu, se caiu chamo de novo
	[ "A`ps xa|grep lighttpd|grep -v grep`" == "A" ] && /etc/init.d/lighttpd start
	# verificando se a lo tah ativa
	[ "A`ifconfig lo|grep 127`" == "A" ] && ifconfig lo 127.0.0.1
	# verifico se a porta 26 tah aberta
	[ "A`netstat -nlp|grep ":26"|grep inetd`" == "A" ] && /etc/init.d/inetd stop && /etc/init.d/inetd start
	
	# verifico se a particao de dados estah somente leitura
	echo 1 > /mnt/$DATADISK/.temp
	if [ ! -f /mnt/$DATADISK/.temp ]; then
		umount /mnt/$DATADISK
		fsck.vfat -a /dev/$DATADISK
		mount -o uid=33 /mnt/$DATADISK
	else
		rm -fr /mnt/$DATADISK/.temp
		if [ -f /mnt/$DATADISK/.temp ]; then
			umount /mnt/$DATADISK
			fsck.vfat -a /dev/$DATADISK
			mount -o uid=33 /mnt/$DATADISK
		fi
	fi

    #dmesg -c | tee -a ${TMPFILE}
    dmesg -c >> ${TMPFILE}

    cat ${TMPFILE} >> ${LOGFILE}

    if [ -s ${TMPFILE} ]; then
        #arquivo mudou
		declare -x NEXUS=$NEXUS;$PHP -q /$NEXUS/core/nlib/task_sysmonitor.nx 2>&1 >> /tmp/task.log
    fi

    echo -n > ${TMPFILE}
    
    if [ -f /tmp/nx_internal ]; then
		grep `cat /tmp/nx_internal` /proc/net/arp > ${TMPARP}
		if [ "A`diff ${TMPARP} ${ARPDAT}`" != "A" ]; then 
			declare -x NEXUS=$NEXUS;$PHP -q /$NEXUS/core/nlib/task_arpchange.nx
		fi
	fi
    

    sleep ${DELAY}
done
