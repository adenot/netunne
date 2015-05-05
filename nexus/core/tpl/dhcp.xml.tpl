<global>
option netbios-node-type 2;
option netbios-scope "";
option netbios-name-servers {interfacehost};

{option}

{subnet}

{host}
</global>
<subnet>
subnet {network} netmask {netmask} {
	{range}range {range1} {range2};

{option}

}
</subnet>
<host>
	host {host} {
	  hardware ethernet {mac};
	  fixed-address {ip};
	}
</host>
<filedefault>
INTERFACES="{interface}"
</filedefault>
<option>
{name} {value};
</option>
<maps>
	<map>
		<from>dhcp/dhcpd.conf</from>
		<to>/etc/</to>
	</map>
	<map>
		<from>dhcp/dhcp.default</from>
		<to>/etc/default/dhcp</to>
	</map>
</maps>
<act>
	<post>sh $NEXUS/core/bin/scripts/exec.sh /etc/init.d/dhcp restart</post>
</act>
