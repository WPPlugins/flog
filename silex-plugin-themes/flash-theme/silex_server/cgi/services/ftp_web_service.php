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
class ftp_web_service
{
	var $silex_server_ini=null;
	var $serverRootPath;
	// constants
	var $itemTypeField="item type";
	var $itemSizeField="item size";
	var $itemNameField="item name";
	var $itemModifDateField="item last modification date";
	// ***********
	// constructor
	function ftp_web_service($argServerRootPath="")
	{
		if (($argServerRootPath!="" && $argServerRootPath!="ftp_web_service")) $this->serverRootPath=$argServerRootPath;
		else $this->serverRootPath="../../";
		
		// read ini file
		$this->silex_server_ini = parse_ini_file($this->serverRootPath."conf/silex_server.ini", false);
		$this->silex_server_ini_with_sections = parse_ini_file($this->serverRootPath."conf/silex_server.ini", true);
		
		// init methodTable for amfphp
		$this->methodTable = array(
			"getFtpContent" => array(
				"description" => "list ftp content",
				"arguments" => array("path" => array("type" => "string","required" => true)),
				"access" => "remote",
				"returns" => "array"
			),
			"listFolderContent" => array(
				"description" => "list a folder content",
				"arguments" => array("path" => array("type" => "string","required" => true)),
				"access" => "private",//"remote",//
				"returns" => "array"
			),
			"renameItem" => array(
				"description" => "rename a file or folder",
				"arguments" => array("path" => array("type" => "string","required" => true), "initial_name" => array("type" => "string","required" => true),"final_name" => array("type" => "string","required" => true)),
				"access" => "remote",
			),
			"deleteItem" => array(
				"description" => "delete a file or folder",
				"arguments" => array("path" => array("type" => "string","required" => true), "item_name" => array("type" => "string","required" => true)),
				"access" => "remote",
			),
			"createFolder" => array(
				"description" => "create a new folder",
				"arguments" => array("path" => array("type" => "string","required" => true), "item_name" => array("type" => "string","required" => true)),
				"access" => "remote",
			)
		);
	}	
	// ***********
	function getFtpContent($path)
	{
		$_array=$this->listFolderContent($this->serverRootPath.$this->silex_server_ini["MEDIA_FOLDER"].$path);
		return $_array;
	}
	function listFolderContent($folder)
	{
		$resArray=array();
		if (!$this->isInFolder($folder,"./")){
			// error : no right to access this folder
			return $resArray;
		}
		$tmpFolder = opendir($folder);
		$tmp=0;
		while ($tmpFile = readdir($tmpFolder)) 
		{
			$resArray[$tmp][$this->itemNameField]=$tmpFile;
			$resArray[$tmp][$this->itemModifDateField]=date ("F d Y H:i:s.", filemtime($folder.$tmpFile));
			//if ($tmpFile != "." && $tmpFile != "..")
			if (is_file($folder.$tmpFile))
			{
				$resArray[$tmp][$this->itemSizeField]=filesize($folder.$tmpFile);
				$resArray[$tmp][$this->itemTypeField]="file";
			}
			else
			{
				$resArray[$tmp][$this->itemSizeField]="";
				$resArray[$tmp][$this->itemTypeField]="folder";
			}
			$tmp++;
		}
		closedir($tmpFolder);
		return $resArray;
	}
	/* isInFolder
	 * @param filepath			file about to be accessed
	 * @param folderName			folder name 
	 * @returns true if filepath designate a path which is in the folder corresponding to folderName
	 */
	function isInFolder($filepath,$folderName){
		//return true;
		return (strpos(realpath($filepath),realpath($this->serverRootPath.$folderName."/"))==0);
	}
	// ***********
	function renameItem($path,$initial_name,$final_name)
	{
		if (!$this->isInFolder($path,"./")){
			// error : no right to access this folder
			return null;
		}
		//if (is_file($this->serverRootPath.$this->silex_server_ini["MEDIA_FOLDER"].$initial_name)==TRUE)
			rename ( $this->serverRootPath.$this->silex_server_ini["MEDIA_FOLDER"].$path.$initial_name, $this->serverRootPath.$this->silex_server_ini["MEDIA_FOLDER"].$path.$final_name);
		return $this->getFtpContent($path);
	}
	// ***********
	function deleteItem($path,$item_name)
	{
		if (!$this->isInFolder($path,"./")){
			// error : no right to access this folder
			return null;
		}
		if (is_file($this->serverRootPath.$this->silex_server_ini["MEDIA_FOLDER"].$path.$item_name))
			@$res=unlink($this->serverRootPath.$this->silex_server_ini["MEDIA_FOLDER"].$path.$item_name);
		else
			@$res=rmdir($this->serverRootPath.$this->silex_server_ini["MEDIA_FOLDER"].$path.$item_name);

		return $this->getFtpContent($path);
	}
	function createFolder($path,$item_name)
	{
		if (!$this->isInFolder($path,"./")){
			// error : no right to access this folder
			return null;
		}
		mkdir($this->serverRootPath.$this->silex_server_ini["MEDIA_FOLDER"].$path.$item_name);
		return $this->getFtpContent($path);
	}
}
?>