#!/bin/sh

test -f /etc/ddclient.conf && /usr/sbin/ddclient -daemon=0 -syslog -quiet retry