<?php
/*
	this file is part of SILEX
	SILEX : RIA developement tool - see http://silex-ria.org/

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
// read silex_server.ini
$silex_server_ini=parse_ini_file("conf/silex_server.ini", false);;

// compute url base
if (isset($_GET["scriptUrl"]))
	// passed  by .htaccess url rewrite
	$scriptUrl=$_GET["scriptUrl"];
else
	// from server variables
	$scriptUrl=$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];

$lastSlashPos=strrpos($scriptUrl,"sitemap_index");
$urlBase="http://".substr($scriptUrl,0,$lastSlashPos);
// display websites in sitemap format
header('Content-type: application/xml; charset="utf-8"',true);
echo '<?xml version="1.0" encoding="UTF-8"?>
	<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
';

//2004-10-01T18:23:17+00:00
$tmpFolder = opendir($silex_server_ini["CONTENT_FOLDER"]);
while ($tmpFile = readdir($tmpFolder)){
	$segmentFolder=$silex_server_ini["CONTENT_FOLDER"].$tmpFile."/search_index/";
	if ($tmpFile!="." && $tmpFile!=".." && is_dir($segmentFolder)){
		echo "		<sitemap>
		<loc>".$urlBase."sitemap.php?id_site=$tmpFile</loc>
";
		echo "			<lastmod>".date ("Y-m-d",filemtime($segmentFolder))."</lastmod>
";
		echo "		</sitemap>
";
	}
}
echo '</sitemapindex>'
?>