<?php

include_once "common.nx";

//echo microtime();echo "\n";
$user = new Forward();
//echo microtime();

/*
for ($i=0;$i<count($user->users); $i++) {
	echo ".";
	//$usersid[$user->users[$i][login]]=$i;
}
echo "\n";

echo microtime();
*/

print_r($user->returnacl(2));

//print_r($user->merge());

//print_r($user->user_out);

//print_r($user->merge_user("spike"));

//echo conv::cleanout($user->user_out[spike]);

//$user->createuser_route("spike");

//$obj=new Object();
//print_r($obj->get("`INTERFACE.INTERNAL`"));

//var_dump(Forward::validuser("teste21"));

//echo Forward::guesttotal("abc");

/*
echo "initializing..\n";
$user->opencbqnum();
$user->printcbqnum();

$user->freecbqnum("b");
$user->freecbqnum("c");
$user->freecbqnum("a");



$user->printcbqnum();
$user->filecbqnum();
*/

//print_r($user->trackchanges());

?>
