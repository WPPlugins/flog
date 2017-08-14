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

<?php
	require_once("../../rootdir.php");
	require_once("../localisation.php");
	$loc = new localisation();

	//get the value for rewrite base. example : if you've installed silex at arielsommeria.com/silex/, returns /silex
	function getRewriteBase(){
		$url = $_SERVER['REQUEST_URI'];
		$parsed = parse_url($url);
		if ( isset( $parsed['path'] ) ) {
			$path = $parsed['path'];
		} else {
			$path = '/';
		}
		$ret = str_replace("cgi/htaccess/link2.html", "", $path);
		//echo $ret;
		return $ret;
	}
	
	function writeModRewrite(){
		$htaccessPath = ROOTPATH . "/.htaccess";
		$rewriteBase = getRewriteBase();
		$template = file_get_contents("rewrite_rules_template.txt");
		//echo $fileContent; 
		$filledTemplate = str_replace("%rewritebase%", $rewriteBase, $template);
		if(file_exists($htaccessPath)){
			$oldHtaccessContent = file_get_contents($htaccessPath);
		}
		$newContent = null;
		if($oldHtaccessContent){
			//htaccess file already exists. try not to destroy it!
			$beginMarkerPos = strpos($oldHtaccessContent, "#silex url rewrite begin");
			
			if($beginMarkerPos !== false){
				//already url rewriting, replace it.
				$endMarkerPos = strpos($oldHtaccessContent, "#silex url rewrite end");
				if($endMarkerPos !== false){
					$newContent = substr_replace($oldHtaccessContent, $filledTemplate, $beginMarkerPos, $endMarkerPos);
				}
			}else{
				//file exists but no url rewriting in it. just append
				$newContent = $oldHtaccessContent . "\n" . $filledTemplate;
			}
		}
		if(!$newContent){
			//old file didn't exist or replacement/appending failed
			$newContent = $filledTemplate;
		}
		file_put_contents($htaccessPath, $newContent);
		
		//$startMarkerPos = strpos(
		//echo "er" . ROOTPATH . "/.htaccess";
		//echo "<br/><br/> " . $fileContent; 
	}
	
	writeModRewrite();
	echo $loc->getLocalised("URL_REWRITING_OK");
	
?>
</body>
</html> 