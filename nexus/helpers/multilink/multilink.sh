#!/bin/bash

. network.conf

ip route delete default

ip route add default scope global nexthop via $P1 dev $IF1 weight 1 \
nexthop via $P2 dev $IF2 weight 1
