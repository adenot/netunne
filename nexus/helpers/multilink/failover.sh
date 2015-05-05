#!/bin/bash

. network.conf

if ! ping -i 1 -c1 $P1 > /dev/null ; then
	P1OFF=1;
fi
if ! ping -i 1 -c1 $P2 > /dev/null ; then
	P2OFF=1;
fi

ATUAL=`cat /etc/gatewaystatus`

if [ $ATUAL = "P1OFF" ]; then 
	if [ $P1OFF ]; then
		exit;
	else
		PON=1;
	fi
elif [ $ATUAL = "P2OFF" ]; then
	if [ $P2OFF ]; then
		exit;
	else
		PON=1;
	fi
fi

# se um dos links cair, refazer a rota com o outro
if [ $P1OFF ]; then
	ip route delete default
	ip route add default scope global nexthop via $P2 dev $IF2 weight 1
	ip route flush cache
	echo "P1OFF" > /etc/gatewaystatus
elif [ $P2OFF ]; then
	ip route delete default
	ip route add default scope global nexthop via $P1 dev $IF1 weight 1
	ip route flush cache
	echo "P2OFF" > /etc/gatewaystatus
elif [ $PON ]; then
	ip route delete default
	ip route add default scope global nexthop via $P1 dev $IF1 weight 1 \
		nexthop via $P2 dev $IF2 weight 1
	ip route flush cache
	echo "PON" > /etc/gatewaystatus
fi

