#!/usr/bin/perl
#-------------------------------------------------------------------------------------
# Nexus Core
# Subrotinas em Comum - Fornece ferramentas em comum para o sistema
# Console Tecnologia da Informação - Analista e Programador: Fabiano Louback Gonçalves
# Arquivo: common.pl - 14/10/2005 - versão 0.3
# -------------------------------------------------------------------------------------
use XML::Mini::Document;
use Data::Dumper;
use Sys::Syslog;
#--------------------------------------------------------


#--------------------------------------------------------
#grava informação no syslog 
#parametro = mensagem

sub slog()
{
	my $msg = shift;
	openlog("nexus", 'cons,pid', 'user');
	syslog('nexus', '%s', $msg);
	closelog();
					
}

#--------------------------------------------------------
#obtem item de configuração do sistema em arquivo xml
#parametros = caminho no xml até o elemento solicitado, arquivo xml

sub getconf()
{
   my ($elementPath, $confType) = @_;
   if(!$confType){$confType="core.xml";}
   my $xmldoc = new XML::Mini::Document;
   my $file = "$ENV{'NEXUS'}/core/conf/$confType";
   $xmldoc->parse("$file");
   my $conf = $xmldoc->getElementByPath($elementPath) or die("ERROR(020105) cant get $elementPath in $file");
   return $conf->text();
}

#------------------------------------------------------
#formatação de data
#parametro = data em unixtime a ser formatada

sub fmtdata()
{
	my $param = shift;
	if(!$param){$param=time();}
	($segundo,$minuto,$hora,$dia,$mes,$ano) = localtime($param);
	return ($dia."/".$mes."/".($ano+1900)." ".$hora.":".$minuto.":".$segundo);
}

#---------------------------------------------------------
#log geral para os processos
#parametros = tipo, numero, mensagem
#tipo = ERRO | WARNING | MSG

sub log()
{
	my($type, $num, $msg) = @_;
	my $cfg; my $timelog;
	open(LOG,">>$ENV{'NEXUS'}".&getconf("/log/path")."/$type/$num.log");
	$cfg=&getconf("/log/timetype");
	if($cfg eq "unixtime"){$timelog=time();} else{$timelog=&fmtdata();}
	print LOG $timelog," - ",$msg,"\n";
	close(LOG);

	open(PID,">$ENV{'NEXUS'}".&getconf("/run/path")."/$type/$num.pid");
	print PID "$num\n";
	close(PID);
}

#---------------------------------------------------------------------------
# Formato do arquivo msg.wall = datahora;contexto;acao;tipoMsg;textoMsg
#Escreve mensagens no arquivo de mensagens
#parametros = contexto, ação, tipo, testo

sub mesg()
{
	my($context, $action, $type, $text, $command, $numCall) = @_;
	my $file="$ENV{'NEXUS'}".&getconf("/msg/path")."/msg.wall-".$numCall;
	my $timelog;
	if(!&lockedfile($file))
	{
		open(MSG,">>$file") or die("020101:ERROR-COMMON-MSG-OPEN");
		&lockfile("$file")  or die("020102:ERROR-COMMON-MSG-LOCK-CREATE");
		$cfg=&getconf("/msg/timetype");
		if(!$numCall){$numCall='';}
		if($cfg eq "unixtime"){$timelog=time();} else{$timelog=&fmtdata();}
		print MSG "----- BEGIN ".$numCall." -----\n";
		print MSG "Time = $timelog\n";
		print MSG "Contexto = $context\n";
		print MSG "Comando = $command\n";
		print MSG "Acao = $action\n";
		print MSG "TipoMsg = $type\n";
		print MSG "TextoMsg = $text\n";
		print MSG "----- END ".$numCall." -----\n";
		#print MSG $timelog.";".$context.";".$action.";".$type.";".$text."\n";
		&unlockfile("$file") or die("020103:ERROR-COMMON-MSG-LOCK-REMOVE");
		close(MSG);
	}
	else {die("020104:ERROR-COMMON-MSG-LOCK-TIMEOUT");}
}

#---------------------------------------------------------
#
#

#---------------------------------------------------------
#cria um lock para arquivo
#parametrp = arquivo

sub lockfile()
{
	my($file) = shift;
	$file =~ s/^>+//g;
	unless(open(LOCK,">$file.lock")) {
		return 0; #ERROR-COMMON-FLOCK-CREATE
	}
	print LOCK $$;
	close(LOCK);
	return 1;	
}

#---------------------------------------------------------
#remove lock de um arquivo
#parametro = arquivo

sub unlockfile()
{
	my $file = shift;
	return unlink("$file.lock"); #ERROR-COMMON-FLOCK-REMOVE
}

#--------------------------------------------------------
#verifica se um arquivo está com lock
#parametros = arquivo, timeout para verificação do lock

sub lockedfile()
{
	my $quit=0;
	my $start = time();
	my($file,$TimeOut) = @_;
	if(!$TimeOut) {$TimeOut=2;}
	while((time()-$start)<$TimeOut){				
		if(-e "$file.lock") {
			sleep(1);
		}
		else {return 0;}	
	}
	return 1;
}

#-------------------------------------------------------
#retorna o pid inicial (pai) de um contexto
#parametro = contexto

sub getFatherPid()
{
	my $cntx = shift;
	my $path = "$ENV{'NEXUS'}".&getconf("/run/path")."/$cntx/";
	my $varErroCommonOpendir = "couldn't open directory";
	opendir(DIR,$path) or die "$varErroCommonOpendir $path";
	my @dir=readdir(DIR);
	closedir(DIR);
	@dir = sort @dir;
	$_ = $dir[2]; /([0-9]*)/;
	return $1;
}
#-------------------------------------------------------
#retorna o pid do último processo (last child) de um contexto
#conforme parametro parametro modificador 
#	= 'FIRST' retornara o pid do primeiro processo filho
#	= 'LAST' retornará o pid do último processo filho 
#	= 'ALL' retornara a lista com todos os processos filhos 
#	se omitido o parametro modificador o valor assumido sera 'LAST'

sub getChildPid()
{
	my $i;my $ret;
	my ($cntx, $modify) = @_;
	if(!$modify){$modify='LAST';}
	my $path = "$ENV{'NEXUS'}".&getconf("/run/path")."/$cntx/";
	my $varErroCommonOpendir = "couldn't open directory";
	opendir(DIR,$path) or die "$varErroCommonOpendir $path";
	my @dir=readdir(DIR);
	closedir(DIR);
	@dir = sort @dir;
	if($modify eq 'FIRST'){
		$_ = $dir[3]; 
		/([0-9]*)/;
		return $1;
	}
	elsif($modify eq 'LAST'){
		$_ = $dir[$#dir]; 
		/([0-9]*)/;
		return $1;
	}
	elsif($modify eq 'ALL'){
		for($i=3;$i<$dir[$#dir];$i++)
		{
			$_ = $dir[$i]; 
			/([0-9]*)/;
			push(@ret,$1); 
		}
		return @ret;	
	}
}
#-------------------------------------------------------
#obtem o unixtimestamp do ultimo processamento feito por um processo filho
#parametros : 1 - contexto , 2 - numero do processo
#se o parametro 2 for omitido, o ultimo processo do contexto será considerado

sub getChildTime()
{
	my ($cntx, $pid) = @_;
	my $path = "$ENV{'NEXUS'}".&getconf("/run/path")."/$cntx/";
	my $varErroCommonOpendir = "couldn't open directory";
	opendir(DIR,$path) or die "$varErroCommonOpendir $path";
	my @dir=readdir(DIR);
	closedir(DIR);
	@dir = sort @dir;
	if(!$pid)
		{$file = $path.$dir[$#dir];}
	else
		{$file = $path.$pid.".pid";}
	return (stat("$file"))[10];	
}
#--------------------------------------------------------
#obtem o tempo da ultima tarefa executada pelo processo pai de um contexto
#parametro = contexto

sub getFatherTime()
{
	my $cntx = shift;
	my $path = "$ENV{'NEXUS'}".&getconf("/run/path")."/$cntx/";
	my $file;
	my $varErroCommonOpendir = "couldn't open directory";
	opendir(DIR,$path) or die "$varErroCommonOpendir $path";
	my @dir=readdir(DIR);
	closedir(DIR);
	@dir = sort @dir;
	$file = $path.$dir[2];
	return (stat("$file"))[10];
}
#----------------------------------------------------------------------
#obtem o tempo que o (processo pai) de um contexto está ocioso
#parametro = contexto

sub getFatherIdleTime()
{
	my $cntx = shift;
	return (time()-&getFatherTime($cntx));
}
#---------------------------------------------------------------------
#apaga arquivos pid
#parametro = contexto

sub cleanPidLog()
{
	my $varErroCommonOpendir = "couldn't open directory";
	my $varErroCommonUnlink = "couldn't remove file";
	my $cntx = shift;
	my $path = "$ENV{'NEXUS'}".&getconf("/run/path")."/$cntx/";
	opendir(DIR,$path) or die "$varErroCommonOpendir $path";
	my @dir=readdir(DIR);
	foreach $arq (@dir[2..$#dir])
	{
		unlink $path.$arq or die "$varErroCommonUnlink @dir \n";	
	}
	closedir(DIR);
}
#--------------------------------------------------------------------
#bloqueia a execução de um serviço para outros processos
#parametro = servico, pid do solicitante

sub lockservice()
{
	my ($service, $pid) = @_;
	my $file = "$ENV{'NEXUS'}/core/services/flag/$service.lock";
	if(!&servicelocked($service))
	{
		open(SLOCK,">$file");
		print SLOCK $pid.
		close(SLOCK);
	}
}

#---------------------------------------------------------------------
#idesbloqueia a execução de um serviço para outros processos
#parametro = servico, pid do solicitante

sub unlockservice()
{
	my ($service, $pid) = @_;
	my $file = "$ENV{'NEXUS'}/core/services/flag/$service.lock";
	if(!&servicelocked($service))
	{
		open(SLOCK,"<$file");
		$pidlock = <SLOCK>;
		close(SLOCK);
		if($pidlock==$pid)
			{return(unlink($file));}
		else 
			{return 0;}
	}
	return 1;
} 
#-----------------------------------------------------------------------------
#verifica se existe bloqueio para execução de um serviço para outros processos
#parametro = servico

sub servicelocked()
{
	my $service = shift;
	my $lock=0;
	my $file = "$ENV{'NEXUS'}/core/services/flag/$service.lock";
	if(-e $file){$lock=1;}
	return $lock;
	#poderia modificar para retornar o pid de quem 'lockou'
}


#---------------------- TEST PLACES -----------------------------

#if(-e "teste.teste"){ print "existe\n";}else {print "não existe\n" ;}
#if(&validxml("/home/kurumin/tmp/catalog/catalog.xml")){print "valido\n";} else {print "invalido\n";}
#&in_process("network.xml");


#use lib "/home/kurumin/nexus/core/lib";
#use XML::TESTE;
#$x = new XML::TESTE("core.xml");
#print $x->show();


#------------------- LOCK TESTPLACE BEGIN -----------------------
=cut
if($ARGV[0]=="testlock")
{
		  $arq=$ARGV[1];
		  if(&lockedfile($arq))
			  { die("arquivo está com lock e não pode ser aberto"); }
		  else
		  {
			  	if(&lockfile($arq)) 
				{
				  open(F,">>$arq");
				  print "\nopen realizando, pressione qq tecla para escrever no arquivo";
				  $s=<STDIN>;
				  print F "escrevi no arquivo !\n";
				  print "\nescrita realizando, pressione qq tecla para fechar o arquivo";
				  $s=<STDIN>;
              &unlockfile($arq);
				  close(F);
				  print "\ntestes concluidos com sucesso !\n";
			  	} 
				else
				{
					die("\nnão foi possivel criar lock\n");
				}
		  }
}
=cut
#---------------- LOCK TESTPLACE END ------------

#---------------- MSG TESTPLACE BEGIN -----------------
=cut
if($ARGV[0]=="testmsg")
{
	print "\nTestando função de escrita de mensagens\n\n";
	&mesg("TESTPLACE","","ERRO","Aconteceu erro testando testplace");
	print "mensagem gravada !\n";
}
=cut
=cut
if($ARGV[0]=="testmsg")
{
	print "$ARGV[0]\n";
	print "$ARGV[1]\n";
	print "$ARGV[2]\n";
	print &fmtdata($ARGV[0]) ,"\n";
	print &fmtdata(time()) ,"\n";
}
=cut
#---------------- MSG TESTPLACE BEGIN -----------------



   	

#---------------- getInfo BEGIN -----------------
#$xmlhash = $xmldoc->toHash();
#$xmlroot = $xmldoc->getRoot();
#$xmlchildren = $xmlroot->getAllChildren();
#print Dumper($xmlhash),"\n\n\n\n";
#print "(",$xmlroot->name(),"=",$xmlroot->text(),")\n";
#print "(",$xmlroot->getElement($element)->name(),"=",$xmlroot->getElement($element)->text(),")\n";
#foreach( $elem( @{$xmlchildren} ) )
#	{ print "->",$elem->texto(),"\n"; }
#------------------------------------------------------------------------------------------
#print "\nimprimindo XML ------------------------------------\n";
#print &getconf("core/modules/module");
#print "\nfoi impresso XML ------------------------------------\n";

#$i{'RJ'}={'nome','Eu','name','I'};
#$i{'SP'}={'nome','Tu','name','You'};
#$i{'BA'}='vazio';
#print "\n\n\n",$i{'SP'}{'nome'},"\n";
#print $i{'RJ'}{'nome'},"\n";
#----------------- getInfo END ------------------

1;
