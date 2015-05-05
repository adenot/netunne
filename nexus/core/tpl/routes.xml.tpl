<pre>
	<command>
		ip route flush cache
	</command>
</pre>
<interface>
	<command>
		ip route flush table {table}
		#ip route add {network} dev {device} src {ip} table {table}
		ip rule del from {ip} pref {table} table {table}
		ip rule add from {ip} pref {table} table {table}
		ip route add default via {gateway} dev {device} src {ip} proto static table {table}
		ip route append prohibit default table {table} metric 1 proto static
		#echo 0 > /proc/sys/net/ipv4/conf/{device}/rp_filter
	</command>
</interface>
<gateway>
	<command>
		ip rule add prio 50 table main
		ip rule add prio 222 table 222
		ip route flush table 222
		ip route add default table 222 proto static {nexthop}
	</command>
	<nexthop>
		<command>nexthop via {gateway} dev {device} weight {weight}</command>
	</nexthop>
	<remove>
		<command>ip route delete default dev {device}</command>
	</remove>
</gateway>
<post>
	<command></command>
</post>
<fwroute>
	<command>
{internals}iptables -t nat -I POSTROUTING -s {internalnetwork} -o {device} -j MASQUERADE
	</command>
</fwroute>
