<config>
cache_dir ufs /var/spool/squid3-{int} {cachesize} 16 256
# cache_mem {memcachesize} MB
error_directory /etc/squid3/errors/

http_port 808{intnum} transparent
icp_port 313{intnum}

tcp_outgoing_address 10.95.194.{intnum}

access_log /var/log/squid3/access-{int}.log
cache_log /var/log/squid3/cache-{int}.log
pid_filename /var/run/squid3-{int}.pid

hierarchy_stoplist cgi-bin ?
acl QUERY urlpath_regex cgi-bin \?
cache deny QUERY
refresh_pattern ^ftp:           1440    20%     10080
refresh_pattern ^gopher:        1440    0%      1440
refresh_pattern .               0       20%     4320
acl all src 0.0.0.0/0.0.0.0
acl manager proto cache_object
acl localhost src 127.0.0.1/255.255.255.255
acl to_localhost dst 127.0.0.0/8
acl SSL_ports port 443
acl Safe_ports port 80          # http
acl Safe_ports port 21          # ftp
acl Safe_ports port 443         # https
acl Safe_ports port 70          # gopher
acl Safe_ports port 210         # wais
acl Safe_ports port 1025-65535  # unregistered ports
acl Safe_ports port 280         # http-mgmt
acl Safe_ports port 488         # gss-http
acl Safe_ports port 591         # filemaker
acl Safe_ports port 777         # multiling http
acl CONNECT method CONNECT
{acls}acl {aclname} {acltype} {aclvalue}


{httpaccess}http_access {action} {acl_list}

http_access allow manager localhost
http_access deny manager
http_access deny !Safe_ports
http_access deny CONNECT !SSL_ports

http_access allow localhost

http_access deny all

http_reply_access allow all

icp_access allow all

coredump_dir /var/spool/squid3
</config>
<cache>
#!/bin/bash

if [ "$1" == "clean" ]; then
	killall -9 /usr/sbin/squid3
	umount /NEXUS/nexus/core/data/squid-cache-{int}
	
	rm -fr /NEXUS/nexus/core/data/squid-cache-{int}-*
fi


# se nao existe, tenho q parar o squid e criar
if [ ! -f /NEXUS/nexus/core/data/squid-cache-{int}-{cachesize2} ]; then

	#apagando cache antigo
	rm -fr /NEXUS/nexus/core/data/squid-cache-{int}-*

	mkdir -p /NEXUS/nexus/core/data/squid-cache-{int}


	#criando a imagem
	dd if=/dev/zero of=/NEXUS/nexus/core/data/squid-cache-{int}-{cachesize2} bs=1M count={cachesize2}
	yes|mkfs.ext3 /NEXUS/nexus/core/data/squid-cache-{int}-{cachesize2}
	rm -fr /var/spool/squid3-{int}
	ln -s /NEXUS/nexus/core/data/squid-cache-{int}/ /var/spool/squid3-{int}
	
	#montando
	mount -t ext3 -oloop /NEXUS/nexus/core/data/squid-cache-{int}-{cachesize2} /NEXUS/nexus/core/data/squid-cache-{int}/
	chown -R proxy.proxy /NEXUS/nexus/core/data/squid-cache-{int}/
	
	#dizendo pro squid criar o cache
	/usr/sbin/squid3 -z -f /etc/squid3/squid-{int}.conf
	
fi

if [ ! -d /NEXUS/nexus/core/data/squid-cache-{int}/lost+found ]; then
	mount -t ext3 -oloop /NEXUS/nexus/core/data/squid-cache-{int}-{cachesize2} /NEXUS/nexus/core/data/squid-cache-{int}/		
fi
#cachesize precisa ser 15% maior q o tamanho q vai ser usado no squid

{ints}touch /var/spool/squid3-{int}/swap.state
{ints}chown proxy /var/spool/squid3-{int}/swap.state


</cache>
<init>

killall -9 /usr/sbin/squid3

{ints}ifconfig eth{intnum}:79991 10.95.194.{intnum} netmask 255.255.255.255

#desmontando tudo q conseguir
umount /NEXUS/nexus/core/data/squid-cache-*

{ints}sh $NEXUS/core/bin/scripts/exec.sh /bin/bash /etc/squid3/cache-{int}.init

{ints}/usr/sbin/squid3 -D -sYC -f /etc/squid3/squid-{int}.conf

{ints}iptables -t filter -I inputproxy -p tcp --dport 808{intnum} -j ACCEPT

{ints}iptables -t mangle -A routeproxy -s 10.95.194.{intnum} -j ROUTE --oif {int} --gw {gw} --continue

</init>
<reload>
killall -HUP squid3

</reload>
<maps>
	<map>
		<from>proxy/squid-*.conf</from>
		<to>/etc/squid3/</to>
	</map>
	<map>
		<from>proxy/cache-*.init</from>
		<to>/etc/squid3/</to>
	</map>
	<map>
		<from>proxy/squid.init</from>
		<to>/etc/squid3/</to>
	</map>
	<map>
		<from>proxy/squid.reload</from>
		<to>/etc/squid3/</to>
	</map>
</maps>
<act>
	<pre>
		iptables -t mangle -F routeproxy
		iptables -t filter -F inputproxy
	</pre>
	<post>

		chmod +x /etc/squid3/squid.init
		chmod +x /etc/squid3/cache-*.init
		
		sh $NEXUS/core/bin/scripts/exec.sh /bin/bash /etc/squid3/squid.init
		
		# if [ -e /var/run/squid3.pid ]; then if [ "$(ps xa|grep `cat /var/run/squid3.pid`|grep squid)A" == "A" ]; then rm -fr /var/run/squid3.pid;/etc/init.d/squid3 start; fi; else /etc/init.d/squid3 start; fi
	</post>
</act>
