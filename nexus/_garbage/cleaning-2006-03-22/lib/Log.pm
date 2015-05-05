#-------------------------------------------------------------------------------------
# Nexus Core
# Classe para manipular arquivos de log
# Console Soluções Tecnológicas - Analista e Programador: Fabiano Louback Gonçalves
# Arquivo: Log.pm - 02/11/2005 - versão 0.1
# -------------------------------------------------------------------------------------
package Log;
#use strict;
use warnings;
use Sys::Syslog;
require "$ENV{'NEXUS'}/core/lib/Conf.pm";
require "$ENV{'NEXUS'}/core/bin/common.pl";
my @DEF=('core','sys');

#----------------------------------------------------------------

sub new {
	my ($pkg, $type, $context, $pid, $message) = @_;
	if(!$type){$type = $DEF[0].'log';}
	if(!$context){$context = "listener";}
	return bless {
		'type' => $type,
		'context' => $context,
		'pid' => $pid,
		'message' => $message
	},$pkg;
}
#----------------------------------------------------------------
# write a log to logfile
# se o parametro msg não for passado o metodo buscara o atributo message da classe
# retorna 1 se o a escrita do log foi bem sucedida
sub write {
	my ($ins, $msg) = @_;
	if(!$msg){$msg=$ins->{'message'}};
	my $conf = new Conf;
	$conf->source("core");
	my $path; my $timelog; my $ret=0;
	my $pid = $ins->{'pid'};
	my $cntx = $ins->{'context'};
	my $file;
	if($ins->{'type'} eq 'corelog')
	{
		$path = $conf->get("/log/path");
		$file = "$ENV{'NEXUS'}$path/$cntx/$pid.log";
		print "mostrando dados do core.xml\n";
		print "	path='$path' cntx='$cntx' pid='$pid'\n";
		print "  file='$file'\n";
		open(LOG,">>$file");
		if($conf->get("/log/timetype") eq "unixtime")
			{$timelog=time();} else {$timelog=&fmtdata();}
		print LOG $timelog," - ",$msg,"\n";
		close(LOG);
		$ret = 1;
	}
	elsif($ins->{'type'} eq 'syslog')
	{
		openlog("", 'cons,pid', 'user');
		syslog('nexus', '%s', $msg);
		closelog();
		$ret = 1;
	}
	
	#setting pid file for process manager
	$path = $conf->get("/run/path");
	open(PID,">$ENV{'NEXUS'}/$path/$cntx/$pid.pid");
	print PID "$pid\n";
	close(PID);
	return $ret;
}

#----------------------------------------------------------------

sub type {
	my ($ins, $type) = @_;
	if(!$type) {return $ins->{'type'};}
	else {return $ins->{'type'}=$type;}
}

#-----------------------------------------------------------------

sub message {
	my ($ins, $message) = @_;
	if(!$message) {return $ins->{'message'};}
	else {return $ins->{'message'}=$message;}
}

#------------------------------------------------------------------

sub context {
	my ($ins, $context) = @_;
	if(!$context) {return $ins->{'context'};}
	else {return $ins->{'context'}=$context;}
}

#------------------------------------------------------------------
sub teste {
	my $ins = shift;
	return "teste funcionou! type='".$ins->{'type'}."\n";
}
1;

