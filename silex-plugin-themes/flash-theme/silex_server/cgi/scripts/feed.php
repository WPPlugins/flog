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

if(!isset($_GET["id_site"]) || !isset($_GET["query"]))
{
?>
	<h1>feed.php help page</h1>
	<b>Desription:</b>
	<br>
	This script search all the media of a given website, which have been indexed by using the "index" in the manager of Silex websites. It returns a RSS feed of results.
	Pass the following parameters in the URL
	<br>- id_site
	<br>- query
	<br>- limit
	<br>- operator
	<br>- allow_duplicate
	<br>
	<br><b>example:</b>
	<br>feed.php?id_site=open.source.flash.cms&query=context:es&limit=2&allow_duplicate=true
	<br>
	<br><b>result:</b>
	<br>2 first media in the context "es"
<br />--&lt;rss version="2.0"&gt;
<br />----&lt;channel&gt;
<br />------&lt;title&gt;2 results for context:es in open.source.flash.cms&lt;/title&gt;
<br />------&lt;link&gt;http://silex-ria.org:80/open.source.flash.cms/&lt;/link&gt;
<br />------&lt;pubDate&gt;Sat, 19 Dec 2009 13:28:39 +0100&lt;/pubDate&gt;
<br />------&lt;generator&gt;http://www.silex-ria.org&lt;/generator&gt;
<br />------&lt;keywords&gt;context:es&lt;/keywords&gt;
<br />--------&lt;item&gt;
<br />------------&lt;title&gt;&lt;![CDATA[telecharger]]&gt;&lt;/title&gt;
<br />------------&lt;deeplink&gt;&lt;![CDATA[silex/telecharger/]]&gt;&lt;/deeplink&gt;
<br />------------&lt;urlBase&gt;&lt;![CDATA[http://localhost:8888/zzzsilex-ria/open.source.flash.cms/]]&gt;&lt;/urlBase&gt;
<br />------------&lt;context&gt;&lt;![CDATA[es]]&gt;&lt;/context&gt;
<br />------------&lt;text&gt;&lt;![CDATA[[[http://sourceforge.net/project/platformdownload.php?group_id=192954|DOWNLOAD SILEXsobre Sourceforge.net]]silex_server package]]&gt;&lt;/text&gt;
<br />------------&lt;htmlEquivalent&gt;&lt;![CDATA[&lt;p&gt;&lt;TEXTFORMAT LEADING="2"&gt;&lt;P ALIGN="CENTER"&gt;&lt;FONT FACE="din" SIZE="35" COLOR="#CC0000" LETTERSPACING="0" KERNING="0"&gt;[[http://sourceforge.net/project/platformdownload.php?group_id=192954|&lt;U&gt;DOWNLOAD SILEX&lt;/U&gt;&lt;/FONT&gt;&lt;/P&gt;&lt;/TEXTFORMAT&gt;&lt;/p&gt;]]&gt;&lt;/htmlEquivalent&gt;
<br />------------&lt;exactDeeplink&gt;&lt;![CDATA[silextelecharger]]&gt;&lt;/exactDeeplink&gt;
<br />------------&lt;pubDate&gt;&lt;![CDATA[Sat, 19 Dec 2009 13:28:39 +0100]]&gt;&lt;/pubDate&gt;&lt;description&gt;&lt;![CDATA[&lt;p&gt;&lt;TEXTFORMAT LEADING="2"&gt;&lt;P ALIGN="CENTER"&gt;&lt;FONT FACE="din" SIZE="35" COLOR="#CC0000" LETTERSPACING="0" KERNING="0"&gt;[[http://sourceforge.net/project/platformdownload.php?group_id=192954|&lt;U&gt;DOWNLOAD SILEX&lt;/U&gt;&lt;/FONT&gt;&lt;/P&gt;&lt;/TEXTFORMAT&gt;&lt;/p&gt;]]&gt;&lt;/description&gt;&lt;link&gt;&lt;![CDATA[http://silex-ria.org:80/open.source.flash.cms/silex/telecharger/]]&gt;&lt;/link&gt;
<br />--------&lt;/item&gt;
<br />--------&lt;item&gt;
<br />------------&lt;title&gt;&lt;![CDATA[Présentation]]&gt;&lt;/title&gt;
<br />------------&lt;deeplink&gt;&lt;![CDATA[silex/a.propos/histoire/presentation/]]&gt;&lt;/deeplink&gt;
<br />------------&lt;urlBase&gt;&lt;![CDATA[http://localhost:8888/silex-ria/silex-ria.org/website/open.source.flash.cms/]]&gt;&lt;/urlBase&gt;
<br />------------&lt;context&gt;&lt;![CDATA[es]]&gt;&lt;/context&gt;
<br />------------&lt;text&gt;&lt;![CDATA[En 2003, Lex crea una utilidad, MOFI que permite a los internautas interactuar en un entorno FLASH. Intercambiar textos se resuelve rápido. Aparece el problema de intercambiar ficheros y permitir a los usuarios publicar textos, fotos, vídeos, sonidos, un entorno sencillo...]]&gt;&lt;/text&gt;
<br />------------&lt;htmlEquivalent&gt;&lt;![CDATA[&lt;p&gt;&lt;TEXTFORMAT LEADING="2"&gt;&lt;P ALIGN="LEFT"&gt;&lt;FONT FACE="_sans" SIZE="13" COLOR="#333333" LETTERSPACING="0" KERNING="0"&gt;En 2003, Lex crea una utilidad, MOFI que permite a los internautas interactuar en un entorno FLASH. Intercambiar textos se resuelve rápido. Aparece el problema de intercambiar ficheros y permitir a los usuarios publicar textos, fotos, vídeos, sonidos, un entorno sencillo que facilite estas tareas.&lt;/FONT&gt;&lt;/P&gt;&lt;/TEXTFORMAT&gt;&lt;/p&gt;]]&gt;&lt;/htmlEquivalent&gt;
<br />------------&lt;exactDeeplink&gt;&lt;![CDATA[silexaproposhistoirepresentation]]&gt;&lt;/exactDeeplink&gt;

<br />------------&lt;pubDate&gt;&lt;![CDATA[Sat, 19 Dec 2009 13:28:39 +0100]]&gt;&lt;/pubDate&gt;&lt;description&gt;&lt;![CDATA[&lt;p&gt;&lt;TEXTFORMAT LEADING="2"&gt;&lt;P ALIGN="LEFT"&gt;&lt;FONT FACE="_sans" SIZE="13" COLOR="#333333" LETTERSPACING="0" KERNING="0"&gt;En 2003, Lex crea una utilidad, MOFI que permite a los internautas interactuar en un entorno FLASH. Intercambiar textos se resuelve rápido. Aparece el problema de intercambiar ficheros y permitir a los usuarios publicar textos, fotos, vídeos, sonidos, un entorno sencillo que facilite estas tareas.&lt;/FONT&gt;&lt;/P&gt;&lt;/TEXTFORMAT&gt;&lt;TEXTFORMAT LEADING="2"&gt;&lt;P ALIGN="LEFT"&gt;&lt;FONT FACE="_sans" SIZE="13" COLOR="#333333" LETTERSPACING="0" KERNING="0"&gt;&lt;/FONT&gt;&lt;/P&gt;&lt;/TEXTFORMAT&gt;&lt;/p&gt;]]&gt;&lt;/description&gt;&lt;link&gt;&lt;![CDATA[http://silex-ria.org:80/open.source.flash.cms/silex/a.propos/histoire/presentation/]]&gt;&lt;/link&gt;
<br />--------&lt;/item&gt;--&lt;/channel&gt;
<br />--&lt;/rss&gt;
<?php
	exit(0);
}
// **
// includes
set_include_path(get_include_path() . PATH_SEPARATOR . "../../");
include_once 'cgi/includes/silex_search.php';
include_once 'cgi/includes/server_config.php';
require_once("cgi/includes/logger.php");
require_once("cgi/includes/file_system_tools.php");


// create search object
$silex_search_obj=new silex_search();

$server_config = new server_config(); 
$fst = new file_system_tools();
$logger = new logger("feed");
// **
// inputs
// id_site
if (isset($_GET["id_site"]))
	$id_site=$_GET["id_site"];
else
	$id_site=$server_config->silex_server_ini["DEFAULT_WEBSITE"];

// maximum number of results
if (isset($_GET["limit"]))
	$limit=(int)($_GET["limit"]);
else
	$limit=0;

// default operator (and / or)
if (isset($_GET["operator"])){
	switch(strtolower($_GET["operator"])){
		case "or":
			$operator=Zend_Search_Lucene_Search_QueryParser::B_OR;
			break;
		case "and":
			$operator=Zend_Search_Lucene_Search_QueryParser::B_AND;
			break;
		default:
			$operator=Zend_Search_Lucene_Search_QueryParser::B_OR;
	}
}
else
	$operator=Zend_Search_Lucene_Search_QueryParser::B_OR;

Zend_Search_Lucene_Search_QueryParser::setDefaultOperator($operator);

// build website contentent folder
$websiteContentFolder="../../".$server_config->silex_server_ini["CONTENT_FOLDER"].$id_site."/";
// check rights
if ($fst->checkRights($fst->sanitize($websiteContentFolder),constant("file_system_tools::USER_ROLE"),constant("file_system_tools::READ_ACTION"))){

	if (isset($_GET["allow_duplicate"]) && $_GET["allow_duplicate"]=="true")
		$allow_duplicate=true;
	else
		$allow_duplicate=false;

	// get query
	$query=stripslashes(strip_tags(urldecode($_GET['query'])));

	// compute url base
	$scriptUrl=$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];
	$lastSlashPos=strrpos($scriptUrl,$server_config->silex_server_ini["CGI_SCRIPTS_FOLDER"]."feed.php");
	$urlBase="http://".substr($scriptUrl,0,$lastSlashPos).$id_site."/";

	// **
	// search
	$res=$silex_search_obj->find($websiteContentFolder."/",$query,$limit);
}
else{
	$logger->emerg("feed.php no rights to read $websiteContentFolder");
	echo "feed.php no rights to read $websiteContentFolder";
	exit(0);
}

//**
// echo rss
header('Content-Type: text/xml; charset=UTF-8');
$indexFolder=$server_config->silex_server_ini["CONTENT_FOLDER"].$id_site."/search_index/";
if (is_dir($indexFolder))
	$pubDate=date ("r",filemtime($indexFolder));
else
	$pubDate=date ("r");
echo '<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
	<channel>
		<title>'.count($res).' results for '.$query.' in '.$id_site.'</title>
		<link>'.$urlBase.'</link>
		<pubDate>'.$pubDate.'</pubDate>
		<generator>http://www.silex-ria.org</generator>
		<keywords>'.$query.'</keywords>';
echo $silex_search_obj->arrayToRssItems($res,$allow_duplicate,$urlBase,$pubDate);
echo '	</channel>
</rss>';
?>

