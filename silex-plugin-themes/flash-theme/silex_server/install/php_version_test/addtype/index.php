<?php
	require_once("../../../rootdir.php");
	require_once("../htaccess_updater_for_php_version.php");
	tryToFixHtaccessForPhpVersion(".htaccess", ROOTPATH . "/.htaccess");
	exit(0);
?>