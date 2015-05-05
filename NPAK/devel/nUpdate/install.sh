#!/bin/bash

cd /tmp/npak/nUpdate/

rm -fr /tmp/uptemp
mkdir -p /tmp/uptemp

# matando o dmesg
#kill `ps xa|grep "/bin/sh /etc/nexus/bin/scripts/dmesg.sh"|awk {'print $1'}`

# salvando os confs do usuario
cd /NEXUS/nexus/
cp -a core/conf /tmp/uptemp
cp -a interface/conf /tmp/uptemp/iconf

# apagando info. de versao antiga
rm -fr /tmp/uptemp/conf/version.ini
rm -fr /tmp/uptemp/iconf/version.ini

cd -

# abrindo o nexusetc
cp nexusetc.tgz /NEXUS/nexus/
cd /NEXUS/nexus
tar xzvpf nexusetc.tgz

# copiando pra cima do etc
cp -a nexusetc/* etc
rm -fr nexusetc

cd -

# abrindo o nexuscore
cp nexuscore.tgz /NEXUS/nexus/
cd /NEXUS/nexus
tar xzvpf nexuscore.tgz

# copiando pra cima do core
cp -a nexuscore/* core
rm -fr nexuscore

cd -

# abrindo o nexusinterface
cp nexusinterface.tgz /NEXUS/nexus/
cd /NEXUS/nexus
tar xzvpf nexusinterface.tgz

# copiando pra cima da interface
cp -a nexusinterface/* interface
rm -fr nexusinterface

# copiando de volta as configuracoes
cp -a /tmp/uptemp/conf/* core/conf/
cp -a /tmp/uptemp/iconf/* interface/conf/


# cd /tmp/uptemp
cd -

# copiando os objetos estaticos por cima
cp -af objects-static.xml /NEXUS/nexus/core/conf/
cp -af objects-static.xml /NEXUS/nexus/interface/conf/

cp -af conncheck.ini /NEXUS/nexus/core/conf/

# se nao existe o log.db ou ele tem zero bytes, copio um novo
if [ ! -s /NEXUS/nexus/core/data/db/log.db ]; then
	cp -af log.db /NEXUS/nexus/core/data/db/
fi

# zero o hosts.deny pra nao bloquear conexoes sem reverso no ssh
echo > /etc/hosts.deny

# salvando esse pacote para futuras recuperacoes
cp -a /tmp/nUpdate.npak /root/

# copiando scripts do sistema
chmod +x /NEXUS/nexus/core/bin/system/*
cp -a /NEXUS/nexus/core/bin/system/* /bin/

mkdir -p /NEXUS/nexus/core/out/proxy

# atualizando scripts pppoe
cp -a nx_cbq_up /etc/ppp/ip-up.d/
cp -a nx_cbq_down /etc/ppp/ip-down.d/

# colocando defaults do proxy
if [ ! -f /NEXUS/nexus/core/conf/proxy.xml ]; then
	cp -a proxy.xml /NEXUS/nexus/core/conf
	cp -a proxy.xml /NEXUS/nexus/interface/conf
fi

# crontab
cp crontab /etc/

# nohup /bin/sh /etc/nexus/bin/scripts/exec.sh /etc/nexus/bin/nexus.sh "merge(all)" &

# obtenho a licensa
# nohup php5.0 -q /NEXUS/nexus/core/nlib/task_license.nx 2>&1 >> /tmp/task-license.log &


exit
