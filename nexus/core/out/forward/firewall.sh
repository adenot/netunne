
# CLEAN
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
iptables -t filter -X forwarduser
iptables -t mangle -X forwarduser
iptables -t mangle -X routeuser
iptables -t mangle -X routefix
# GLOBAL
echo 1 > /proc/sys/net/ipv4/ip_forward
modprobe ip_nat_ftp
modprobe ip_nat_pptp
modprobe ip_nat_irc
modprobe ip_nat_amanda
modprobe ip_nat_tftp
iptables -t filter -N forwarduser
iptables -t mangle -N forwarduser
iptables -t mangle -N routeuser
iptables -t mangle -N routefix
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
iptables -t filter -I FORWARD -m condition --condition noforward -j DROP
iptables -t filter -I INPUT -i lo -j ACCEPT
iptables -t filter -I INPUT -m state --state ESTABLISHED,RELATED -j ACCEPT
iptables -t filter -I INPUT -p icmp --icmp-type echo-request -m state --state NEW,ESTABLISHED,RELATED -j ACCEPT
iptables -t nat -A PREROUTING -i ppp+ -j RETURN
iptables -t nat -A PREROUTING -p tcp --dport 80 -m mark --mark 9999 -j REDIRECT --to-port 3080
iptables -t filter -I FORWARD -p tcp --dport 53 -j ACCEPT
iptables -t filter -I FORWARD -p udp --dport 53 -j ACCEPT
iptables -t filter -I FORWARD -p tcp --sport 53 -j ACCEPT
iptables -t filter -I FORWARD -p udp --sport 53 -j ACCEPT
iptables -t mangle -A PREROUTING -j MARK --set-mark 9999
iptables -t mangle -A PREROUTING -j guests
iptables -t mangle -I FORWARD -m mark --mark 9998 -j guestsacl
iptables -t mangle -I FORWARD -j guests
iptables -t mangle -A POSTROUTING -j routefix
iptables -t mangle -A FORWARD -j routeuser
iptables -t filter -A FORWARD -j forwarduser
iptables -t mangle -A FORWARD -j forwarduser
iptables -t filter -A FORWARD -j guests
iptables -t filter -P INPUT DROP
iptables -t filter -P FORWARD DROP
# TRAFFIC
# RULES
# INTERNAL INTERFACE: eth0
iptables -t filter -I FORWARD -i ppp+ -o ppp+ -s 192.168.31.0/255.255.255.0 -d 192.168.31.0/255.255.255.0 -j REJECT
iptables -t filter -I FORWARD -m account --aname eth0 --ashort --aaddr 192.168.31.0/255.255.255.0
iptables -t filter -A FORWARD -d 192.168.31.0/255.255.255.0 -j ACCEPT
iptables -t filter -A FORWARD -o ppp+ -j ACCEPT
# EXTERNAL INTERFACE: eth2
iptables -t nat -I POSTROUTING -o eth2 -j MASQUERADE
iptables -t filter -I FORWARD -m account --aname eth2 --ashort --aaddr 192.168.100.31
echo "echo 0 > /proc/sys/net/ipv4/conf/eth2/rp_filter" >> /tmp/nx_external
# FIREWALL eth0
iptables -t filter -A INPUT -i eth0 -p udp --dport 67 -j act_allow
iptables -t filter -A INPUT -i eth0 -p udp -s 0.0.0.0/32 --sport 68 -d 255.255.255.255/32 --dport 67 -m state --state NEW,ESTABLISHED -j act_allow
iptables -t filter -A OUTPUT -o eth0 -p udp -s 0.0.0.0/32 --sport 67 -d 255.255.255.255/32 --dport 68 -m state --state ESTABLISHED -j act_allow
iptables -t filter -i eth0 -p tcp --dport 3080 -A INPUT -j act_allow
iptables -t filter -i eth0 -p tcp --dport 53 -A INPUT -j act_allow
iptables -t filter -i eth0 -p udp --dport 53 -A INPUT -j act_allow
iptables -t filter -i eth0 -p tcp --dport 22 -A INPUT -j act_logallow
iptables -t filter -i eth0 -p tcp --dport 443 -A INPUT -j act_logallow
# FIREWALL eth2
iptables -t filter -A INPUT -i eth2 -p udp --dport 67 -j act_drop
iptables -t filter -A INPUT -i eth2 -p udp -s 0.0.0.0/32 --sport 68 -d 255.255.255.255/32 --dport 67 -m state --state NEW,ESTABLISHED -j act_drop
iptables -t filter -A OUTPUT -o eth2 -p udp -s 0.0.0.0/32 --sport 67 -d 255.255.255.255/32 --dport 68 -m state --state ESTABLISHED -j act_drop
iptables -t filter -i eth2 -p tcp --dport 3080 -A INPUT -j act_drop
iptables -t filter -i eth2 -p tcp --dport 53 -A INPUT -j act_allow
iptables -t filter -i eth2 -p udp --dport 53 -A INPUT -j act_allow
iptables -t filter -i eth2 -p tcp --dport 22 -A INPUT -j act_logallow
iptables -t filter -i eth2 -p tcp --dport 443 -A INPUT -j act_logallow
# FIREWALL eth1
iptables -t filter -A INPUT -i eth1 -p udp --dport 67 -j act_drop
iptables -t filter -A INPUT -i eth1 -p udp -s 0.0.0.0/32 --sport 68 -d 255.255.255.255/32 --dport 67 -m state --state NEW,ESTABLISHED -j act_drop
iptables -t filter -A OUTPUT -o eth1 -p udp -s 0.0.0.0/32 --sport 67 -d 255.255.255.255/32 --dport 68 -m state --state ESTABLISHED -j act_drop
iptables -t filter -i eth1 -p tcp --dport 3080 -A INPUT -j act_drop
iptables -t filter -i eth1 -p tcp --dport 53 -A INPUT -j act_allow
iptables -t filter -i eth1 -p udp --dport 53 -A INPUT -j act_allow
iptables -t filter -i eth1 -p tcp --dport 22 -A INPUT -j act_logallow
iptables -t filter -i eth1 -p tcp --dport 443 -A INPUT -j act_logallow
