<waitroutes>
	IFACES="{ifaces}"
	IFS=" "
	C=0
	while true
	do
		echo waiting for $IFACES >> /tmp/waitroutes.log
		if [ $C = 3 ]; then
			break
		fi
		C=$((C+1))
		NEWIFACES=""
		for i in $IFACES
		do
			if [ "a$i" = "a " ]; then
				continue
			fi
			TMP=`ifconfig $i 2>&1|grep "Device not found"`
			if [ ! -z "$TMP" ]; then
				NEWIFACES="$i $NEWIFACES"
			else
				route add default $i
			fi
		done
		IFACES=$NEWIFACES
		if [ a$NEWIFACES = a ]; then
			break
		fi
		sleep 5
	done
	
	echo "$IFACES" > /tmp/nx_ppp.alert
	
	sh $NEXUS/core/bin/scripts/exec.sh /usr/bin/php5.0 $NEXUS/core/nlib/task_network.nx
	#sh $NEXUS/core/bin/scripts/exec.sh sh $NEXUS/core/out/routes.sh
</waitroutes>
<arpwatch>
ARGS="-N -f /tmp/nx_arp.dat -p -R 30 -i {int} -s /etc/nexus/bin/arpchange.sh"
RUNAS=""
</arpwatch>
<ddclient>
pid=/var/run/ddclient.pid
protocol=dyndns2
use=web,      web=checkip.dyndns.org/,       fw-skip='IP Address'
server=members.dyndns.org
login={login}
password={password}
{domains}
</ddclient>

<resolvconf>
nameserver {nameserver}
{hassearch} search {search}
</resolvconf>
<global>
auto lo
iface lo inet loopback

{iface}
</global>
<dslprovider>
noipdefault
usepeerdns
defaultroute
hide-password
lcp-echo-interval 20
lcp-echo-failure 3
connect /bin/true
noauth
persist
mtu 1492
noaccomp
default-asyncmap
plugin rp-pppoe.so
{device}
user "{dsluser}"
unit {unit}
</dslprovider>
<pppoe>
require-chap
logfile /var/log/pppd.log
debug
{dnss}ms-dns {dns}
proxyarp
ktune
</pppoe>
<pppoeserver>
killall pppoe-server
{interfaces}pppoe-server -I {interface} -N 300 -o 10 -C {cname} -L {localip} {servicename}
</pppoeserver>
<dynamic>
	<internal>
		auto {device}
		iface {device} inet dhcp
			pre-up ifconfig {device} down
	</internal>
	<external>
		auto {device}
		iface {device} inet dhcp
			pre-up ifconfig {device} down
	</external>
</dynamic>
<dsl>
	<external>
		auto {device}
		iface {device} inet manual
		auto dsl-provider-{device}
		iface dsl-provider-{device} inet ppp
			provider dsl-provider-{device}
	</external>
</dsl>
<static>
	<internal>
		auto {device}
		iface {device} inet static
			address {address}
			netmask {netmask}
			network {network}
			broadcast {broadcast}
			pre-up ifconfig {device} down
			pre-up if test -f /var/run/dhclient.{device}.pid ; then kill `cat /var/run/dhclient.{device}.pid`; rm -fr /var/run/dhclient.{device}.* ; fi
	</internal>
	<external>
		auto {device}
		iface {device} inet static
			address {address}
			netmask {netmask}
			network {network}
			broadcast {broadcast}
			{hasgateway}gateway {gateway}
			pre-up ifconfig {device} down
			pre-up if test -f /var/run/dhclient.{device}.pid ; then kill `cat /var/run/dhclient.{device}.pid`; rm -fr /var/run/dhclient.{device}.* ; fi
	</external>
</static>
<disable>
ifconfig {device} down
{hasip} ip addr del {ip} dev {device}
</disable>
<maps>
	<map>
		<from>network/interfaces</from>
		<to>/etc/network/interfaces</to>
	</map>
	<map>
		<from>network/ddclient.conf</from>
		<to>/etc/ddclient.conf</to>
	</map>
	<map>
		<from>network/resolv.conf.nx</from>
		<to>/etc/resolv.conf.nx</to>
	</map>
	<map>
		<from>network/pppoe-server-options</from>
		<to>/etc/ppp/</to>
	</map>
	<map>
		<from>network/pserver</from>
		<to>/etc/ppp/</to>
	</map>
	<map>
		<from>network/dsl-provider-*</from>
		<to>/etc/ppp/peers</to>
	</map>
</maps>
<act>
	<pre>
		touch /tmp/nx_arp.dat
		rm -fr /etc/ppp/peers/dsl-provider-*
		rm -fr /etc/ddclient.conf
	</pre>
	<post>
		sh $NEXUS/core/bin/scripts/exec.sh sh $NEXUS/core/out/network/disable.sh
		sh $NEXUS/core/bin/scripts/exec.sh /etc/init.d/networking restart
		sh $NEXUS/core/bin/scripts/exec.sh /usr/bin/php5.0 $NEXUS/core/nlib/task_network.nx
		chmod +x /etc/ppp/pserver
		sh $NEXUS/core/bin/scripts/exec.sh sh /etc/ppp/pserver
		sh $NEXUS/core/bin/scripts/exec.sh sh $NEXUS/core/bin/scripts/ddclient.sh
		sh /tmp/nx_external
	</post>
</act>
