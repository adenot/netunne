#!/bin/sh

. /etc/nexus/path

chown -R www-data:www-data $NEXUS/interface/conf
chown -R www-data:www-data $NEXUS/interface/www
chown -R www-data:www-data $NEXUS/interface/userauth
#chown -R www-data:www-data $NEXUS/core/data/log
chmod 777 -R $NEXUS/core/data/log


if [ ! -h $NEXUS/interface/www/graph ]
then
	ln -s $NEXUS/core/data/graph $NEXUS/interface/www/graph
fi

if [ -e $NEXUS/core/data/userthemes/custom ]
then
	chmod 777 $NEXUS/core/data/userthemes/custom
fi

