#!/usr/bin/perl
use Unix::Processors;
         my $procs = new Unix::Processors;
         print "There are ", $procs->max_online, " CPUs at ", $procs->max_clock, "\n";
         if ($procs->max_online != $procs->max_physical) {
             print "Hyperthreading between ",$procs->max_physical," physical CPUs.\n";
         }
         (my $FORMAT =   "%2s  %-8s     %4s    \n") =~ s/\s\s+/ /g;
         printf($FORMAT, "#", "STATE", "CLOCK",  "TYPE", );
         foreach my $proc (@{$procs->processors}) {
             printf ($FORMAT, $proc->id, $proc->state, $proc->clock, $proc->type);
	print "\nid do proc eh = '".$proc->id."'\n";
         }

