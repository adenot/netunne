<clean>
	<command>
		iptables -t filter -F
		iptables -t mangle -F
		iptables -t nat -F
		iptables -t filter -X act_logdrop
		iptables -t filter -X act_drop
		iptables -t filter -X act_logallow
		iptables -t filter -X act_allow
		
		iptables -t filter -X guests
		iptables -t filter -X guests
		iptables -t mangle -X guests
		iptables -t mangle -X guestsacl
		
		iptables -t filter -X inputproxy
		iptables -t filter -X forwarduser
		iptables -t filter -X outputuser
		iptables -t mangle -X forwarduser
		iptables -t mangle -X outputuser
		iptables -t mangle -X routeuser
		iptables -t mangle -X routefix
		iptables -t mangle -X routeproxy
	</command>
</clean>
<global>
	<command>
		echo 1 > /proc/sys/net/ipv4/ip_forward
		modprobe ip_nat_ftp
		modprobe ip_nat_pptp
		modprobe ip_nat_irc
		modprobe ip_nat_amanda
		modprobe ip_nat_tftp
		
		iptables -t filter -N inputproxy
		iptables -t filter -N forwarduser
		iptables -t filter -N outputuser
		iptables -t mangle -N forwarduser
		iptables -t mangle -N outputuser
		iptables -t mangle -N routeuser
		iptables -t mangle -N routefix
		iptables -t mangle -N routeproxy
		
		iptables -t filter -N guests
		iptables -t filter -N guests
		iptables -t mangle -N guests
		iptables -t mangle -N guestsacl
		
		iptables -t filter -N act_logdrop
		iptables -t filter -N act_drop
		iptables -t filter -N act_allow
		iptables -t filter -N act_logallow
		
		iptables -t filter -A act_logallow -j LOG
		iptables -t filter -A act_logallow -j act_allow

		iptables -t filter -A act_logdrop -j LOG
		iptables -t filter -A act_logdrop -j act_drop
		
		iptables -t filter -A act_allow -j ACCEPT
		
		iptables -t filter -A act_drop -j DROP
		
		iptables -t filter -I INPUT -i ppp+ -j ACCEPT
		
		iptables -t filter -I INPUT -j inputproxy

		iptables -t filter -I FORWARD \
			-m condition --condition noforward \
			-j DROP
		
		iptables -t filter -I INPUT -i lo -j ACCEPT
		iptables -t filter -I INPUT -m state --state ESTABLISHED,RELATED -j ACCEPT
		iptables -t filter -I INPUT -p icmp --icmp-type echo-request -m state --state NEW,ESTABLISHED,RELATED -j ACCEPT
		
		iptables -t nat -A PREROUTING -i ppp+ -j RETURN

		iptables -t nat -A PREROUTING \
			-p tcp --dport 80 -m mark --mark 9999 \
			-j REDIRECT --to-port 3080
	
		iptables -t filter -I FORWARD -p tcp --dport 53 -j ACCEPT
		iptables -t filter -I FORWARD -p udp --dport 53 -j ACCEPT
		iptables -t filter -I FORWARD -p tcp --sport 53 -j ACCEPT
		iptables -t filter -I FORWARD -p udp --sport 53 -j ACCEPT

		iptables -t mangle -A PREROUTING -j MARK --set-mark 9999
		
		iptables -t mangle -A PREROUTING -j guests
		iptables -t mangle -I FORWARD -m mark --mark 9998 -j guestsacl
		iptables -t mangle -I FORWARD -j guests	
		

		iptables -t mangle -A OUTPUT -j routeproxy
		iptables -t mangle -A OUTPUT -j routefix

		iptables -t mangle -A FORWARD -j routeuser
		
		iptables -t filter -A FORWARD -j forwarduser
		
		iptables -t mangle -A FORWARD -j forwarduser

		iptables -t filter -A FORWARD -j guests
		
		iptables -t filter -A OUTPUT -j outputuser
		iptables -t mangle -A OUTPUT -j outputuser
		
		iptables -t filter -P INPUT DROP
		iptables -t filter -P FORWARD DROP
		
	</command>
</global>
<services>
	<service>
		<id>dhcp</id>
		<command>
			iptables -t filter -A INPUT -i {interface} -p udp --dport 67 -j act_{action}
			iptables -t filter -A INPUT -i {interface} -p udp -s 0.0.0.0/32 --sport 68 \
     			-d 255.255.255.255/32 --dport 67 -m state --state NEW,ESTABLISHED \
      			-j act_{action}
			iptables -t filter -A OUTPUT -o {interface} -p udp -s 0.0.0.0/32 --sport 67  \
    		   -d 255.255.255.255/32 --dport 68 -m state --state ESTABLISHED -j act_{action}
		</command>
	</service>
	<service>
		<id>dns</id>
		<command>
			iptables -t filter -i {interface} -p tcp --dport 53 -A INPUT -j act_{action}
			iptables -t filter -i {interface} -p udp --dport 53 -A INPUT -j act_{action}
		</command>
	</service>
	<service>
		<id>ssh</id>
		<command>
			iptables -t filter -i {interface} -p tcp --dport 22 -A INPUT -j act_{action}
		</command>
	</service>
	<service>
		<id>webuser</id>
		<command>
			iptables -t filter -i {interface} -p tcp --dport 3080 -A INPUT -j act_{action}
		</command>
	</service>
	<service>
		<id>webadm</id>
		<command>
			iptables -t filter -i {interface} -p tcp --dport 443 -A INPUT -j act_{action}
		</command>
	</service>
</services>
<interface>
	<command>
		iptables -t filter -I FORWARD \
			-i ppp+ -o ppp+ -s {network} -d {network} -j REJECT
		iptables -t filter -I FORWARD \
			-m account --aname {interface} --ashort --aaddr {network}
		iptables -t filter -A FORWARD \
			-d {network} -j ACCEPT
		iptables -t filter -A FORWARD -o ppp+ -j ACCEPT
	</command>
	<external>
		<command>
			iptables -t nat -I POSTROUTING -o {interface} -j MASQUERADE
			#iptables -t filter -I FORWARD -m account --aname {interface} --ashort --aaddr {ip}
			
			iptables -t mangle -I INPUT -i {interface} -m account --aname {interface} --ashort --aaddr {ip}
			iptables -t mangle -I OUTPUT -o {interface} -m account --aname {interface} --ashort --aaddr {ip}

			echo "echo 0 > /proc/sys/net/ipv4/conf/{interface}/rp_filter" >> /tmp/nx_external
		</command>
	</external>
</interface>
<traffic>
	<command>
		iptables -t filter -I FORWARD \
			{acl} \
			-m account --aname {aname} --ashort --aaddr {aaddr}
	</command>
</traffic>
<rule>
	<acl>
		<command>
			iptables -t mangle -I FORWARD \
				{acl} \
				-j MARK --set-mark {aclmark}
		</command>
	</acl>
	<acldrop>
		<command>
			iptables -t filter -I FORWARD \
				-m mark --mark {aclmark} \
				-j DROP
		</command>cccb
	</acldrop>
</rule>
<publish>
	<interface>
		<command>
			iptables -t nat -I PREROUTING -i {interface} -p {proto} -m {proto} --dport {dport} -j DNAT --to-destination {newip}:{newdport}
			iptables -t filter -A FORWARD -s {newip} -p {proto} -m {proto} --sport {newdport} -j ACCEPT
		</command>
		<remove>
			<command>
				iptables -t nat -D PREROUTING -i {interface} -p {proto} -m {proto} --dport {dport} -j DNAT --to-destination {newip}:{newdport}
			</command>
		</remove>
	</interface>
</publish>
<user>
	<command>
			iptables -t mangle -N {userchain}
			iptables -t mangle -F {userchain}
			iptables -t filter -N {userchain}
			iptables -t filter -F {userchain}
			iptables -t filter -m condition --condition {userchain} -A {userchain} -j ACCEPT	
	</command>
	<pppoefix>
		<command>
			iptables -t filter -I forwarduser -m account --aname {userchain} --ashort --aaddr {userip}
			
			iptables -t filter -I outputuser -m account --aname {userchain} --ashort --aaddr {userip}
			
			iptables -t mangle -I outputuser -d {userip} -j {userchain}
			
			iptables -t mangle -A forwarduser \
				-s {userip} -i {userint} \
				-m condition --condition {userchain} \
			 	-j {userchain}
			 	
			iptables -t mangle -A forwarduser \
				-d {userip} \
				-j {userchain}

			iptables -t filter -A forwarduser \
				-s {userip} -i {userint} \
				-m condition --condition {userchain} \
			 	-j {userchain}

			iptables -t nat -I PREROUTING \
				-i {userint} \
				-s {userip} \
				-m condition --condition {userchain} \
				-j RETURN

			echo 0 > /proc/net/ipt_condition/{userchain}
			echo 0 > /proc/net/nf_condition/{userchain}
			
			/sbin/tc qdisc del dev {userint} root
			/sbin/tc qdisc add dev {userint} root handle 1 cbq bandwidth 10Mbit avpkt 1000 cell 8
			/sbin/tc class change dev {userint} root cbq weight 1Mbit allot 1514
			
		</command>
	</pppoefix>
	<pppoeunfix>
		<command>
			iptables -t filter -D forwarduser -m account --aname {userchain} --ashort --aaddr {userip}
			
			iptables -t filter -D outputuser -m account --aname {userchain} --ashort --aaddr {userip}
			
			iptables -t mangle -D outputuser -d {userip} -j {userchain}
			
			iptables -t mangle -D forwarduser \
				-s {userip} -i {userint} \
				-m condition --condition {userchain} \
			 	-j {userchain}
			 	
			iptables -t mangle -D forwarduser \
				-d {userip} \
				-j {userchain}
				
			iptables -t filter -D forwarduser \
				-s {userip} -i {userint} \
				-m condition --condition {userchain} \
			 	-j {userchain}
			 	
			iptables -t nat -D PREROUTING \
				-i {userint} \
				-s {userip} \
				-m condition --condition {userchain} \
				-j RETURN
				
		</command>
	</pppoeunfix>
	<fix>
		<command>
			iptables -t filter -I forwarduser -m account --aname {userchain} --ashort --aaddr {userip}
			
			iptables -t filter -I outputuser -m account --aname {userchain} --ashort --aaddr {userip}
			
			iptables -t mangle -I outputuser -d {userip} -j {userchain}
						
			iptables -t mangle -A forwarduser \
				-s {userip} -m mac --mac-source {usermac} \
				-m condition --condition {userchain} \
			 	-j {userchain}
			 	
			iptables -t mangle -A forwarduser \
				-d {userip} \
				-j {userchain}

			iptables -t filter -A forwarduser \
				-s {userip} -m mac --mac-source {usermac} \
				-m condition --condition {userchain} \
			 	-j {userchain}

			iptables -t nat -I PREROUTING \
				-m mac --mac-source {usermac} \
				-s {userip} \
				-m condition --condition {userchain} \
				-j RETURN

			echo 0 > /proc/net/ipt_condition/{userchain}
			echo 0 > /proc/net/nf_condition/{userchain}
		</command>
	</fix>
	<unfix>
		<command>
			iptables -t filter -D forwarduser -m account --aname {userchain} --ashort --aaddr {userip}
			
			iptables -t filter -D outputuser -m account --aname {userchain} --ashort --aaddr {userip}
			
			iptables -t mangle -D outputuser -d {userip} -j {userchain}
			
			iptables -t mangle -D forwarduser \
				-s {userip} -m mac --mac-source {usermac} \
				-m condition --condition {userchain} \
			 	-j {userchain}
			 	
			iptables -t mangle -D forwarduser \
				-d {userip} \
				-j {userchain}
				
			iptables -t filter -D forwarduser \
				-s {userip} -m mac --mac-source {usermac} \
				-m condition --condition {userchain} \
			 	-j {userchain}
			 	
			iptables -t nat -D PREROUTING \
				-m mac --mac-source {usermac} \
				-s {userip} \
				-m condition --condition {userchain} \
				-j RETURN
				
		</command>
	</unfix>
	<route>
		<command>
			iptables -t mangle -A routeuser -s {userip} -j ROUTE --gw {gateway} --oif {int} --continue
			iptables -t mangle -I {userchain} -m account --aname {int} --ashort --aaddr {intip}/255.255.255.255
			echo 1 > /proc/net/ipt_condition/{userchain}
			echo 1 > /proc/net/nf_condition/{userchain}

		</command>
	</route>
	<unroute>
		<command>
			iptables -t mangle -D routeuser -s {userip} -j ROUTE --gw {gateway} --oif {int} --continue
			iptables -t mangle -D {userchain} -m account --aname {int} --ashort --aaddr {intip}/255.255.255.255
			echo 0 > /proc/net/ipt_condition/{userchain}
			echo 0 > /proc/net/nf_condition/{userchain}
		</command>
	</unroute>
	<proxy>
		<command>
			iptables -t nat -I PREROUTING -s {userip} -p tcp --dport 80 -j REDIRECT --to-ports 808{intnum}
		</command>
	</proxy>
	<unproxy>
		<command>
			iptables -t nat -D PREROUTING -s {userip} -p tcp --dport 80 -j REDIRECT --to-ports 808{intnum}
		</command>
	</unproxy>
	<noauth>
		<command>echo 1 > /proc/net/ipt_condition/{userchain};echo 1 > /proc/net/nf_condition/{userchain}</command>
	</noauth>
	<authremove>
		<command>echo 0 > /proc/net/ipt_condition/{userchain};echo 0 > /proc/net/ipt_condition/{userchain}</command>
	</authremove>
	<mac>
		<command>

			iptables -t mangle -I FORWARD \
				-d {userip} \
				-j {userchain}
			iptables -t filter -I FORWARD \
				-s {userip} -m mac --mac-source {usermac} \
				- j DROP
			iptables -t filter -I FORWARD \
				-s {userip} -m mac --mac-source {usermac} \
				-m condition --condition {userchain} \
				-j {userchain}
			iptables -t filter -I FORWARD \
				-d {userip} \
				-j {userchain}
			iptables -t nat -A PREROUTING \
				-i {userint} -p tcp --dport 80 \
				-m mac --mac-source {usermac} \
				-s {userip} \
				-m condition --condition ! {userchain} \
				-j REDIRECT --to-port 3080
			iptables -t nat -A PREROUTING \
				-i {userint} \
				-m mac --mac-source {usermac} \
				-s {userip} \
				-m condition --condition {userchain} \
				-j RETURN
		</command>
	</mac>
	<acl>
		<command>
			iptables -t mangle -I {userchain} \
				{acl} \
				-j MARK --set-mark {aclmark}
		</command>
	</acl>
	<preacl>
		<command>
			iptables -t mangle -I {userchain} \
			-m mark --mark {aclmark} \
			-j RETURN
		</command>
	</preacl>
	<acldrop>
		<command>
			iptables -t filter -I {userchain} \
				-m mark --mark {aclmark} \
				-j DROP
		</command>
	</acldrop>
</user>
<guest>
	<command>
		iptables -t mangle -N {userchain}
		iptables -t filter -N {userchain}
		iptables -t filter -I {userchain} -j ACCEPT
	</command>
	<global>
		<command>
		iptables -t filter -A guestsacl -j ACCEPT
		</command>
	</global>
	<add>
		<command>
			iptables -t mangle -A forwarduser \
				-s {userip} \
			 	-j {userchain}
			iptables -t mangle -A forwarduser \
				-d {userip} \
				-j {userchain}
			iptables -t filter -A forwarduser \
				-m mac --mac-source {usermac}
				-s {userip} \
			 	-j {userchain}
			iptables -t nat -I PREROUTING \
				-s {userip} \
				-j RETURN
		</command>
	</add>
	<remove>
		<command>
			iptables -t mangle -D forwarduser \
				-s {userip} \
			 	-j {userchain}
			iptables -t mangle -D forwarduser \
				-d {userip} \
				-j {userchain}
			iptables -t filter -D forwarduser \
				-s {userip} \
			 	-j {userchain}
			iptables -t nat -D PREROUTING \
				-s {userip} \
				-j RETURN
		</command>
	</remove>
</guest>	
<acl>
	<type>proto</type>
	<command>-p {proto}</command>
</acl>
<acl>
	<type>src</type>
	<command>-s {src}</command>
</acl>
<acl>
	<type>dst</type>
	<command>-d {dst}</command>
</acl>	
<acl>
	<type>dport</type>
	<command>--dport {dport}</command>
</acl>
<acl>
	<type>sport</type>
	<command>--sport {sport}</command>
</acl>
<acl>
	<type>layer7</type>
	<command>-m layer7 --l7proto {layer7}</command>
</acl>
<acl>
	<type>timestart</type>
	<command>--timestart {timestart}</command>
</acl>
<acl>
	<type>timestop</type>
	<command>--timestop {timestop}</command>
</acl>
<acl>
	<type>days</type>
	<command>--days {days}</command>
</acl>
<acl>
	<type>time</type>
	<command>-m time</command>
</acl>
<time>
	<command>-m time --timestart {timestart} --timestop {timestop} --days {days}</command>
</time>
<route>
	<command>iptables -t mangle -A routeuser -m mark --mark {aclmark} -j ROUTE --gw {gw} --oif {out} --continue</command>
</route>
<aclstemplate>
	<template>
		<name>MSN</name>
		<id>msn</id>
		<acls>
			<acl>
				<sitelistname>MSN</sitelistname>
				<block>{file1}</block>
				<drop></drop>
			</acl>
			<acl>
				<service>`SERVICE.msnmessenger`</service>
				<drop></drop>
			</acl>
		</acls>
		<files>
			<file>
				<name>file1</name>
				<content>gateway/gateway.dll</content>
			</file>
		</files>
	</template>
	<template>
		<name>Web Messengers</name>
		<id>webmsn</id>
		<acls>
			<acl>
				<sitelistname>Webmsn</sitelistname>
				<block>{file1}</block>
				<ipblock>{file2}</ipblock>
				<drop></drop>
			</acl>
		</acls>
		<files>
			<file>
				<name>file1</name>
				<content>onlinemessenger.nl
e-messenger.net  
jabber.meta.net.nz/webmsg/register.php
linux.mty.itesm.mx/jabber/chat/
msn.audiowatcher.com
msn2go.com.br
webmessenger.msn.com
iloveim.com
imaginarlo.com
mangeloo.com
mastaline.com
messenger-online.com/emessenger.php
msn2go.com/
phonefox.com
researchhaven.com/Chat.htm
wbmsn.net
web2messenger.com 
meebo.com 
iloveim.com
messenger.msn.com
messenger.hotmail.com
webmessenger.com
icq.com
centova.net
ebuddy.com
communicationtube.net
koolim.com
messengerfx.com
express.instan-t.com/MyIM/start.htm
imunitive.com
radiusim.com
polysolve.com
wablet.com
jwchat.org</content>
			</file>
			<file>
				<name>file2</name>
				<content>194.109.193.91 
203.97.93.14
131.178.5.153
193.238.160.0/24
64.92.172.107
193.238.160.68
65.54.239.142
72.29.84.79
82.98.135.43
216.32.66.234
83.172.138.12
87.239.8.21
65.163.27.2
193.238.160.84
216.32.66.0/24
72.21.0.0/16  
64.92.172.108
216.32.84.0/24
72.36.146.0/24
216.129.112.0/24
69.36.226.0/24
70.85.188.186
66.226.14.81</content>
			</file>
		</files>
	</template>
	<template>
		<name>Anonymizers</name>
		<id>anonymizer</id>
		<acls>
			<acl>
				<sitelistname>proxy</sitelistname>
				<block>{file1}</block>
				<ipblock>{file2}</ipblock>
				<drop></drop>
			</acl>
		</acls>
		<files>
			<file>
				<name>file1</name>
				<content>vivovantagens.cjb.net
browseatwork.com 
ibypass.com   
guardster.com
hidemyass.com 
pimpmyip.com 
vtunnel.com
polysolve.com
cgi-proxy.net
hidemyass.com
proxyfi.com
anonym.to
anonynizer.com
web_proxy.cgi
unipeak.com 
hrmovie.com
kproxy.com
anonymouse.ws
megaproxy.com
3proxy.com
vtunnel.com
spynot.com
fsurf.com
proxy.org
webproxy.kaxy.com
browseatwork.com
anonymizer.com
the-cloak.com
guardster.com
proxify.com
freeproxy.ru
anonymouse.ws
proxyking.net
anonymousindex.com
proxy.cgi
proxy.com
proxy.net
proxy.org
proxy.com.br
englishtunnel.com
calculatepie.com
safehazard.com
tunnel.com
backfox.com
a81.info
bigkitty.info
browseblocked.com
deob.net
eeddit.com
elanceconnect.com
hideidentity.info
hideme.de
hugefatman.info
iunknown.net
matrixprox.info
mzvb.com
no-oil.com
passmyass.info
proxy2surf.com
proxystorm.com
roccoproxy.info
safeproxie.com
safepillage.com
securepillage.com
uck.in
yearbooksurf.com
10dir.com
474.cc
645.cc
911surf.info
access24h.com
allfreehere.info
anonymousfreeproxy.com
youhide.com
anonymousproxyonline.com
proxyfirst.com
anonymouse.org
autobypass.com
anonymous
bestmyspaceunblock.info
browsethe.net
browse24h.com
browserunblocker.com
cantfilter.us
circumsurf.com
cloak-me.info
diglet.org
djedi.biz
exproxy.info
facespaceweb.com
fluffu.com
funblock.info
getpast.info
gothru.info
instantbypass.com
ipfrogs.com
ipsite.net
mootzone.com
myproxybox.com
proxy.pl</content>
			</file>
			<file>
				<name>file2</name>
				<content>67.15.221.2
68.178.232.99
69.57.132.37
208.53.131.176
67.159.45.93
66.79.168.59
66.98.179.208
81.95.1.72 
207.36.225.250
207.234.209.125
62.193.226.74
217.11.54.126
72.232.196.138
67.159.45.233
63.247.81.253
64.72.123.89
74.53.126.157
64.128.190.246
67.15.221.2
216.127.72.7
69.57.132.37
66.98.130.235
212.227.111.26
64.14.244.60
82.98.86.174</content>
			</file>
		</files>
	</template>
	<template>
		<name>Orkut</name>
		<id>orkut</id>
		<acls>
			<acl>
				<sitelistname>Orkut</sitelistname>
				<block>{file1}</block>
				<ipblock>{file2}</ipblock>
				<drop></drop>
			</acl>
		</acls>
		<files>
			<file>
				<name>file1</name>
				<content>orkut.com</content>
			</file>
			<file>
				<name>file2</name>
				<content>72.14.209.0/24</content>
			</file>
		</files>
	</template>
	<template>
		<name>Google Talk</name>
		<id>gtalk</id>
		<acls>
			<acl>
				<sitelistname>Google Talk</sitelistname>
				<block>{file1}</block>
				<drop></drop>
			</acl>
			<acl>
				<dst>209.85.163.125/255.255.255.255</dst>
				<drop></drop>
			</acl>
			<acl>
				<dst>66.249.83.83/255.255.255.255</dst>
				<drop></drop>
			</acl>
			<acl>
				<dst>66.249.83.19/255.255.255.255</dst>
				<drop></drop>
			</acl>
			<acl>
				<dst>209.85.165.189/255.255.255.255</dst>
				<drop></drop>
			</acl>
			<acl>
				<dst>216.239.51.125/255.255.255.255</dst>
				<drop></drop>
			</acl>
			<acl>
				<dst>72.14.253.125/255.255.255.255</dst>
				<drop></drop>
			</acl>
			<acl>
				<dst>64.233.185.19/255.255.255.255</dst>
				<drop></drop>
			</acl>
			<acl>
				<dst>209.85.137.125/255.255.255.255</dst>
				<drop></drop>
			</acl>
		</acls>
		<files>
			<file>
				<name>file1</name>
				<content>talk.google.com
talkx.l.google.com
chatenabled.mail.google.com
mail.google.com/mail/channel/bind</content>
			</file>
		</files>
	</template>
</aclstemplate>
<routeping>
	<add>
		<command>iptables -t mangle -A routefix -p icmp -d {ip} -j ROUTE --gw {gw} --oif {int} --continue</command>
	</add>
	<remove>
		<command>iptables -t mangle -F routefix</command>
	</remove>
</routeping>
<cbq>
	<interface>
		/sbin/tc qdisc del dev {out} root
		/sbin/tc qdisc add dev {out} root handle 1 cbq bandwidth 10Mbit avpkt 1000 cell 8
		/sbin/tc class change dev {out} root cbq weight 1Mbit allot 1514
	</interface>
	<rule>
		/sbin/tc class del dev {out} parent 1: classid 1:{seq} cbq bandwidth 10Mbit rate {rate}Kbit weight {weight}Kbit prio 5 allot 1514 cell 8 maxburst 20 avpkt 1000
		/sbin/tc qdisc del dev {out} parent 1:{seq} handle {seq} tbf rate {rate}Kbit buffer 10Kb/8 limit 15Kb mtu 1500
		/sbin/tc filter del dev {out} parent 1:0 protocol ip prio 200 handle {mark} fw classid 1:{seq}
		
		/sbin/tc class add dev {out} parent 1: classid 1:{seq} cbq bandwidth 10Mbit rate {rate}Kbit weight {weight}Kbit prio 5 allot 1514 cell 8 maxburst 20 avpkt 1000
		/sbin/tc qdisc add dev {out} parent 1:{seq} handle {seq} tbf rate {rate}Kbit buffer 10Kb/8 limit 15Kb mtu 1500
		/sbin/tc filter add dev {out} parent 1:0 protocol ip prio 200 handle {mark} fw classid 1:{seq}
	</rule>
</cbq>
<maps>
	<map>
		<from>forward/cbq-*</from>
		<to>/etc/shaper/</to>
	</map>
	<map>
		<from>forward/pppoe/cbq-*</from>
		<to>/etc/nexus/shaper-pppoe/</to>
	</map>
	<map>
		<from>forward/firewall.sh</from>
		<to>/etc/nexus/</to>
	</map>
	<map>
		<from>forward/chap-secrets</from>
		<to>/etc/ppp/</to>
	</map>
	<map>
		<from>forward/shaper.sh</from>
		<to>/etc/nexus/</to>
	</map>
</maps>
<act>
	<pre>
	rm -fr /etc/nexus/shaper-pppoe/*
	rm -fr /etc/shaper/*
	rm -fr /tmp/nx_external
	mkdir -p /tmp/nx_condit/
	#cp -a /proc/net/ipt_condition/* /tmp/nx_condit/
	echo 1 > /tmp/nx_applying
	
	rm -fr /tmp/nx_unroute.*
	rm -fr /tmp/nx_forward*
	rm -fr /tmp/nx_route.*
	</pre>
	<post>
	# if [ "a`diff /etc/nexus/firewall.sh /tmp/nx_firewall.sh`" != "a" ]; then sh $NEXUS/core/bin/scripts/exec.sh sh /etc/nexus/firewall.sh; fi

	sh $NEXUS/core/bin/scripts/exec.sh /bin/sh /tmp/nx_firewall.sh
	sh $NEXUS/core/bin/scripts/exec.sh /bin/sh /tmp/nx_shaper.sh

	for i in /tmp/nx_forward.*;do sh $i;done
	for i in /tmp/nx_route.*;do sh $i;done

	# sh $NEXUS/core/bin/scripts/exec.sh /bin/sh $NEXUS/core/bin/scripts/cbq_restore.sh
	# sh $NEXUS/core/bin/scripts/exec.sh /bin/sh $NEXUS/core/bin/scripts/guest_restore.sh
	
	rm -fr /tmp/nx_applying
	touch /tmp/nx_dhcprestart

	cp -a $NEXUS/core/conf/forward.xml /tmp/nx_oldforward.xml
	
	if [ "a`ps ax|grep pppoe|grep -v grep`" == "a" ]; then echo oi; fi
	
	
	# iptables -P INPUT ACCEPT; iptables -F INPUT
	</post>
</act>
