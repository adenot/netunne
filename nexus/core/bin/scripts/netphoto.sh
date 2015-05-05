#!/bin/sh

. /etc/nexus/path

OUT=$NEXUS/core/data/netphoto

#ip=`/sbin/ifconfig $INT|grep inet|awk {'print $2'}|cut -d":" -f2`
#if [ -z $ip ]; then
# estah em portugues
#        ip=`/sbin/ifconfig $INT|grep inet|awk {'print $3'}|cut -d":" -f2`
#fi

if [ $1 = "conn" ]; then
        arq=contr-`date +%Y-%m-%d.%H-%M-%S`.data
        cat /proc/net/ip_conntrack > $OUT/$arq
elif [ $1 = "arp" ]; then
        arq=arp-`date +%Y-%m-%d.%H-%M-%S`.data
        cat /proc/net/arp > $OUT/$arq
elif [ $1 = "proxy" ]; then
        arq=proxy-`date +%Y-%m-%d.%H-%M-%S`.data
        if [ -d /var/log/oops/ ]; then
	        cat /var/log/oops/access*.log > $OUT/oops-$arq
    	    for i in `ls /var/log/oops/access*.log`; do echo > $i; done
        else
	        cat /var/log/squid3/access*.log > $OUT/squid-$arq
    	    for i in `ls /var/log/squid3/access*.log`; do echo > $i; done
    	fi
fi