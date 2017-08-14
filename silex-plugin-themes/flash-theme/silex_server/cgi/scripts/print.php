<html>
<body>
<div align='center'><a href="javascript:self.close('1_medaille');">Fermer</a></div>
<div align='center'><a href="javascript:self.print();">Imprimer</a></div>
<?php
/*
	this file is part of SILEX
	SILEX : RIA developement tool - see http://silex.sourceforge.net/

	SILEX is (c) 2004-2007 Alexandre Hoyau and is released under the GPL License:

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License (GPL)
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.
	
	To read the license please visit http://www.gnu.org/copyleft/gpl.html
*/
//RECUPERATION des VARIABLES
$htmlText_str=stripslashes(urldecode($_POST['htmlText_str']));

echo $htmlText_str;

if (isset($_POST['url_str'])){
	$url_str=$_POST['url_str'];
	echo "<br><br><br>Source : <a href=".stripslashes($url_str).">".stripslashes($url_str)."</a><br><br>";
}
//echo "GET : ".$_GET['url_str'];
//echo "<br>POST : ".$_POST['url_str'];
//echo "&result=Done&";
?>
</body>
</html>
