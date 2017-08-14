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
// php 5 needed
/*
if (version_compare(PHP_VERSION,'5','<')){
	echo "your php version is too old for SILEX. You need php 5 or older. Your php version is ".PHP_VERSION.". Check <a href='install'>SILEX install here</a> and <a href='http://silex-ria.org/help/documentation/installation/'>the install section of the documentation here</a>";
	exit(0);
}
*/
// ROOTURL is calculated in rootdir.php
global $ROOTURL;

// **
// includes
require_once(dirname(__FILE__).'/rootdir.php');
set_include_path(get_include_path() . PATH_SEPARATOR . ROOTPATH);
set_include_path(get_include_path() . PATH_SEPARATOR . ROOTPATH.'cgi/library/');

// $isDefaultWebsite is true if there was no id_site in get or post data.
$isDefaultWebsite = false;

$do_redirect = false;

// Retrieve the site name from URL
if (strpos($_SERVER['REQUEST_URI'],'?/')>0){
	/*
		ROOTURL is calculated in rootdir.php
	$ROOTURL = substr($_SERVER['REQUEST_URI'],0,$maxlen-2);
	*/
	$maxlen = strlen($_SERVER['SCRIPT_NAME'])-strlen('index.php')+2;
	// pretty permalinks
	if (substr($_SERVER['REQUEST_URI'], -1, 1) != '/'){
		$url = substr($_SERVER['REQUEST_URI'],$maxlen);
	}
	else{
		$url = substr($_SERVER['REQUEST_URI'],$maxlen,-1);
	}
	// if the id is of the form "mysite/mypage1/.../mypageX", reload with "mysite/#/mypage1/.../mypageX"
	$tab_url = explode ('/',$url);
	$id_site = array_shift($tab_url);
	$deeplink= implode ('/',$tab_url);
	//echo $ROOTURL.' - '.$id_site.' - '.$deeplink;
	if($deeplink!='') 
		$do_redirect = true;
}
else{
	// is the site name in GET or POST
	if (isset($_POST['id_site']))
		$id_site=$_POST['id_site'];
	else
		if (isset($_GET['id_site']))
			$id_site=$_GET['id_site'];
		else
		{
			$isDefaultWebsite = true;
	//		$id_site=$serverConfig->silex_server_ini['DEFAULT_WEBSITE'];
		}
}

//check if installer ran. We should use the password_manager class with isAuthenticationFileAvailable, but since this is the main page keep it light
if(!file_exists(ROOTPATH.'conf/pass.php') || version_compare(PHP_VERSION,'5','<')){
	?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML><HEAD>
<title>Silex php error</title>
<meta http-equiv="REFRESH" content="0;url=<?php echo $ROOTURL; ?>install">
</HEAD></HTML><?php
	exit(0);
}
// Here we have the site name and Silex is installed
// include Silex classes in order to build the html page
include_once ROOTPATH.'cgi/includes/server_config.php';
include_once ROOTPATH.'cgi/includes/site_editor.php';
include_once ROOTPATH.'cgi/includes/server_content.php';
require_once ROOTPATH.'cgi/includes/logger.php';
require_once ROOTPATH.'cgi/includes/file_system_tools.php';
require_once ROOTPATH.'cgi/includes/site_editor.php';
require_once ROOTPATH.'cgi/includes/hook_manager.php';

// instanciate the classes
$serverConfig = new server_config(); 
$serverContent = new server_content();
$siteEditor = new site_editor();
$fst = new file_system_tools();
$hook_manager = new hook_manager();
$logger = new logger('main silex index');

// $SILEX_API_JS_SCRIPT_FOLDER_URL can be overriden with the "index-head" hook
global $SILEX_API_JS_SCRIPT_FOLDER_URL;
$SILEX_API_JS_SCRIPT_FOLDER_URL = $ROOTURL.$serverConfig->silex_server_ini['JAVASCRIPT_FOLDER'];

// plugins
if (isset($serverConfig->silex_server_ini['PLUGINS_LIST'])){
	$pluginsArray = explode(',',$serverConfig->silex_server_ini['PLUGINS_LIST']);
	foreach($pluginsArray as $pluginName){
		require_once(ROOTPATH.$pluginName);
	}
}

// if no id_site has been found
if ($isDefaultWebsite) $id_site=$serverConfig->silex_server_ini['DEFAULT_WEBSITE']; 
else if ($id_site == $serverConfig->silex_server_ini['DEFAULT_WEBSITE']) $isDefaultWebsite = true;
// **
// inputs
// PASS POST AND GET DATA TO FLASH and JS
$js_str='';
$fv_js_object='';
while( list($k, $v) = each($_GET) ){if($k && $v){ $fv_js_object.=($fv_js_object==''?'':',').$k.' : \''.$v.'\''; $js_str.='$'.$k.' = \''.$v.'\'; ';}}
while( list($k, $v) = each($_POST) ){if($k && $v){ $fv_js_object.=($fv_js_object==''?'':',').$k.' : \''.$v.'\''; $js_str.='$'.$k.' = \''.$v.'\'; ';}}
//echo "........................".$js_str;
//echo "<br>........................".$str;

// retrieve id_site from POST or GET
//echo $_SERVER['REQUEST_URI'].'----------'.$_SERVER['SCRIPT_NAME'];


// **
// retrieve website config data
global $websiteConfig; 
$websiteConfig = $siteEditor->getWebsiteConfig($id_site);
//echo id_site." - ".$websiteConfig['CONFIG_START_SECTION'];
// redirect to 404 website
if (!$websiteConfig)
{
	$id_site = $serverConfig->silex_server_ini['DEFAULT_ERROR_WEBSITE'];
	//$websiteConfig=$siteEditor->getWebsiteConfig($id_site);
	
/*	$scriptUrl=$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
	$lastSlashPos=strrpos($scriptUrl,'widget.php');
	$newUrl = 'http://'.substr($scriptUrl,0,$lastSlashPos).$id_site;
*/	header('HTTP/1.1 301 Moved Permanently'); 
	header('Location:'.$ROOTURL.'?/'.$id_site); 
	header('Connection: close'); 
	exit;
	
	// $websiteConfig['ENABLE_DEEPLINKING'] = 'false';
	// $str.="fo.addVariable('id_site', '".$id_site."');"; $js_str.="$"."id_site"." = '".$id_site."'; ";
}

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

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html style="overflow:auto; height:100%;margin:0px;padding:0px;">
	<head>
		<?php 
			$hook_manager->call_hooks('index-head');
		?>
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

		<script type="text/javascript" src="<?php echo $SILEX_API_JS_SCRIPT_FOLDER_URL; ?>silex_api.min.js"></script>
		<script type="text/javascript" src="<?php echo $SILEX_API_JS_SCRIPT_FOLDER_URL; ?>hook.min.js"></script>
		<script type="text/javascript">
			// default value for root URL is the one we may have set before this line. If it is not defined yet we take the value of root URL of php
			var $rootUrl;
			if(!$rootUrl) $rootUrl='<?php echo $ROOTURL; ?>';
			// If it is not defined yet we initialize it with the default value
			var $id_site;
			if(!$id_site) $id_site='<?php echo $id_site; ?>';
			// If it is not defined yet we initialize it with the default value
			var javascriptFolderUrl;
			if(!javascriptFolderUrl) javascriptFolderUrl= '<?php echo $SILEX_API_JS_SCRIPT_FOLDER_URL; ?>';
			
			// pass post and get data to js
			eval("<?php echo $js_str; ?>");
			
			<?php if($do_redirect==true) 
				{
			?>
					try
					{
				<?php
					// skip id_site for default website
					if ($isDefaultWebsite == true) { ?>
						window.location = '<?php echo $ROOTURL.'/#/'.$deeplink; ?>';
					<?php }else{ ?>
						window.location = '<?php echo $ROOTURL.'?/'.$id_site.'/#/'.$deeplink; ?>';
				<?php }
				?>
					}
					catch($e)
					{
						// we have no access to url hash
					}
			<?php
				} ?>
		</script>
	</head>
	<body style="overflow:auto; padding:0px;height:100%; margin-top:0; margin-left:0; margin-bottom:0; margin-right:0;" onload="onLoadBodyCallback();">
		<!--[if IE]>
		<script type="text/javascript" event="FSCommand(command,args)" for="silex">
		  silex_DoFSCommand(command, args);
		</script>
		<![endif]-->
		<div style="position: absolute; z-index: 1000;" id="frameContainer" name="frameContainer"></div>
		<div id="flashcontent" name="flashcontent" align="center" style="overflow:auto; position: absolute; z-index: 0; width: 100%; height: 100%;">
			<noscript>
				<?php 
					// to do : put this in cgi/includes/silex_api.php
					$param = Array(
					'movie' => $ROOTURL.'loader.swf?flashId=silex',
					'src' => $ROOTURL.'loader.swf?flashId=silex',
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
						'ENABLE_DEEPLINKING' => 'false', // will be overriden by the js parameter of silex.js::SilexJsStart
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
						'silex_exec_str' => '_no_value_'
					);
					if (isset($websiteConfig['PRELOAD_FILES_LIST']))
						$flashVars['preload_files_list'] .= ','.$websiteConfig['PRELOAD_FILES_LIST'];

					$fV=0;
					//$fv_js_object='';
					foreach( $flashVars as $key => $var ){
						$param['FlashVars'] .= $key . '=' . $var;
						$param['FlashVars'] .= sizeof( $fV++ ) > 0 ? '&' : '';
						if ($fv_js_object != '') $fv_js_object .= ', ';
						$fv_js_object .= $key . ':"' . $var . '"';
					}
				?>
				<object id="silex"  classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="100%"  height="100%" standby="Loading... Please wait.">
					<?php

						$param_js_object='';
						$Param_String = '';
						foreach( $param as $key => $var ){
							if($key != 'src' && $key != 'pluginspage')
								echo "\n				<param name=\"$key\" value=\"$var\">";
							if($key != 'movie' && $key != 'codebase')
								$Param_String .= ' ' . $key . "=\"$var\"";
							
							if ($param_js_object != '') $param_js_object .= ', ';
							$param_js_object .= $key . ':"' . $var . '"';
						}


					?>
					<embed height="100%" width="100%"<?php echo $Param_String;?>>
					</embed>
					<noembed>
						<?php
							$hook_manager->call_hooks('index-noembed');
						?>
			    			<iframe frameborder="0" height="100%" width="100%" src="<?php echo $ROOTURL; ?>no-flash.html">Your browser doesnt support Frames. Update your browser to watch this page.</iframe>
						<?php echo $htmlLinks.'<p><br>page keywords <br></p>'.$htmlKeywords.'<p><br>HTML EQUIVALENT:</p>'.$htmlEquivalent; ?>
						<a href="http://silex-ria.org">powered by silex <?php include 'version.txt'; ?></a>
						<br><a href="http://silexlabs.com">released by the Silex team</a>
						<a href="<?php echo $ROOTURL; ?>sitemap.php?id_site=<?php echo $id_site; ?>"><?php echo $websiteTitle; ?> - sitemap</a>
					</noembed>
				</object>
			</noscript>
		</div>
		<iframe style="display:none" id="downloadFrame" name="downloadFrame"></iframe>
		<?php if(isset($websiteConfig['googleAnaliticsAccountNumber']) && $websiteConfig['googleAnaliticsAccountNumber']!='') { ?>
		<div id="googleAnalFrame"></div>
		<script type="text/javascript" src="http://www.google-analytics.com/urchin.js"></script>
		<?php } ?>
		<div id="toolsContainerDiv"></div>
		<div id="stats"></div>
		<script type="text/javascript">
			<?php if($do_redirect==false) { ?>
				$no_flash_page = undefined;
				$php_str = '';
				$additional_flashvars = '';
				$enableDeeplinking = "<?php if(isset($websiteConfig['ENABLE_DEEPLINKING'])) echo $websiteConfig['ENABLE_DEEPLINKING']; else echo 'true'; ?>";
				$DEFAULT_WEBSITE="<?php echo $serverConfig->silex_server_ini['DEFAULT_WEBSITE']; ?>";
				$php_id_site="<?php echo $id_site; ?>";
				$php_website_conf_file="<?php echo $serverConfig->silex_server_ini['CONTENT_FOLDER'].$id_site.'/'.$serverConfig->silex_server_ini['WEBSITE_CONF_FILE']; ?>";

				$SILEX_CLIENT_CONF_FILES_LIST=$php_website_conf_file + "," + "<?php echo $serverConfig->silex_server_ini['SILEX_CLIENT_CONF_FILES_LIST']; ?>";
				$flashPlayerVersion="<?php if(isset($websiteConfig['flashPlayerVersion'])) echo $websiteConfig['flashPlayerVersion']; else echo '7'; ?>";
				$CONFIG_START_SECTION="<?php if(isset($websiteConfig['CONFIG_START_SECTION'])) echo $websiteConfig['CONFIG_START_SECTION']; else echo 'start'; ?>";
				$SILEX_ADMIN_AVAILABLE_LANGUAGES="<?php //echo $serverContent->getLanguagesList(); ?>";
				$SILEX_ADMIN_DEFAULT_LANGUAGE="<?php echo $serverConfig->silex_server_ini['SILEX_ADMIN_DEFAULT_LANGUAGE']; ?>";
				$htmlTitle="<?php echo $websiteTitle; ?>";
				$preload_files_list="<?php echo $websiteConfig['layoutFolderPath'].$websiteConfig['initialLayoutFile'].',fp'.$websiteConfig['flashPlayerVersion'].'/'.$websiteConfig['layerSkinUrl']; 
				if (isset($websiteConfig['PRELOAD_FILES_LIST']))
					echo ','.$websiteConfig['PRELOAD_FILES_LIST'];?>";
				$bgColor="#<?php echo $websiteConfig['bgColor']; ?>";
			<?php } ?>

		</script>
			<?php
				$hook_manager->call_hooks('index-script');
			?>
		<script type="text/javascript">
			
			<?php if($do_redirect==false) { ?>
				var onLoadBodyCallbackOccured = false;
				function onLoadBodyCallback()
				{
					// only one time (useful??)
					if (onLoadBodyCallbackOccured == true) {return;}
					onLoadBodyCallbackOccured= true;
					
					// include the javascirpt scripts
					silexNS.SilexApi.addScript(javascriptFolderUrl+"jquery.min.js");
					silexNS.SilexApi.addScript(javascriptFolderUrl+"swfobject.min.js");
					silexNS.SilexApi.addScript(javascriptFolderUrl+"utils.min.js");
					silexNS.SilexApi.addScript(javascriptFolderUrl+"jsframe.min.js");
					silexNS.SilexApi.addScript(javascriptFolderUrl+"deeplink.min.js");
					silexNS.SilexApi.addScript(javascriptFolderUrl+"silex.min.js");
					silexNS.SilexApi.includeJSSCripts(onScriptsLoadedCallback);
				}
				/**
				 * callback function passed to SilexApi::includeJSSCripts
				 * so that it will be called by silex API after all scripts are loaded
				 */
				function onScriptsLoadedCallback()
				{
					// silexJsObj is used for deep link and tracking, it is accessed by plugins and silex core ActionScript framework
					silexJsObj=SilexJsStart(
						$flashPlayerVersion,
						$DEFAULT_WEBSITE,
						$CONFIG_START_SECTION,
						$SILEX_CLIENT_CONF_FILES_LIST,
						$enableDeeplinking,
						$SILEX_ADMIN_DEFAULT_LANGUAGE,
						$SILEX_ADMIN_AVAILABLE_LANGUAGES,
						$htmlTitle,
						$preload_files_list,
						$bgColor,
						$php_str,
						$php_id_site,
						$additional_flashvars, // additional flash vars
						$rootUrl, // rootUrl
						{<?php echo $fv_js_object; ?>},
						{<?php echo $param_js_object; ?>},
						$no_flash_page
					);
							
					// check hash compatibility with previous deeplinking system (id_site was after the hash)
					var websiteStartSection = "<?php echo $websiteConfig['CONFIG_START_SECTION']; ?>";
					var defaultWebsite = "<?php echo $serverConfig->silex_server_ini['DEFAULT_WEBSITE']; ?>";
					var currentHash = getUrlHash(); // comes from deeplin.js
					var isCompatible = silexNS.SilexApi.checkCompatibility(websiteStartSection,defaultWebsite,currentHash,$rootUrl);
				}
			<?php } ?>
		</script>
			<?php
				$hook_manager->call_hooks('index-body-end');
			?>
	</body>
</html>
