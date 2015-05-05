<?php
	ob_implicit_flush();

	include('msnp9.class.php');
	include('msn_sb.class.php');

	$msn = new msn;

	if ($msn->connect('modtest_im_02@yahoo.com.br', 'robot02'))
	{
		// we're connected
		// run rx_data function to 'idle' on the network
		// rx_state will loop until the connection is dropped

		$msn->rx_data();

		echo "(Connection dropped)\n";
	}
	else
	{
		// wrong username and password?
		echo "(Error Connecting to the MSN Network)\n";
	}


?>