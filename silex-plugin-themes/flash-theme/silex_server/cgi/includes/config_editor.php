<?php
	//class to read and write config files
	
	set_include_path(get_include_path() . PATH_SEPARATOR . "../library/");
	require_once("consts.php");
	require_once("logger.php");
	require_once("file_system_tools.php");
	
	class config_editor{
		var $logger=null;
		var $fst;		
		
		function config_editor(){
			$this->logger = new logger("config_editor");
			$this->fst = new file_system_tools();
		}

	/* sanitize
	 * 
	 */
	
		/**
		* filePath : example conf/silex.ini. will be concatenated with root
		* fileFormat: see library/Config.php. example: phpconstants , flashvars
		* dataToMerge: an array with key, values
		*/
		function updateConfigFile($filePath, $fileFormat, $dataToMerge){
			if($fileFormat == "inifile"){
				//only use inicommented. no use for 2 ini file parsers...
				$fileFormat = "inicommented";
			}
			$this->logger->debug("updateConfigFile");
			$filePathRelativeToRoot = ROOTPATH . $filePath;
			$sanitizedFilePath = $this->fst->sanitize($filePathRelativeToRoot);
			//echo "sanitizedFilePath" . $sanitizedFilePath;
			$this->logger->debug("sanitizedFilePath" . $sanitizedFilePath);
			$this->logger->debug("dataToMerge" . print_r($dataToMerge, true));
			if ($this->fst->checkRights($sanitizedFilePath, file_system_tools::ADMIN_ROLE, file_system_tools::WRITE_ACTION)){
				$conf = new silex_config;
				$confContainer = $conf->parseConfig($sanitizedFilePath, $fileFormat);        
				//note: not sure what happens when file doesn't exist yet. 
				print_r($confContainer->toArray());
				
				foreach ($dataToMerge as $key => $value) {
					//echo $key . $value;
					$confContainer->setDirective($key, $value);
				}
				//echo '<br/>';
				//print_r($confContainer->toArray());
				$conf->writeConfig($sanitizedFilePath, $fileFormat);
				return true;
			}else{
				if ($this->logger) $this->logger->emerg(" updateConfigFile no rights to write $filePath");
				return false;
			}
		}
		
		/**
		* filePath : example conf/silex.ini. will be concatenated with root
		* fileFormat: see library/Config.php. example: phpconstants , flashvars
		* returns array
		*/
		function readConfigFile($filePath, $fileFormat){
			require_once(ROOTPATH . "/cgi/amf-core/util/Authenticate.php");
			if($fileFormat == "inifile"){
				//only use inicommented. no use for 2 ini file parsers...
				$fileFormat = "inicommented";
			}
			
			$this->logger->debug("filepath" . $filePath);
			$filePathRelativeToRoot = ROOTPATH . $filePath;
			$sanitizedFilePath = $this->fst->sanitize($filePathRelativeToRoot);
			$this->logger->debug("sanitizedFilePath" . $sanitizedFilePath);
			$isAllowed = false;
			$auth = new Authenticate();
			$isAdmin = $auth->isUserInRole(AUTH_ROLE_USER);
			if ($this->logger) $this->logger->debug("readConfigFile. user is admin : " . $isAdmin . "roles : " . $_SESSION['amfphp_roles'] . ", filePath : " . $filePath . ", fileformat : " . $fileFormat);
			//echo("isAdmin" . $isAdmin);
			if($isAdmin){
				$isAllowed = $this->fst->checkRights($sanitizedFilePath, file_system_tools::ADMIN_ROLE, file_system_tools::READ_ACTION);
			}else{
				$isAllowed = $this->fst->checkRights($sanitizedFilePath, file_system_tools::USER_ROLE, file_system_tools::READ_ACTION);
			}
			
			if ($isAllowed){
				$conf = new silex_config;
				$confContainer = $conf->parseConfig($sanitizedFilePath , $fileFormat);        
				$res = $confContainer->toArray();
				$res = $res["root"];            
				if ($this->logger) $this->logger->debug(print_r($res,true));
				return $res;
			}else{
				if ($this->logger) $this->logger->emerg(" readConfigFile no rights to read $sanitizedFilePath");
				
			}
			return null;
			
		}
		
		/*
		merge different configs into 1 flash vars like file
		*/
		function mergeConfFilesIntoFlashvars($filesList){
			if ($this->logger) $this->logger->debug("mergeConfFilesIntoFlashvars(" . print_r(func_get_args(), true));

			$res=array();

			// read files
			if ($filesList){
				foreach ($filesList as $value) {
					if ($value!=""){
						$file_path=ROOTPATH . $value;
						if ($this->fst->checkRights($this->fst->sanitize($file_path),file_system_tools::USER_ROLE,file_system_tools::READ_ACTION) && is_file($file_path)){
							$_array = @file($file_path);
							// remove bom
							if(substr($_array[0], 0,3) == pack("CCC",0xef,0xbb,0xbf)) {
								$_array[0]=substr($_array[0], 3);
							}
							$res[]=$_array;
						}
						else{
							if ($this->logger) $this->logger->emerg("mergeConfFilesIntoFlashvars $value not allowed to read this file");
						}
					}
				}
			}
			return $res;
		}
		
		
	}
