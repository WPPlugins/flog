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
	//RECUP DES VARIABLES
	if (isset($GLOBALS['HTTP_RAW_POST_DATA']))
	{
	    $_AMF_RAW_DATA = str_replace("&","\";$","$".URLDecode($GLOBALS['HTTP_RAW_POST_DATA']));
	    $_AMF_RAW_DATA = str_replace("=","=\"",$_AMF_RAW_DATA)."\";";
	
		//echo "BRUT ".URLDecode($GLOBALS['HTTP_RAW_POST_DATA']);
		//echo "\nCODE ".$_AMF_RAW_DATA;
	
	    eval($_AMF_RAW_DATA);
	
		//echo "FINAL ".$chemin.$fichier;
	}
	else
	{
		if (isset($_GET['initial_name']))
		{
			$final_name=$_GET['final_name'];
			$initial_name=$_GET['initial_name'];
		}
		else
		{
			$final_name=$_POST['final_name'];
			$initial_name=$_POST['initial_name'];
		}
	}
if (!$final_name) $final_name=$initial_name;

// check rights
$silex_server_ini = parse_ini_file("../../conf/silex_server.ini", false);	
/* isInFolder
 * @param filepath			file about to be accessed
 * @param folderName			folder name 
 * @returns true if filepath designate a path which is in the folder corresponding to folderName
 */
function isInFolder($filepath,$folderName){
	//return true;
	//echo realpath("../../".$filepath)." , ".realpath("../../".$folderName)."<br>";
	return (strpos(realpath("../../".$filepath),realpath("../../".$folderName))===0);
}
if (!isInFolder($initial_name,$silex_server_ini["MEDIA_FOLDER"])){
	// error : no right to access this folder
	//echo "error : no right to access this folder";
	exit(0);
}
/* YES FINAL 
header("Content-Type: application/force-download");
header("Content-Length: " .(string)(filesize($myFile)) );
header('Content-disposition: attachment; filename="'.$final_name.'"');
header("Content-Transfer-Encoding: binary");
*/

// from http://fr2.php.net/header
$mm_type="application/octet-stream";
header("Cache-Control: public, must-revalidate");
header("Pragma: hack");
header("Content-Type: " . $mm_type);
header("Content-Length: " .(string)(filesize("../../".$initial_name)) );
header('Content-Disposition: attachment; filename="'.basename($final_name).'"');
header("Content-Transfer-Encoding: binary\n");
// for IE7
header('Vary: User-Agent');


// ecrit direct dans le doc: echo "Download should begin shortly... You can close this window.";
// ecrit direct dans le doc: echo "<script language=\"javascript\">alert ('Download should begin shortly... You can close this window.');</script>";
//echo "<script language=\"javascript\">window.close();</script>";

readfile("../../".$initial_name);
/**/
exit();
?>