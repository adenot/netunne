<config>
http_port 808{intnum}
icp_port 313{intnum}
userid  proxy
logfile /var/log/oops/oops-{int}.log
accesslog       /var/log/oops/access-{int}.log
pidfile         /var/run/oops/oops-{int}.pid
statistics      /var/run/oops/oops_statfile-{int}
mem_max         64m
# memory cache
lo_mark         {memcachesize}m
default-expire-value    7
ftp-expire-value        7
max-expire-value        30
last-modified-factor    5
default-expire-interval 1
icp_timeout     1000
disk-low-free   3
disk-ok-free    5
force_http11
always_check_freshness
force_completion 75
# maximo tamanho pros objetos
maxresident     {objectsize}m
insert_x_forwarded_for  yes
insert_via              yes
fetch_with_client_speed yes
acl     MSIE            header_substr   user-agent MSIE
acl     ADMINS          src_ip          127.0.0.1
acl     PURGE           method          PURGE
acl     CONNECT         method          CONNECT
acl     SSLPORT         port            443
acl_deny PURGE !ADMINS
acl_deny CONNECT !SSLPORT
stop_cache      ?
stop_cache      cgi-bin
local-networks  10/8 192.168/16

acl		INTERNAL		src_ip		include:/etc/oops/ips_internal

group   world   {
        networks        0/0;
        badports        [0:79],110,138,139,513,[6000:6010];
        http {
                deny    dstdomain * ;
        }
        icp {
                deny    dstdomain * ;
        }
}

group users {
        networks_acl 	INTERNAL;
		miss            allow;
		redir_mods      transparent;
		connect-from 	10.95.194.{intnum};
        http {
                allow dstdomain * ;
        }

}


storage {
        path /NEXUS/nexus/core/data/cache-{int}-{cachesize} ;
        # tamanho do cache
        size {cachesize}m ;
}
module err {
        template /etc/oops/err_template.html;
        lang us;
}

module transparent {
	myport 808{intnum};
}


</config>
<internal>
{internalnetwork}

</internal>
<init>
#!/bin/bash

killall -9 oops

iptables -t mangle -F routeproxy
iptables -t filter -F inputproxy

{ints}ifconfig eth{intnum}:79991 10.95.194.{intnum} netmask 255.255.255.255

{ints}if [ ! -f /NEXUS/nexus/core/data/cache-eth{intnum}-{cachesize} ]; then rm -fr /NEXUS/nexus/core/data/cache-eth{intnum}-*; /usr/sbin/oops -c/etc/oops/oops-eth{intnum}.cfg -z; fi
{ints}/usr/sbin/oops -xACDFHINS -c/etc/oops/oops-eth{intnum}.cfg -d

{ints}iptables -t filter -I inputproxy -p tcp --dport 808{intnum} -j ACCEPT

{ints}iptables -t mangle -A routeproxy -s 10.95.194.{intnum} -j ROUTE --oif eth{intnum} --gw {gw} --continue

</init>
<clean>
/bin/rm -fr $NEXUS/core/data/cache-eth*
/bin/sh $NEXUS/core/bin/scripts/exec.sh /bin/bash /etc/oops/oops.init
</clean>
<install>
/bin/sh $NEXUS/core/bin/scripts/exec.sh /usr/bin/rpl testing stable /etc/apt/sources.list
/usr/bin/yes ''|DEBIAN_FRONTEND=noninteractive /usr/bin/apt-get --force-yes update -y -f
/usr/bin/yes ''|DEBIAN_FRONTEND=noninteractive /usr/bin/apt-get --force-yes install oops -y -f
/bin/sh $NEXUS/core/bin/scripts/exec.sh /usr/sbin/update-rc.d -f oops remove
/bin/sh $NEXUS/core/bin/scripts/exec.sh /usr/bin/killall -9 oops
/usr/bin/yes ''|DEBIAN_FRONTEND=noninteractive /usr/bin/dpkg --configure -a
</install>
<maps>
	<map>
		<from>proxy/oops-*.cfg</from>
		<to>/etc/oops/</to>
	</map>
	<map>
		<from>proxy/oops.init</from>
		<to>/etc/oops/</to>
	</map>
	<map>
		<from>proxy/ips_internal</from>
		<to>/etc/oops/</to>
	</map>
</maps>
<act>
	<post>
		sh $NEXUS/core/bin/scripts/exec.sh /bin/bash /etc/oops/oops.init
	</post>
</act>
