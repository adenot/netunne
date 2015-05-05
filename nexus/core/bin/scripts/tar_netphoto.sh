#!/bin/sh

. /etc/nexus/path

cd /$NEXUS/core/data/netphoto

arq=netphoto-`date +%Y-%m-%d.%H-%M-%S`.tar.bz2

tar cjvpf $arq *.data
rm -fr *.data
