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

// **
// includes
set_include_path(get_include_path() . PATH_SEPARATOR . "./");
require_once 'cgi/includes/silex_search.php';
require_once('cgi/includes/logger.php');
require_once('cgi/includes/server_config.php');
require_once('cgi/includes/file_system_tools.php');
include_once 'cgi/includes/server_config.php';
require_once('cgi/includes/site_editor.php');

$logger = new logger('sitemap');
$fst = new file_system_tools();
$siteEditor = new site_editor(); 
$server_config = new server_config(); 

// **
// create search object
$silex_search_obj=new silex_search();

// **
// inputs
if (isset($_GET['id_site']))
	$id_site=$_GET['id_site'];
else
	$id_site=$server_config->silex_server_ini['DEFAULT_WEBSITE'];

// build website contentent folder
$websiteContentFolder='./'.$server_config->silex_server_ini['CONTENT_FOLDER'].$id_site.'/';
// check rights
if ($fst->checkRights($fst->sanitize($websiteContentFolder),constant('file_system_tools::USER_ROLE'),constant('file_system_tools::READ_ACTION'))){

	$allow_duplicate=false;


	// compute url base
	$scriptUrl=$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
	$lastSlashPos=strrpos($scriptUrl,'sitemap.php');
	if ($server_config->silex_server_ini['USE_URL_REWRITE'] == 'true')
		$urlBase='http://'.substr($scriptUrl,0,$lastSlashPos).$id_site.'/';
	else
		$urlBase='http://'.substr($scriptUrl,0,$lastSlashPos).'?/'.$id_site.'/';

	// **
	// search
	$websiteConfig = $siteEditor->getWebsiteConfig($id_site);
	$query=$websiteConfig['CONFIG_START_SECTION'];
	$res=$silex_search_obj->find($websiteContentFolder.'/',$query);
}
else{
	$logger->emerg('feed.php no rights to read '.$websiteContentFolder);
	echo 'feed.php no rights to read '.$websiteContentFolder;
	exit(0);
}

//**
// echo rss
//Wed, 12 Dec 2007 16:06:09 +0100
header('Content-Type: text/xml; charset=UTF-8');
$indexFolder=$server_config->silex_server_ini['CONTENT_FOLDER'].$id_site.'/search_index/';
if (is_dir($indexFolder))
	$pubDate=date ('r',filemtime($indexFolder));
else
	$pubDate=date ('r');

echo '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
		
		// --------- from silex_search.php
		// array to eliminate duplicated deeplinks
		$res_str='';
		$foundDeeplinks=Array();				

		if ($res){
			foreach ($res as $hit) {

				// retrieve record data
				$fields=$hit->getDocument();
				
				// build rss item
				$rssItem=Array();
				
				foreach ($fields->getFieldNames() as $tag) {
					$rssItem[$tag]=$hit->$tag;
				}
				$search==null;
				if (isset($rssItem['deeplink'])){
					$search=array_search($rssItem['deeplink'],$foundDeeplinks);
				}
					
				if ($search!==FALSE && $search!==NULL){
					// deeplink allready seen
					//$this->logger->debug('silex_search.php arrayToRssItems '.$rssItem['deeplink'].' allready seen ');
				}
				else{
					// store the deeplink in $foundDeeplinks
					//$this->logger->debug('silex_search.php arrayToRssItems '.$hit->deeplink.' found ');
					$foundDeeplinks[]=$rssItem['deeplink'];

					$res_str.='
			<url>';
					$res_str.='
				<loc>'.$urlBase.$rssItem['deeplink'].'</loc>';
					//$res_str.='
				//<lastmod>'.$rssItem['pubDate'].'</lastmod>';
					$res_str.='
			</url>';
				}
			}
		}
echo $res_str.'
</urlset>';
?>

