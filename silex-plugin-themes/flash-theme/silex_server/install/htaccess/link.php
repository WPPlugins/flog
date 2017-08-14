<?php

	require_once("../../rootdir.php");
	require_once("../localisation.php");
	$loc = new localisation();
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<style type="text/css">
		<!--
		body,td,th {
			font-family: Arial, Helvetica, sans-serif;
			font-size: 12px;
			background-color: white;

		}
		.style2 {
			font-size: 19px;
			color: #FFFFFF;
		}
		.style5 {color: #FF9900; font-size: 17px; }
		.style6 {font-size: 17px}
		.style7 {font-size: 16px}
		.style8 {color: #666666}
		-->
		</style>
	</head>
<body>
<?php echo $loc->getLocalised("URL_REWRITING_NOK")?>
<br/>
<span class="style8"><?php echo $loc->getLocalised("URL_REWRITING_NOK_SO_WHAT")?></span><br />