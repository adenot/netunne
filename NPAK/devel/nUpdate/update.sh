rm -fr nexuscore.tgz
ncftpget -u transfer -p transfer 192.168.100.2 . nexuscore/nexuscore.tgz
rm -fr nexusinterface.tgz
ncftpget -u transfer -p transfer 192.168.100.2 . nexusinterface/nexusinterface.tgz
rm -fr nexusetc.tgz
ncftpget -u transfer -p transfer 192.168.100.2 . nexusetc/nexusetc.tgz

