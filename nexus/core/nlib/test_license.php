<?php

include "common.nx";

$a = new License();
echo $a->request_license();
echo "#############################################\n";
$b = new Checklicense();
$b->open_license();



?>
