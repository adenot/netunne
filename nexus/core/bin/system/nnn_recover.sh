#!/bin/sh

# . /etc/nexus/path

date > /root/recover.log

if [ ! -f /root/nUpdate.npak ]; then
	echo "/root/nUpdate.npak does not exists. Trying to download from http://www.console.com.br/center/npak/nUpdate.npak..."
	cd /root
	wget http://www.console.com.br/center/npak/nUpdate.npak 2>&1 >> /root/recover.log
	if [ ! -f nUpdate.npak ]; then
		echo 
		echo "Cannot download file, please configure network!"
		exit
	fi
	cd -
fi

# refazendo diretorios
echo 
echo -n "Creating directories... "
mkdir -p /NEXUS/nexus/interface
mkdir -p /NEXUS/nexus/core
mkdir -p /NEXUS/nexus/etc
echo "Ok"

# instalando ultima atualizacao
echo
echo -n "Installing last package... "
mkdir -p /tmp/npak/
tar -C /tmp/npak/ -xzvpf /root/nUpdate.npak  2>&1 >> /root/recover.log
sh /tmp/npak/nUpdate/install.sh  2>&1 >> /root/recover.log
echo 
echo "Ok"

# refazendo link para dados na outra particao
echo
echo -n "Creating data partition link... "
DATADISK=`cat /etc/nexusdatadisk`
rm -fr /NEXUS/nexus/core/data
ln -s /mnt/$DATADISK/nexusdata /NEXUS/nexus/core/data
echo "Ok"

# restaurando ultimo backup
echo
echo "Restoring last usable backup... "

cd /NEXUS/nexus/core/data/backupconf/
for bkp in `ls -t`
do
	tar xzpf $bkp 2>&1 >> /root/recover.log
	if [ -d conf ]; then
		break
	fi
done

mkdir -p /NEXUS/nexus/core/conf
mkdir -p /NEXUS/nexus/interface/conf

cp -a conf/* /NEXUS/nexus/core/conf 2>&1 >> /root/recover.log
cp -a conf/* /NEXUS/nexus/interface/conf  2>&1 >> /root/recover.log

rm -fr conf

echo "Ok"

echo "Netunne Restored! Please Reboot your system pressing CRTL+ALT+DEL"
