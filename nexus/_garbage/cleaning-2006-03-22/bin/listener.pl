#!/usr/bin/perl
# -------------------------------------------------------------------------------------
# Nexus Core
# Módulo Listener - Responsável por criar socket e ouvir requisições de clientes
# Console Soluções Tecnológicas - Analista e Programador: Fabiano Louback Gonçalves
# Arquivo: listener.pl - 10/10/2005 - versão 0.3
# -------------------------------------------------------------------------------------

	use IO::Socket;
	use POSIX;
	use Locale::gettext;
	use PHP::Interpreter;
	
#use strict;
#no strict 'refs';

	require "$ENV{'NEXUS'}/core/bin/common.pl";
	require "$ENV{'NEXUS'}/core/bin/in.pl";

	my $php = PHP::Interpreter->new();
	$php->include("$ENV{'NEXUS'}/core/nlib/in.nx");

	#variáveis locais -----------------------------------------------------
	my $pid;  my $listener_sock; my $lis; 
	my $buf; my @apid=(); my $resp; my $domain;	
	my $func; my $context = "listener";

	#variáveis para serem utilizadas no arquivo de log --------------------------------
	my $varLogStart = "log iniciado";
	my $varLogPeerStart = "Conexão estabelecida com cliente";
	my $varLogPeerIP = "IP do cliente =";
	my $varLogPeerPort = "Porta = ";
	my $varLogGetData = "Foi recebida a instrução";
	my $varLogPeerClose = "Cliente fechou conexão, o processo está sendo finalizado.";
	my $varLogListProc = "Lista de processos";
 	my $varLogPeerNewPid = "Criado novo processo filho";
	
	#Variáveis para serem utilizadas no socket ---------------------------------------
	my $varSockLocalonly = &getconf("/socket/localonly","listener.xml");
	my $varSockHost = &getconf("/socket/host","listener.xml");
	my $varSockPort = &getconf("/socket/port","listener.xml");
	my $varSockListen = &getconf("/socket/listen","listener.xml");
	my $varSockProto = &getconf("/socket/proto","listener.xml");
	my $varSockReuse = &getconf("/socket/reuse","listener.xml");
	my $varSockPeerStart = "\nNEXUS - CORE\n---------------\nconexao estabelecida - numero da conexao =";
	my $varSockGetData = "Foi recebida a instrução";
	my $varSockSendData = "";
	my $varSockGoodSyntax = "Sintaxe de mensagem válido";
	my $varSockBadSyntax = "Sintaxe de mensagem inválido"; 
	my $varSockNotFunction = "Função de entrada inexistente";
	
	#variáveis de erro ------------------------------------------------------------
	my $varErro020101 = "ERRO(020101) Couldn't make fork to daemonize";
	my $varErro020102 = "ERRO(020102) Couldn't create new nession to daemonize";
	my $varErro020101 = "ERRO(020101) Couldn't create socket";
	my $varErro020301 = "ERRO(020301) Couldn't make fork to listen client";
	my $varErro020501 = "ERRO(020501) Bad formed command";
	my $varErro020401 = "ERRO(020401) Couldn't remove pid file";

	#inicializando configurações de locale --------------------------------
	$domain = Locale::gettext->domain($context);
	$domain->dir("$ENV{'NEXUS'}".&getconf("/locale/path"));

	#Iniciando processo Daemonizing ---------------------------------------
	$pid = fork;
	exit if $pid;
	die "$varErro020101 - $!" unless defined($pid);
	POSIX::setsid() or die "$varErro020102 - $!";

	#Iniciando log --------------------------------------------------------
	&cleanPidLog($context);
	&log($context,$$,$varLogStart);
	
	#definindo se aceita conexões de fora ou apenas local -----------------
	if($varSockLocalonly){
		$listener_sock = new IO::Socket::INET
			(
				Host => $varSockHost,
				LocalPort => $varSockPort,
				Listen    => $varSockListen,
				Proto     => $varSockProto,
				Reuse     => $varSockReuse
			);
	}
	else{
			$listener_sock = new IO::Socket::INET
			(
				LocalHost => $varSockHost,
				LocalPort => $varSockPort,
				Listen    => $varSockListen,
				Proto     => $varSockProto,
				Reuse     => $varSockReuse
			);
	}
	die "$varErro020101 - $!" unless ($listener_sock);

	while ($client = $listener_sock->accept())
	{
			#if($client->peerhost()=="192.168.100.205") {next;} #if(gethostbyaddr($client->peeraddr()) !~ /console\.com\.br/) next;
			if ($client->peerhost() ne "127.0.0.1") { next; }
			$pid = fork();
			die "$varErro020301 - $!" unless defined($pid);
			if($pid==0)
			{
				# eh o filho
				my $numRec=0;
				print $client "$varSockPeerStart $$\n\n";
				&log($context,$$,$varLogPeerStart);
				&log($context,$$,$varLogPeerIP.$client->peerhost());
				&log($context,$$,$varLogPeerPort.$client->peerport());
				while (defined ($buf = <$client>))
				{
					$numRec++;
					chomp $buf;
					#print $client "$varSockGetData $$ - $numRec : $buf\n";
					#print "\n\nall pid = ",&getChildPid($context,"ALL");
					#&slog("Nexus Core Listener: ".$buf);
					&log($context,$$,"$varLogGetData = $buf");
					#$resp = $buf=~/[A-Za-z0-9]+/g;
					if($buf=~/([a-zA-Z]+)[\s]*[(]["](.*)["][)]/){
						$func = "in_$1";
						if ($php->function_exists($func)){
							$resp = $php->call($func,"$2");
							print $client $resp."\n";
							# &mesg($context,"","MSG",$resp,$buf,$$.$numRec);		
							# print $client $$.$numRec."\n";
						} else {
							print $client "$varErro020501 - $varSockNotFunction\n";
						}
					}
					elsif ($buf eq "exit") {
						die;
					}
					else{
						print $client "$varErro020501 - $varSockBadSyntax\n";
						# &mesg($context,"","ERRO","$varErro020501",$buf,$$.$numRec);
					}
				}
				&log($context,$$,"$varLogPeerClose");
				#apagar o arquivo pid de $$
				unless(unlink("$ENV{'NEXUS'}/core/sys/run/$context/$$.pid")){ 
						  &mesg($context,"","ERRO","$varErro020401 core/sys/run/$context/$$.pid");
				}
				exit(0);
			}
			else{
				&log($context,$$,"$varLogPeerNewPid = $pid");
				push(@apid,$pid);
				&log($context,$$,"$varLogListProc = @apid");
			}
	}
#EOF
