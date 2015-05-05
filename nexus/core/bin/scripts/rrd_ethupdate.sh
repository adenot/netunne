#!/bin/bash

# usage: rrd_ethupdate interface bytes_in bytes_out

. /etc/nexus/path

RRDUPDATE=/usr/bin/rrdupdate
RRDDIR=/$NEXUS/core/data/rrd/

$RRDUPDATE $RRDDIR/$1.rrd N:$2:$3  2>&1 >> /tmp/task.log