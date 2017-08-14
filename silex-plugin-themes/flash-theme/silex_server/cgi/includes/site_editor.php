<?php
	//class to edit site data
	
	require_once("logger.php");
	require_once("server_config.php");
	require_once("file_system_tools.php");
	require_once("silex_search.php");
	
	define("ACTION_DUPLICATE", "duplicate");
	define("ACTION_RENAME", "rename");
	
	class site_editor{
		var $logger = null;
		var $fst = null;		
		var $server_config = null;
		const DEFAULT_WEBSITE_CONF_FILE = "default_website_conf.txt";
		
		function site_editor(){
			$this->fst = new file_system_tools();
			$this->logger = new logger("site_editor");
			$this->server_config = new server_config();
		}

		// ******************************************************
		// find the desired records
		function getSectionSeoData($id_site,$deeplink,$urlBase=null){
			if ($this->logger) $this->logger->debug("getSectionSeoData($id_site,$deeplink)");
			$path=ROOTPATH . $this->server_config->silex_server_ini["CONTENT_FOLDER"].$id_site;

			// check rights
			if ($this->fst->checkRights($this->fst->sanitize($path),file_system_tools::USER_ROLE,file_system_tools::READ_ACTION)){
				// new index
				$silex_search_obj=new silex_search();
				
				// keep only letters since zend would replace it by spaces
				$exactDeeplink=preg_replace('~[^-a-z]+~', '', $deeplink);

				// build the query - e.g. exactDeeplink:("start/en")
				$query=silex_search::seoExactDeeplinkPlayerTag.":(\"".$exactDeeplink."\")";
				
				// replace the "/" by "?" since "/" is considered as " "
				$query=str_replace("/","?",$query);
				
				// find the record corresponding to this deeplink
				$hits=$silex_search_obj->find($path."/",$query);
				
				return $silex_search_obj->buildSeoDataObject($hits,$deeplink,$urlBase);
			}
			else{
				if ($this->logger) $this->logger->emerg("getSectionSeoData no rights to read $id_site - $deeplink");
				return null;
			}
		}
			
		// **
		// write xml data into a file
		function writeSectionData($xmlData, $xmlFileName,$sectionName, $id_site, $seoObject, $domObject=null)
		{
			if ($this->logger) $this->logger->debug("writeSectionData(xmlData, $xmlFileName,$sectionName, $id_site,$seoObject)");
			
			$path = ROOTPATH . $this->server_config->silex_server_ini["CONTENT_FOLDER"];

			// check rights
			if ($this->fst->checkRights($this->fst->sanitize($path),file_system_tools::ADMIN_ROLE,file_system_tools::WRITE_ACTION)){
				if ($this->logger) $this->logger->debug("writeSectionData rights OK");
				//  open file
				$fileHandle=fopen($path."/".$xmlFileName,"w");
				if (!$fileHandle){
					if ($this->logger) $this->logger->debug("writeSectionData error opening file ".$fileHandle);
					return "error opening file ".$fileHandle;
				}
				if ($this->logger) $this->logger->debug("writeSectionData open ok (".$path."/".$xmlFileName.")");

				// add UTF-8 header
				$xmlData="\xEF\xBB\xBF".$xmlData; 

				// write data
				if (!fputs ($fileHandle,$xmlData)){
					if ($this->logger) $this->logger->emerg("writeSectionData error writing to file ".$fileHandle);
					return "error writing to file ".$fileHandle;
				}
				if ($this->logger) $this->logger->debug("writeSectionData close ok");

				// close
				fclose($fileHandle);

				// write data AMF FORMAT
				if (isset($domObject))
				{
					include_once("../amf-core/io/AMFSerializer.php");
					include_once("../amf-core/util/AMFHeader.php");
					include_once("../amf-core/util/AMFBody.php");
					include_once("../amf-core/util/AMFObject.php");
					$amfSerializer = new AMFSerializer();
					$amfObject = new AMFObject($domObject);
					//$amfObject->outputStream = $domObject;
					$amfBody = new AMFBody("","/1","onResult");
					$amfBody->setResults($domObject);
					$amfObject->addBody($amfBody);
					$outHeader = new AMFHeader("AppendToGatewayUrl", false, "?" . ini_get('session.name') . "=" . $session_id);
					$amfObject->addOutgoingHeader($outHeader);
					$amfData = $amfSerializer->serialize($amfObject);
					
					// retrieve file extension
					$amfExt = '.amf';
					if (isset($this->server_config->silex_client_ini["AMF_FILE_EXTENSION"]))
						$amfExt = $this->server_config->silex_client_ini["AMF_FILE_EXTENSION"];
					// build file name
					$fileHandle=fopen($path."/".$id_site."/".$sectionName.$amfExt,"w");
					//  open file
					if (!$fileHandle){
						if ($this->logger) $this->logger->debug("writeSectionData error opening file ".$fileHandle);
						return "error opening file ".$fileHandle;
					}
					if ($this->logger) $this->logger->debug("writeSectionData open ok (".$path."/".$id_site."/".$sectionName.'.amf'.")");

					// write data
					if (!fputs ($fileHandle,$amfData)){
						if ($this->logger) $this->logger->emerg("writeSectionData error writing to file ".$fileHandle);
						return "error writing to file ".$fileHandle;
					}
					if ($this->logger) $this->logger->debug("writeSectionData close ok");

					// close
					fclose($fileHandle);
				}
				
				// create search object
				$silex_search_obj=new silex_search();

				return $silex_search_obj->storeSeoData($path."/".$id_site."/",$sectionName,$seoObject);
			}
			else{
				if ($this->logger) $this->logger->emerg("writeSectionData no rights to write data : $xmlFileName, $sectionName, $id_site, $seoObject");
				return "no rights to write data : $xmlFileName, $sectionName, $id_site, $seoObject";
			}		
		}
		
		/**private, puts code in common for rename and duplicate section
		* rename section
		* call renameSection("start", "cv") renames all files that fit the pattern start.* to cv.*
		* does nothing if the section or site doesn't exist
		* should be future proof for adding new kinds of file to section
		*/
		
		function operateSection($siteName, $oldSectionName, $newSectionName, $action){
			$this->logger->debug("operateSection($siteName, $oldSectionName, $newSectionName)");
			$siteFolderPath = ROOTPATH . $this->server_config->silex_server_ini["CONTENT_FOLDER"] . "/" . $siteName . "/";
			$filesToRename = Array();
			$startString = $oldSectionName . ".";
			if ($handle = opendir($siteFolderPath)) {
				while (false !== ($file = readdir($handle))) {
					if(strpos($file, $startString) === 0){
						array_push($filesToRename, $file);
					}
				}
				closedir($handle);
			}			
			foreach($filesToRename as $fileName){
				$sanitizedFilePath = $this->fst->sanitize($siteFolderPath . $fileName);
				if ($this->fst->checkRights($sanitizedFilePath, file_system_tools::ADMIN_ROLE, file_system_tools::WRITE_ACTION)){
					//note if duplicate should only need read, but too complicated for now. @Todo A.S.K.
					$newFileName = str_replace($oldSectionName, $newSectionName, $fileName);
					if(file_exists($siteFolderPath . $newFileName)){
						$this->logger->err("$siteFolderPath/$newFileName already exists");
						throw new Exception("$siteFolderPath/$newFileName already exists");
					}else{
						if($action == ACTION_RENAME){
							rename($siteFolderPath . $fileName, $siteFolderPath . $newFileName);
						}else if($action == ACTION_DUPLICATE){
							copy($siteFolderPath . $fileName, $siteFolderPath . $newFileName);
						}
						$this->logger->debug($action . " $siteName/$fileName to $siteName/$newFileName");
					}
				}else{
					$this->logger->err("modifying $siteFolderPath/$fileName not allowed");
				}
			}
		}
		
		function renameSection($siteName, $oldSectionName, $newSectionName){
			$this->operateSection($siteName, $oldSectionName, $newSectionName, ACTION_RENAME);
		}
		
		function duplicateSection($siteName, $oldSectionName, $newSectionName){
			$this->operateSection($siteName, $oldSectionName, $newSectionName, ACTION_DUPLICATE);
		}
		
		function createWebsiteIndex($id_site){
			$relPath = $this->server_config->silex_server_ini["CONTENT_FOLDER"].$id_site;
			$fullPath = ROOTPATH . $relPath;
			// check rights
			if ($this->fst->checkRights($this->fst->sanitize($fullPath),file_system_tools::ADMIN_ROLE,file_system_tools::WRITE_ACTION)){
				if ($this->logger) $this->logger->debug("createWebsiteIndex($id_site)");
				require_once '../includes/silex_search.php';
				
				// open website conf file
				$websiteConfig=$this->getWebsiteConfig($id_site);
				
				// build the entry point (start xml file)
				$firstSectionName=$websiteConfig["CONFIG_START_SECTION"];
				if ($this->logger) $this->logger->debug("createWebsiteIndex - start with section: $firstSectionName"); 

				// **
				// create search object
				$silex_search_obj=new silex_search($this->server_config->sepCharForDeeplinks);
					

				// create indexes
				return $silex_search_obj->createIndex($relPath."/",$firstSectionName);
			}
			else{
				if ($this->logger) $this->logger->emerg("createWebsiteIndex no rights to create website index for $id_site");
				return 0;
			}		
		}	
		//todo : get rid of this function
		function parse_client_side_ini_file($filePath){
			// check rights
			$res = null;
			if ($this->fst->checkRights($this->fst->sanitize($filePath),file_system_tools::USER_ROLE,file_system_tools::READ_ACTION)){
				$conf = new silex_config;
				$res = $conf->parseConfig($filePath, 'flashvars');        
				$res = $res->toArray();
				$res = $res["root"];
			}
			else{
				if ($this->logger) $this->logger->emerg("parse_client_side_ini_file no rights to read $filePath");
			}		
			return $res;
		}
					
		function getWebsiteConfig($id_site,$mergeWithServerConfig=false){
			if ($this->logger) $this->logger->debug("getWebsiteConfig, id_site : $id_site"); 

			$filePath=ROOTPATH . $this->server_config->silex_server_ini["CONTENT_FOLDER"].$id_site."/".$this->server_config->silex_server_ini["WEBSITE_CONF_FILE"];
			if ($this->logger) $this->logger->debug("getWebsiteConfig - file path = $filePath"); 

			$res=$this->parse_client_side_ini_file($filePath);
			if ($this->logger) $this->logger->debug("getWebsiteConfig returns ".print_r($res,true)); 
			
			if ($mergeWithServerConfig==true)
			{
				try
				{
					$res = array_merge($res,$this->silex_client_ini,$this->server_config->silex_server_ini);
				}
				catch (Exception $e) 
				{
					//var_dump($e->getTrace());
				}
			}
			
			return $res;
		}		
		
		/** 
		 * delete a website
		 */
		function deleteWebsite($id_site){
			$id_site = str_replace(array('/', '\\'), "", htmlentities(strip_tags($id_site)));
			$path=ROOTPATH . $this->server_config->silex_server_ini["CONTENT_FOLDER"].$id_site;

			if ($this->logger) $this->logger->debug("writeWebsiteConfig delete website (data empty) ".$path." renamed -> ".ROOTPATH . $this->server_config->silex_server_ini["TRASH_FOLDER"].$id_site."_deleted_".date('Y-m-d_H-i-s'));

			return rename($path,ROOTPATH . $this->server_config->silex_server_ini["TRASH_FOLDER"].$id_site."_deleted_".date('Y-m-d_H-i-s'));
		}
		/** 
		 * create a website
		 */
		function createWebsite($id_site)
		{
			// sanitize
			$id_site = str_replace(array('/', '\\'), "", htmlentities(strip_tags($id_site)));
			$path = ROOTPATH . $this->server_config->silex_server_ini["CONTENT_FOLDER"].$id_site;
			if ($this->logger) $this->logger->debug("createWebsite ".$id_site);
			if (is_dir($path))
				return false;
			else
			{
				// **
				// website creation
				if(!mkdir($path, 0755)){
					throw new Exception("couldn't create folder at $path");
				}
				if ($this->logger) $this->logger->info("createWebsite mkdir(".$path.")");
				// check rights
				if ($this->fst->checkRights($this->fst->sanitize($path),file_system_tools::ADMIN_ROLE,file_system_tools::WRITE_ACTION))
				{
					$source = ROOTPATH . $this->server_config->silex_server_ini["CONF_FOLDER"].self::DEFAULT_WEBSITE_CONF_FILE;
					$dest = $path . "/" . $this->server_config->silex_server_ini["WEBSITE_CONF_FILE"];
					return copy($source, $dest);
				}
				else
				{
					rmdir($path);
					return false;
				}
			}
		}
		/** 
		 * rename a website
		 */
		function renameWebsite($id_site,$newId)
		{
			// sanitize
			$id_site = str_replace(array('/', '\\'), "", htmlentities(strip_tags($id_site)));
			$newId = str_replace(array('/', '\\'), "", htmlentities(strip_tags($newId)));

			$path=ROOTPATH . $this->server_config->silex_server_ini["CONTENT_FOLDER"].$id_site;
			$newPath=ROOTPATH . $this->server_config->silex_server_ini["CONTENT_FOLDER"].$newId;

			if (!is_dir($newPath))
			{
				if ($this->logger) $this->logger->debug("renameWebsite rename website ".$path." renamed -> ".$newPath);

				return rename($path,$newPath);
			}
			return FALSE;
		}
		/**
		* deprecated. this is still used by the website config tool, and createWebsite so leave it for now.
		*/
		function writeWebsiteConfig($websiteInfo,$id_site){
			if ($this->logger) $this->logger->debug("writeWebsiteConfig($websiteInfo,$id_site) ");
			if (!$id_site || $id_site=="")
				return false;
				
			$path=ROOTPATH . $this->server_config->silex_server_ini["CONTENT_FOLDER"].$id_site;

			// **
			// website creation
			// check existance
			$initial_path=$path;
			if (!$this->fst->sanitize($path)){
				if ($this->logger) $this->logger->info("writeWebsiteConfig mkdir(".$initial_path.")");
				mkdir($initial_path);
				$path=$this->fst->sanitize($initial_path);
			}
			
			// check rights
			if ($this->fst->checkRights($path,file_system_tools::ADMIN_ROLE,file_system_tools::WRITE_ACTION)){

				// **
				// write data into the file

				// build data string
				$data="";
				foreach ($websiteInfo as $key => $value) {
					$data.=$key."=".$value."&\n";
				}

				// delete website or write data?
				if ($data==""){
					if ($this->logger) $this->logger->debug("writeWebsiteConfig delete website (data empty) ".$path." renamed -> ".ROOTPATH . $this->server_config->silex_server_ini["TRASH_FOLDER"].$id_site."_deleted_".date('Y-m-d_H-i-s'));
					// not empty! - return rmdir($this->server_config->silex_server_ini["CONTENT_FOLDER"].$id_site);
					return rename($path,ROOTPATH . $this->server_config->silex_server_ini["TRASH_FOLDER"].$id_site."_deleted_".date('Y-m-d_H-i-s'));
				}
				else{
					// open
					$fileName=$path."/conf.txt";
					$fileHandle=fopen($fileName,"w");
					if (!$fileHandle){
						if ($this->logger) $this->logger->debug("writeWebsiteConfig error opening file : $fileName");
						return false;
					}
					// add UTF-8 header
					$data="\xEF\xBB\xBF".$data; 
					// write
					$isOk=fputs ($fileHandle,$data);
					if (!$isOk){
						if ($this->logger) $this->logger->debug("writeWebsiteConfig error writing data : $data in $fileName");
						return false;
					}
					// close
					fclose($fileHandle);
				}
				// return no-error state
				return true;
			}
			else{
				if ($this->logger) $this->logger->debug("writeWebsiteConfig no rights to write data : $data in $fileName");
			}
			return false;
		}
		function duplicateWebsite($id_site,$newName){
			if ($this->logger) $this->logger->debug("duplicateWebsite($id_site,$newName) ");

			$path=ROOTPATH . $this->server_config->silex_server_ini["CONTENT_FOLDER"].$id_site;
			$newPath=ROOTPATH . $this->server_config->silex_server_ini["CONTENT_FOLDER"].$newName;

			if (is_dir(ROOTPATH . $this->server_config->silex_server_ini["CONTENT_FOLDER"].$newName)==TRUE){
				return "This website already exist";
			}
			if (!$newName || $newName==""){
				return "No name specified";
			}
			mkdir($newPath);
			$this->fst->sanitize($newPath);

			// check rights
			if ($this->fst->checkRights($this->fst->sanitize($path),file_system_tools::ADMIN_ROLE,file_system_tools::WRITE_ACTION)){
				// do the copy
				if ($this->doDuplicateWebsite($path."/",$newPath."/")==TRUE)
					return "";
				else{
					// delete partially copyed folder
					if (is_dir(ROOTPATH . $this->server_config->silex_server_ini["CONTENT_FOLDER"].$newName))
						rename(ROOTPATH . $this->server_config->silex_server_ini["CONTENT_FOLDER"].$newName,ROOTPATH . $this->server_config->silex_server_ini["TRASH_FOLDER"].$id_site."_".$newName."_rename-error_".date('Y-m-d_H-i-s'));
					if ($this->logger) $this->logger->emerg("duplicateWebsite error : Unknown error - rename(".ROOTPATH . $this->server_config->silex_server_ini["CONTENT_FOLDER"].$newName." becomes -> ".ROOTPATH . $this->server_config->silex_server_ini["TRASH_FOLDER"].$id_site."_".$newName."_rename-error_".date('Y-m-d_H-i-s').")");
					return "Unknown error";
				}
			}
			else{
				if ($this->logger) $this->logger->emerg("duplicateWebsite no rights to duplicate website : $id_site to $newName");
				return "no rights to duplicate website : $id_site to $newName";
			}		
		}
		// do the copy
		function doDuplicateWebsite($folder,$newFolder){
			if ($this->logger) $this->logger->debug("doDuplicateWebsite($folder,$newFolder)");

			// list folder and copy
			$tmpFolder = opendir($folder);
			while ($tmpFile = readdir($tmpFolder)) {
				if ($tmpFile != "." && $tmpFile != ".."){
					if (is_file($folder.$tmpFile)){
						if (!copy($folder.$tmpFile,$newFolder.$tmpFile)){
							if ($this->logger) $this->logger->emerg("doDuplicateWebsite error while copying file "+$folder.$tmpFile." to ".$newFolder.$tmpFile);
							return FALSE;
						}
					}
				}
			}
			return TRUE;
		}
		
	}
?>