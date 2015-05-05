<?php

#include "phpinfo.nx"

//show_source ("phpinfo.nx");
//print_r(unserialize('a:3:{s:6:"connid";s:2:"oi";s:4:"info";s:3:"oii";s:4:"func";s:4:"INFO";}'));

echo get_include_path();

include "common.nx";

echo "incore:".INCORE."\n";
echo "nexux:".NEXUS;

//include_once "cls.nx";

echo conv::randkey();


?>
