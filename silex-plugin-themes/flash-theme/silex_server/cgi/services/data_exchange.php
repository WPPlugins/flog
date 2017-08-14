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
set_include_path(get_include_path() . PATH_SEPARATOR . "../../");
set_include_path(get_include_path() . PATH_SEPARATOR . "../library/");
set_include_path(get_include_path() . PATH_SEPARATOR . "../includes/");
set_include_path(get_include_path() . PATH_SEPARATOR . "./cgi/includes/");


require_once("cgi/includes/domxml.php");
/** Zend_Debug */
require_once 'Zend/Debug.php';
//Config
require_once("silex_config.php");
require_once("server_config.php");
require_once("password_manager.php");
require_once("file_system_tools.php");

require_once("rootdir.php");
require_once("logger.php");
require_once("site_editor.php");
require_once("server_content.php");
class data_exchange
{
	
	var $logger = null;
	// ***********
	// constructor
	function data_exchange()
	{
		$this->logger = new logger("data_exchange");
		// **
		require_once("data_exchange.methodTable.php");
	}


	// ***********
	
	/**
	 * used by the manager to list languages and by index.php
	 */
	function getLanguagesList()
	{
		$s = new server_content();
		return $s->getLanguagesList();
	}
	function listLanguageFolderContent()
	{
		$s = new server_content();
		return $s->listLanguageFolderContent();
	}
	function listWebsiteFolderContent($id_site){
		$s = new server_content();
		return $s->listWebsiteFolderContent($id_site);
	}
	function listToolsFolderContent($path){
		$s = new server_content();
		return $s->listToolsFolderContent($path);
	}
	function listFtpFolderContent($path){
		$s = new server_content();
		return $s->listFtpFolderContent($path);
	}
    /*
    merge different configs into 1 flash vars like file
    */
	function getDynData($wesiteInfo,$filesList){
		if ($this->logger) $this->logger->debug("getDynData(" . print_r(func_get_args(), true));

		$configEditor = new config_editor();
		return $configEditor->mergeConfFilesIntoFlashvars($filesList);
	}
    /** 
	 * delete a website
     */
    function deleteWebsite($id_site){
		$s = new site_editor();
		return $s->deleteWebsite($id_site);
    }
    /** 
	 * create a website
     */
    function createWebsite($id_site){
		$s = new site_editor();
		return $s->createWebsite($id_site);
    }
    /** 
	 * rename a website
     */
    function renameWebsite($id_site,$newId){
		$s = new site_editor();
		return $s->renameWebsite($id_site,$newId);
    }
    /**
    * deprecated. this is still used by the website config tool, and createWebsite so leave it for now.
    */
	function writeWebsiteConfig($websiteInfo,$id_site){
		$s = new site_editor();
		return $s->writeWebsiteConfig($websiteInfo, $id_site);
	}
	function duplicateWebsite($id_site,$newName){
		$s = new site_editor();
		return $s->duplicateWebsite($id_site,$newName);
	}

	function createWebsiteIndex($id_site){
		$s = new site_editor();
		return $s->createWebsiteIndex($id_site);
	}	
	
	function getWebsiteConfig($id_site,$mergeWithServerConfig=false){
		$s = new site_editor();
		return $s->getWebsiteConfig($id_site,$mergeWithServerConfig);
	}
	
	/**
	 * interface for calc_dir_size
	 * returns the size of a folder in a readable form
	 */
	function getFolderSize($folder){
		$fst = new file_system_tools();
		return $fst->getFolderSize($folder);
	}
	
    /**
    * filePath : example conf/silex.ini. will be concatenated with root
    * fileFormat: see library/Config.php. example: phpconstants , flashvars
    * dataToMerge: an array with key, values
    */
    function updateConfigFile($filePath, $fileFormat, $dataToMerge){
		require_once("config_editor.php");
		$c = new config_editor();
		return $c->updateConfigFile($filePath, $fileFormat, $dataToMerge);
    }
	
    /**
    * filePath : example conf/silex.ini. will be concatenated with root
    * fileFormat: see library/Config.php. example: phpconstants , flashvars
    * returns array
    */
    function readConfigFile($filePath, $fileFormat){
		require_once("config_editor.php");
		$c = new config_editor();
		return $c->readConfigFile($filePath, $fileFormat);
	}
	
	// **
	// write xml data into a file
	function writeSectionData($xmlData, $xmlFileName,$sectionName, $id_site, $seoObject, $domObject){
		$s = new site_editor();
		return $s->writeSectionData($xmlData, $xmlFileName,$sectionName, $id_site, $seoObject, $domObject);
	}
	
	function renameSection($siteName, $oldSectionName, $newSectionName){	
		$s = new site_editor();
		$s->renameSection($siteName, $oldSectionName, $newSectionName);
	}

	function duplicateSection($siteName, $oldSectionName, $newSectionName){	
		$s = new site_editor();
		$s->duplicateSection($siteName, $oldSectionName, $newSectionName);
	}

	function doLogin(){
		if ($this->logger) $this->logger->debug("doLogin ok");
		return true;
	}
	function doLogout(){
		Authenticate::logout();
		if ($this->logger) $this->logger->debug("doLogout");
		return true;
	}	
	// This function will authenticate the client
	// before return the value of method call
	function _authenticate($user, $pass){
		$p = new password_manager;
		$this->logger->debug(" _authenticate($user, $pass)");
		return $p->authenticate($user, $pass);
	}

	// ******************************************************
	// find the desired records
	function getSectionSeoData($id_site,$deeplink,$urlBase=null){
		$s = new site_editor();
		return $s->getSectionSeoData($id_site,$deeplink,$urlBase);
	}
	
	function setPassword($login, $password){
		$p = new password_manager;
		return $p->setPassword($login, $password);
	}	
	
	function getLogins(){
		$p = new password_manager;
		return $p->getLogins();
	}
	
	function deleteAccount($login){
		$p = new password_manager;
		return $p->deleteAccount($login);
	}
	
	
}
?>