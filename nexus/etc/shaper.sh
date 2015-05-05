/sbin/tc qdisc del dev eth0 root
/sbin/tc qdisc add dev eth0 root handle 1 cbq bandwidth 10Mbit avpkt 1000 cell 8
/sbin/tc class change dev eth0 root cbq weight 1Mbit allot 1514
/sbin/tc qdisc del dev eth2 root
/sbin/tc qdisc add dev eth2 root handle 1 cbq bandwidth 10Mbit avpkt 1000 cell 8
/sbin/tc class change dev eth2 root cbq weight 1Mbit allot 1514
