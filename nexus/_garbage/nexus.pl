#!/usr/bin/perl -w

# -------------------------------------------------------------------------------------
# Nexus Core

	use IO::Socket;
	use POSIX;
	use PHP::Interpreter;
	use strict;
	
	require "$ENV{'NEXUS'}/core/bin/common.pl";

	my $php = PHP::Interpreter->new();
	
	$php->eval('ini_set("display_errors", "Off");');
	
	$php->include("$ENV{'NEXUS'}/core/nlib/common.nx");
	$php->include("$ENV{'NEXUS'}/core/nlib/cls.nx");
	$php->include("$ENV{'NEXUS'}/core/nlib/in.nx");
	
	
	#variáveis locais -----------------------------------------------------
	my $pid; my $pid2; my $listener_sock; my $lis; 
	my $buf; my @apid=(); my $resp; my $domain;	
	my $func; my $context = "listener";
	my $client;
	
	#Variáveis para serem utilizadas no socket ---------------------------------------
	my $varSockHostonly = &getconf("/socket/hostonly","listener.xml");
	my $varSockHost = &getconf("/socket/host","listener.xml");
	my $varSockPort = &getconf("/socket/port","listener.xml");
	my $varSockListen = &getconf("/socket/listen","listener.xml");
	my $varSockProto = &getconf("/socket/proto","listener.xml");
	my $varSockReuse = &getconf("/socket/reuse","listener.xml");
	
	#Iniciando processo Daemonizing ---------------------------------------
	#$pid = fork;
	#exit if $pid;
	#die "ERROR: Can't fork - $!" unless defined($pid);
	#POSIX::setsid() or die "ERROR: Can't daemonize - $!";

    $SIG{CHLD} = 'IGNORE';

	my $pidpai = $$;
	$php->call("initialize",$pidpai);
	
	#definindo se aceita conexões de fora ou apenas local -----------------
	if($varSockHostonly){
		$listener_sock = new IO::Socket::INET
			(
				LocalHost => $varSockHost,
				LocalPort => $varSockPort,
				Listen    => $varSockListen,
				Proto     => $varSockProto,
				Reuse     => $varSockReuse
			);
	}
	else{
			$listener_sock = new IO::Socket::INET
			(
				LocalPort => $varSockPort,
				Listen    => $varSockListen,
				Proto     => $varSockProto,
				Reuse     => $varSockReuse
			);
	}
	die "ERROR: Can't initialize socket (is port $varSockPort already in use?) - $!" unless ($listener_sock);
	
	while ($client = $listener_sock->accept())
	{
		#if($client->peerhost()=="192.168.100.205") {next;} #if(gethostbyaddr($client->peeraddr()) !~ /console\.com\.br/) next;

		$pid = fork();
		die "ERROR: Can't fork connection - $!" unless defined($pid);
		
		if($pid==0)
		{
			# eh o filho
			my $numRec=0;
			$resp = $php->call("in_newconnection",($client,$$));
			print $client $resp."\n";
			while (defined ($buf = <$client>))
			{

				$numRec++;
				chomp $buf;
				$pid2 = fork();
				if($pid2==0)
				{
					eval {
						$resp = $php->call("in","$buf");
					};
				
					if ($@) {
						print $client "ERROR ('System Library Error',LIBERROR)\n";
						$resp="";
					}
	
					if ($resp) {
						print $client $resp."\n";
					} else {
						print $client "ERROR ('NULL Result',NULLERROR)\n";
					}
						
					$resp="";
					exit(0);
				}
			}
			print $client "saiu";
			close $listener_sock;
			exit(0);
			
		}
		else{
			push(@apid,$pid);
		}
	}

	
