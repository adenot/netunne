#!/bin/bash

. /etc/nexus/path


RRDTOOL=/usr/bin/rrdtool
RRDDIR=/$NEXUS/core/data/rrd
IMGDIR=/$NEXUS/core/data/graph
date=date

if [ ! -d $IMGDIR ]
then
	exit 1;
fi

if [ -z $1 ]
then
	exit 1;
fi

if [ ! -e $RRDDIR/$1.rrd ]
then
	exit 1;
fi

RRDDB=$RRDDIR/$1.rrd

create_graph() {
	$RRDTOOL graph $1 -s $2 -a PNG\
	-v "Upload    Download (bps)" \
	-w 600 -h 180 \
	-E \
	-A \
	-t "$3" \
	DEF:out_bytes=${RRDDB}:out:AVERAGE \
	DEF:in_bytes=${RRDDB}:in:AVERAGE \
	CDEF:out_bits=out_bytes,8,* \
	CDEF:in_bits=in_bytes,8,* \
	CDEF:in_neg_bits=in_bits,-1,* \
	COMMENT:"              Máx\t\t\tAverage\t Last\n" \
	LINE5:out_bits#001188 \
	AREA:out_bits#3377ff:"Download\t" \
	GPRINT:out_bits:MAX:"%6.2lf %sbps\t" \
	GPRINT:out_bits:AVERAGE:"%6.2lf %sbps\t" \
	GPRINT:out_bits:LAST:"%6.2lf %sbps\n" \
	LINE5:in_neg_bits#440044:"" \
	AREA:in_neg_bits#aa44aa:"Upload\t" \
	GPRINT:in_bits:MAX:"%6.2lf %sbps\t" \
	GPRINT:in_bits:AVERAGE:"%6.2lf %sbps\t" \
	GPRINT:in_bits:LAST:"%6.2lf %sbps\n" \
	HRULE:0#000000 \
	VRULE:$($date -d "$($date +%m/%d/%y) 00:00" "+%s")#ff0000
}	

create_graph $IMGDIR/$1-last-day.png -1d 'last day'
create_graph $IMGDIR/$1-last-week.png -1w 'last week'
create_graph $IMGDIR/$1-last-month.png -1m 'last month'
create_graph $IMGDIR/$1-last-year.png -1y 'last year'
