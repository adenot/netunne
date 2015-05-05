#!/bin/bash

# usage: rrd_update interface ip arquivo

# 1 = interface
# 2 = ip
# 3 = login do usuario 

echo "rrd_update parametros: $1 $2" >> /tmp/task.log

. /etc/nexus/path

RRDUPDATE=/usr/bin/rrdupdate
RRDDIR=/$NEXUS/core/data/rrd/

ACCOUNT=/proc/net/ipt_account/$3
LOGIN=$3
IP=$2

# CINCO MINUTOS !! NAO PODE SER EXECUTADO MAIS NEM MENOS SE NAO ALTERAR AQUI
TIMEOUT=300

CAT=/bin/cat
AWK=/usr/bin/awk
ECHO=/bin/echo

# 1  2 3             4         5 6 7           8 9 10        111213           141516   1718
# ip = 192.168.0.255 bytes_src = 0 packets_src = 0 bytes_dest = 0 packets_dest = 0 time = 543

grep "$IP" $ACCOUNT | $AWK -v OUT=$LOGIN -v TIMEOUT=$TIMEOUT -v RRDUPDATE=$RRDUPDATE -v RRDDIR=$RRDDIR '{ if (TIMEOUT - $18 > 0) system (RRDUPDATE " " RRDDIR "/" OUT ".rrd" " N:" $6 ":" $12) }'  2>&1 >> /tmp/task.log

