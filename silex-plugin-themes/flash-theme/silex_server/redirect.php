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
set_include_path(get_include_path() . PATH_SEPARATOR . './');
set_include_path(get_include_path() . PATH_SEPARATOR . './cgi/library/');
require_once('rootdir.php');
include_once 'cgi/includes/server_config.php';
include_once 'cgi/includes/site_editor.php';
include_once 'cgi/includes/server_content.php';
require_once('cgi/includes/logger.php');
require_once('cgi/includes/file_system_tools.php');
require_once('cgi/includes/site_editor.php');

$serverConfig = new server_config(); 
$serverContent = new server_content();
$siteEditor = new site_editor();
$fst = new file_system_tools();
$logger = new logger('main silex index');


// **
// inputs
// from: http://localhost/_silex/_sourceforgeSVN/silex_server/?path=default/start/aaa
// to: http://localhost/_silex/_sourceforgeSVN/silex_server/#default/start/aaa
$path=$_GET['path'];

// add trailing slash if needed
$isTrailingSlashMissing=false;
if (substr($path, -1, 1) != '/'){
//	$path.='/';
	$isTrailingSlashMissing=true;
}

////////////////////////////////////////////////////
//echo $path; exit(0);
// $id_site = from start to 1st slash
$pos=strpos($path,'/');
if ($pos){
	$id_site=substr($path,0,$pos);
	// $hash = from 1st slash to the end
	$hash=substr($path,$pos);
	// remove ending slash in hash
	if (substr($hash,strlen($hash)-1)=='/') $hash=substr($hash,0,strlen($hash)-1);

	$relativePath='';
}
else{
	// ERROR OF URL REWRITE IN THIS CASE
	$id_site=$path;
	$hash='';

	$relativePath='./';
}
// **
// workaround bug http://silex-ria.org/intro/aide/silex -> http://silex-ria.org/#intro/aide/silex
if (substr($id_site,0,1)=='/')
	$id_site=substr($id_site,1);
// **

// no redirect if no id_site => // if(!$id_site || $id_site='') $id_site='default';

// relativePath
$nSlashes=substr_count($path,'/');
//if ($isTrailingSlashMissing==true) $nSlashes--;
for ($idx=0;$idx<$nSlashes;$idx++) $relativePath.='../';

// used to pass isRedirect to javascript
$isRedirect=TRUE;

// **
// retrieve website config data
$websiteConfig=$siteEditor->getWebsiteConfig($id_site);

// redirect to 404 website
/*if (!$websiteConfig)
{
	$id_site = $serverConfig->silex_server_ini['DEFAULT_ERROR_WEBSITE'];
	$path = $id_site;
	$hash = '';
	$websiteConfig=$siteEditor->getWebsiteConfig($id_site);
}*/


// HTML KEYWORDS, TITLE, ...
// $sectionName = from last slash to the end of hash
$sectionName=substr($hash,strrpos($hash,'/')+1);
$deeplink=substr($hash,1);

// redirect
//$newUrl='location:'.$relativePath.'?silex=/'.$id_site.'#'.$hash;
//header($newUrl);

// $newUrl=$relativePath.'#'.$id_site.$hash;

//echo $newUrl.'<br>';
// pass redirect command to javascript
//$str.="void(window.location='$newUrl');";
	
$newUrl=$relativePath.'?/'.$id_site.'/#'.$hash;

// title
$websiteTitle=$websiteConfig['htmlTitle'];

// icon
//$favicon='media/silex.ico';
$favicon='';
if (isset($websiteConfig['htmlIcon']) && $websiteConfig['htmlIcon']!='')
	$favicon='<link rel="shortcut icon" href="'.$websiteConfig['htmlIcon'].'"><link rel="icon" href="'.$websiteConfig['htmlIcon'].'">';

// main rss feed
//$mainRssFeed='cgi/scripts/feed.php?id_site='.$id_site;
$mainRssFeed='';
if (isset($websiteConfig['mainRssFeed']) && $websiteConfig['mainRssFeed']!='')
	$mainRssFeed='<link rel="alternate" type="application/rss+xml" title="RSS" href="'.$websiteConfig['mainRssFeed'].'">';
// htmlKeywords
$websiteKeywords=$websiteConfig['htmlKeywords'];
// language
$defaultLanguage=$websiteConfig['DEFAULT_LANGUAGE'];

// get the HTML KEYWORDS, TITLE, ...
//echo 'getSectionSeoData($id_site,'.$websiteConfig['CONFIG_START_SECTION'].')';
$linksUrlBase = "";
if ($serverConfig->silex_server_ini['USE_URL_REWRITE'] == 'true')
	$linksUrlBase = $ROOTURL.$id_site.'/';
else
	$linksUrlBase = $ROOTURL.'?/'.$id_site.'/';

$seoDataHomePage=$siteEditor->getSectionSeoData($id_site, $websiteConfig['CONFIG_START_SECTION'], $linksUrlBase);
if (isset($deeplink) && $deeplink!='')
	$seoData=$siteEditor->getSectionSeoData($id_site,$deeplink,$linksUrlBase);
else
{
	$seoData = $seoDataHomePage;
	$deeplink = '';
}
	
if (!$seoData){
	//echo "Section not found: $id_site, $hash, $sectionName";
	$hash='';
}
// html and SEO init
$htmlTitle=$seoDataHomePage['title'].' - '.$seoData['title'];
$htmlDescription=$seoData['description'];
$htmlTags=$seoData['tags'];
$htmlEquivalent='<H4>This page content</H4><br>'.($seoData['htmlEquivalent']);
$htmlKeywords='<H4>Website keywords</H4><br>'.($seoDataHomePage['description']).'<H4>This page keywords</H4><br>'.($seoData['description']);
// add a link to the home page
$htmlLinks='<h1>navigation</h1>'.$id_site.' > '.$deeplink.'<h4><a href="'.$linksUrlBase.$websiteConfig['CONFIG_START_SECTION'].'/">Home page: '.($seoDataHomePage['title']).'</a></h4>';
// links of this page
$htmlLinks.='<H4>Links of this page ('.($seoData['title']).')</H4>';
if (isset($seoData['links'])) $htmlLinks.=$seoData['links'];

	$scriptUrl=$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
	$lastSlashPos=strrpos($scriptUrl,$id_site.'/'.$deeplink);
	$urlBase='http://'.substr($scriptUrl,0,$lastSlashPos);

/**/
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title><?php echo $websiteTitle." - ".$htmlTitle; ?></title>
		<meta http-equiv="cache-control" content="must-revalidate, pre-check=0, post-check=0, max-age=0">
		<meta http-equiv="Last-Modified" content="<?php echo gmdate('D, d M Y H:i:s').' GMT'; ?>">
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta http-equiv="description" content="<?php echo $websiteTitle.' - '.$htmlDescription; ?>">
		<meta http-equiv="keywords" content="<?php echo $htmlTags.' '.$websiteKeywords; ?>">
		<meta HTTP-EQUIV="content-language" CONTENT="<?php echo $defaultLanguage; ?>">
		<meta HTTP-EQUIV="Generator" CONTENT="Silex <?php include 'version.txt'; ?>">
		<meta name="robots" content="INDEX/FOLLOW">
		
		<?php echo $mainRssFeed; ?>
		<?php echo $favicon; ?>
	</head>
	<body style="padding:0px;height:100%; margin-top:0; margin-left:0; margin-bottom:0; margin-right:0; background-color:#<?php echo $websiteConfig['bgColor']; ?>">
		<!--[if IE]>
		<script type="text/javascript" event="FSCommand(command,args)" for="silex">
		  silex_DoFSCommand(command, args);
		</script>
		<![endif]-->
		<noscript>
		      <?php

		            $param = Array(
                        'movie' => $relativePath.'loader.swf?flashId=silex',
                        'src' => $relativePath.'loader.swf?flashId=silex',
                        'type'=>'application/x-shockwave-flash',
                        'bgColor' => $websiteConfig['bgColor'],
                        'pluginspage'=>'http://www.adobe.com/products/flashplayer/',
                        //'codebase' => 'http://www.adobe.com/products/flashplayer/',
                        'scale' => 'noscale',
                        'swLiveConnect' => 'true',
                        'AllowScriptAccess' => 'always',
                        'quality' => 'best',
                        'wmode' => 'transparent',
                        'FlashVars' => ''
                    );

                    $flashVars = Array(
                        'ENABLE_DEEPLINKING' => 'false',
                        'DEFAULT_WEBSITE' => $serverConfig->silex_server_ini['DEFAULT_WEBSITE'],
                        'id_site' => $id_site,
                        'php_website_config_file' => $serverConfig->silex_server_ini['CONTENT_FOLDER'].$id_site.'/'.$serverConfig->silex_server_ini['WEBSITE_CONF_FILE'],
                        'config_files_list' => $serverConfig->silex_server_ini['CONTENT_FOLDER'].$id_site.'/'.$serverConfig->silex_server_ini['WEBSITE_CONF_FILE'] . ',' . $serverConfig->silex_server_ini['SILEX_CLIENT_CONF_FILES_LIST'],
                        'flashPlayerVersion' => isset($websiteConfig['flashPlayerVersion']) ? $websiteConfig['flashPlayerVersion'] : '7',
                        'CONFIG_START_SECTION' => isset($websiteConfig['CONFIG_START_SECTION']) ? $websiteConfig['CONFIG_START_SECTION'] : 'start',
                        'SILEX_ADMIN_AVAILABLE_LANGUAGES' => $serverContent->getLanguagesList(),
                        'SILEX_ADMIN_DEFAULT_LANGUAGE' => $serverConfig->silex_server_ini['SILEX_ADMIN_DEFAULT_LANGUAGE'],
                        'htmlTitle' => $websiteTitle,
                        'preload_files_list' => $websiteConfig['layoutFolderPath'].$websiteConfig['initialLayoutFile'].',fp'.$websiteConfig['flashPlayerVersion'].'/'.$websiteConfig['layerSkinUrl'],
                        'forceScaleMode' => 'showAll',
                        'silex_result_str' => '_no_value_',
                        'silex_exec_str' => '_no_value_',
						'rootUrl' => $urlBase
                    );
					if (isset($websiteConfig['PRELOAD_FILES_LIST']))
						$flashVars['preload_files_list'] .= ','.$websiteConfig['PRELOAD_FILES_LIST'];

                    $fV_String='';
                    foreach( $flashVars as $key => $var ){
                        $param['FlashVars'] .= $key . '=' . $var;
                        $param['FlashVars'] .= sizeof( $fV_String ) > 0 ? '&' : '';
                    }
                ?>
             <object id="silex"  classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="100%"  height="100%" standby="Loading... Please wait.">
				<?php
						$Param_String = '';
						foreach( $param as $key => $var ){
                        if($key != 'src' && $key != 'pluginspage')
                            echo "\n                <param name=\"$key\" value=\"$var\">";
                        if($key != 'movie' && $key != 'codebase')
                            $Param_String .= ' ' . $key . "=\"$var\"";
                    }
                ?>
                <embed height="100%" width="100%"<?php echo $Param_String;?>></embed>
				<noembed>
					<iframe frameborder="0" height="100%" width="100%" src="<?php echo $relativePath; ?>no-flash.html">Your browser doesnt support Frames. Update your browser to watch this page.</iframe>
					<?php echo $htmlLinks.'<p><br>website keywords <br></p>'.$websiteKeywords.'<p><br>page keywords <br></p>'.$htmlKeywords.'<p><br>HTML EQUIVALENT:</p>'.$htmlEquivalent; ?>
					<a href="http://silex-ria.org">powered by silex <?php include 'version.txt'; ?></a>
					<br><a href="http://silexlabs.com">released by the Silex team</a>
					<a href="<?php echo $relativePath; ?>sitemap.php?id_site=<?php echo $id_site; ?>"><?php echo $websiteTitle; ?> - sitemap</a>
				</noembed>
			</object>
		</noscript>
		<script type="text/javascript">
			window.location = "<?php echo $newUrl; ?>";
		</script>
	</body>
</html>
