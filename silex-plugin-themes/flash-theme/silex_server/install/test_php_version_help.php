<?php
	require_once("../rootdir.php");
	require_once("localisation.php");
	$loc = new localisation();	
	echo $loc->getLocalised("TEST_PHP_VERSION_HELP");

?>
