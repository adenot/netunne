#!/bin/bash 

. /etc/nexus/path

if [ $1 = "one" ]; then
	# um minuto
	
	# mata um possivel task_timelimit anterior
	kill -9 `cat /tmp/nx_timelimit.pid`
	cd /$NEXUS/core/nlib
	declare -x NEXUS=$NEXUS;$PHP -q /$NEXUS/core/nlib/task_timelimit.nx 2>&1 >> /tmp/task.log


elif [ $1 = "five" ]; then
	# cinco minutos
	
	# mata um possivel task_timelimit anterior
	kill -9 `cat /tmp/nx_account.pid`
	
	cd /$NEXUS/core/nlib
	declare -x NEXUS=$NEXUS;$PHP -q /$NEXUS/core/nlib/task_account.nx 2>&1 >> /tmp/task.log
	declare -x NEXUS=$NEXUS;$PHP -q /$NEXUS/core/nlib/task_conncheck.nx 2>&1 >> /tmp/task.log

	/etc/nexus/bin/scripts/netphoto.sh conn
	/etc/nexus/bin/scripts/netphoto.sh arp

elif [ $1 = "fifteen" ]; then
	# quinze minutos
	cd /$NEXUS/core/nlib


elif [ $1 = "hourly" ]; then
	# por hora
	sh /etc/nexus/bin/scripts/ddclient.sh
	cd /$NEXUS/core/nlib
	echo `date` > /tmp/task.log


elif [ $1 = "daily" ]; then
	# por dia
	declare -x NEXUS=$NEXUS;$PHP -q /$NEXUS/core/nlib/task_data.nx 2>&1 >> /dev/null
	rm -fr /tmp/task.log
	rm -fr /var/log/nexustask.log
	rm -fr /var/log/*.gz
	
	cd /$NEXUS/core/nlib
	declare -x NEXUS=$NEXUS;$PHP -q /$NEXUS/core/nlib/task_graph.nx 2>&1 >> /tmp/task.log
	declare -x NEXUS=$NEXUS;$PHP -q /$NEXUS/core/nlib/task_billing.nx 2>&1 >> /tmp/task.log
	sh /etc/nexus/bin/scripts/tar_netphoto.sh
	# date > /$NEXUS/core/data/log/autolicense.log
	# $PHP -q /etc/nexus/bin/nexus.sh "requestlicense" >> /$NEXUS/core/data/log/autolicense.log
	declare -x NEXUS=$NEXUS;$PHP -q /$NEXUS/core/nlib/task_license.nx 2>&1 >> /tmp/task-license.log
	declare -x NEXUS=$NEXUS;$PHP -q /$NEXUS/core/nlib/task_update.nx 2>&1 >> /tmp/task-update.log
	
	/etc/nexus/bin/scripts/netphoto.sh proxy


elif [ $1 = "weekly" ]; then
	# semanal
	cd /$NEXUS/core/nlib

fi
