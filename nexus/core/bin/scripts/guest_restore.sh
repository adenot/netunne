#!/bin/bash

. /etc/nexus/path

cd /$NEXUS/core/nlib
declare -x NEXUS=$NEXUS;$PHP -q /$NEXUS/core/nlib/task_timelimit.nx restore
