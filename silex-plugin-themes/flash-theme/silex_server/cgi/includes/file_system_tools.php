<?php
	require_once("rootdir.php");
	require_once("server_config.php");
	require_once("logger.php");
	require_once("consts.php");
	class file_system_tools{
		var $logger = null;
	
		// for rights to read or write folders and files
		const WRITE_ACTION="write";
		const READ_ACTION="read";
		const ADMIN_ROLE="admin";
		const USER_ROLE="user";		
	
		// ftp client web service constants
		// folders list xml node names
		const itemTypeField="item type";
		const itemSizeField="item size";
		const itemReadableSizeField="item readable size";
		const itemNameField="item name";
		const itemNameNoExtField="item name no extension";
		const itemModifDateField="item last modification date";
		const itemWidthField="item width";
		const itemHeightField="item height";
		const itemContentField="itemContent";
	
		function file_system_tools(){
			$this->logger = new logger("file_system_tools");
		}
		
		/* sanitize
		* @param filepath . use a full absolute path
		*/
		function sanitize($filepath){
			//if ($this->logger) $this->logger->debug("sanitize : $filepath, 1 : ". strip_tags($filepath) . ", 2: " . htmlentities(strip_tags($filepath)) . ", 3 : ". realpath(htmlentities(strip_tags($filepath))));
			// sanitise input
			$filepath=realpath(htmlentities(strip_tags($filepath)));

			// return sanitized path
			return $filepath;
		}
		/* isInFolder
		 * @param filepath			file about to be accessed
		 * @param folderName			folder name 
		 * @returns true if filepath designate a path which is in the folder corresponding to folderName
		 */
		function isInFolder($filepath,$folderName){
			return (strpos($filepath,realpath(ROOTPATH . $folderName))===0);
		}
		function readableFormatFileSize($size, $round = 0) 
		{
			//Size must be bytes!
			$sizes = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
			for ($i=0; $size > 1024 && $i < count($sizes) - 1; $i++) $size /= 1024;
			return round($size,$round).$sizes[$i];
		} 
		/**
		 * Calculate directory size info.
		 * @param   string  $path   without trailing '/' or '\' (eg. '/users/sampleUser', not '/users/sampleUser/')
		 * @return  array with size, count, and dircount
		 */
		function get_dir_size_info($path)
		{
		  $totalsize = 0; 
		  $totalcount = 0; 
		  $dircount = 0; 
		  if ($handle = opendir ($path)) 
		  { 
			while (false !== ($file = readdir($handle))) 
			{ 
			  $nextpath = $path . '/' . $file; 
			  if ($file != '.' && $file != '..' && !is_link ($nextpath)) 
			  { 
				if (is_dir ($nextpath)) 
				{ 
				  $dircount++; 
				  $result = $this->get_dir_size_info($nextpath); 
				  $totalsize += $result['size']; 
				  $totalcount += $result['count']; 
				  $dircount += $result['dircount']; 
				} 
				elseif (is_file ($nextpath)) 
				{ 
				  $totalsize += filesize ($nextpath); 
				  $totalcount++; 
				} 
			  } 
			} 
		  } 
		  closedir ($handle); 
		  $total['size'] = $totalsize; 
		  $total['count'] = $totalcount; 
		  $total['dircount'] = $dircount; 
		  if ($this->logger) $this->logger->debug("get_dir_size_info for $path : " . print_r($total, true));
		  return $total; 
		}
		/**
		 * interface for get_dir_size_info
		 * returns the size of a folder in a readable form
		 */
		function getFolderSize($folder)
		{
			if ($this->logger) $this->logger->debug("getFolderSize($folder) ");

			require_once(ROOTPATH . "/cgi/amf-core/util/Authenticate.php");
			$path = ROOTPATH . $folder;
			$path = $this->sanitize($path);

			$isAllowed = false;
			$auth = new Authenticate();
			$isAdmin = $auth->isUserInRole(AUTH_ROLE_USER);

			if($isAdmin){
				$isAllowed = $this->checkRights($path, self::ADMIN_ROLE, self::READ_ACTION);
			}else{
				$isAllowed = $this->checkRights($path, self::USER_ROLE, self::READ_ACTION);
			}
			
			if ($this->logger) $this->logger->debug( "path" . $path . ", isAdmin" . $isAdmin . ", isAllowed" . $isAllowed);
			
			if ($isAllowed)
			{
				$sizeObj = $this->get_dir_size_info($path);
				//if ($this->logger) $this->logger->debug("sizeObj : " . print_r($sizeObj, true));
				return $this->readableFormatFileSize($sizeObj['size']);
			}
			return "forbidden";
		}	
		
		function listFolderContent($folder, $isRecursive=true)
		{
			$folderInitial=$folder;
			if ($this->logger) $this->logger->debug("listFolderContent($folder) ");
			if ($this->checkRights($this->sanitize($folder),self::USER_ROLE,self::READ_ACTION)){
				$tmpFolder = opendir($folder);
				$tmp=0;
				$resArray=array();
				while ($tmpFile = readdir($tmpFolder)) 
				{
					if ($tmpFile != "." && $tmpFile != ".."){
						//if ($tmpFile != "." && $tmpFile != "..")
						if (is_file($folder."/".$tmpFile))
						{
							$FileNameTokens = explode('.',$tmpFile);
							$ext = strtolower(array_pop($FileNameTokens));
							
							$resArray[$tmp][self::itemNameField]=$tmpFile;
							$resArray[$tmp][self::itemNameNoExtField]=implode(".", $FileNameTokens);
							$resArray[$tmp][self::itemModifDateField]=date ("Y-m-d\H:i:s", filemtime($folder."/".$tmpFile));
							$resArray[$tmp][self::itemSizeField]=filesize($folder."/".$tmpFile);
							$resArray[$tmp][self::itemReadableSizeField]=$this->readableFormatFileSize(filesize($folder."/".$tmpFile));
							$resArray[$tmp][self::itemTypeField]="file";
							if($ext == 'jpeg' || $ext == 'jpg'){
								$imageSize = getimagesize ($folder."/".$tmpFile);
								$resArray[$tmp][self::itemWidthField] = $imageSize[0];
								$resArray[$tmp][self::itemHeightField] = $imageSize[1];
							}
							$resArray[$tmp]["ext"]=$ext;
						}
						elseif($isRecursive==true && is_dir($folder."/".$tmpFile))
						{
							if(strpos($tmpFile, '.') !== 0){ //don't list folders starting with '.'
								$resArray[$tmp][self::itemNameField]=$tmpFile;
								$resArray[$tmp][self::itemTypeField]="folder";
								$resArray[$tmp][self::itemContentField] = $this->listFolderContent($folderInitial.'/'.$tmpFile.'/');
							}
						}
					}
					$tmp++;
				}
				closedir($tmpFolder);
			}
			else{
				if ($this->logger) $this->logger->emerg("listFolderContent($folder) - not allowed to list this folder ");
			}
			//$this->logger->debug("listFolderContent($folder) - $tmp elements : ".print_r($resArray,true));
			return $resArray;
		}	

		/**
		 * from http://fr.php.net/realpath
		 * Because realpath() does not work on files that do not
		 * exist, I wrote a function that does.
		 * It replaces (consecutive) occurences of / and \\ with
		 * whatever is in DIRECTORY_SEPARATOR, and processes /. and /.. fine.
		 * Paths returned by get_absolute_path() contain no
		 * (back)slash at position 0 (beginning of the string) or
		 * position -1 (ending)
		 */
		function get_absolute_path($path) {
			$path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, htmlentities(strip_tags($path)));
			$parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
			$absolutes = array();
			foreach ($parts as $part) {
				if ('.' == $part) continue;
				if ('..' == $part) {
					array_pop($absolutes);
				} else {
					$absolutes[] = $part;
				}
			}
			return implode(DIRECTORY_SEPARATOR, $absolutes);
		}
		/* checkRights
		 * check if user is allowed to access (read or write) a file  given the user's role
		 * @param $filepath	the path to the file 
		 * @param $usertype	the type of user : self::ADMIN_ROLE or self::USER_ROLE
		 * @param $action	the type of action : self::READ_ACTION or self::WRITE_ACTION
		 * returns false if role is not allowed to do the action
		 */
		function checkRights($filepath,$usertype,$action){
			$serverConfig = new server_config();
			// debug trace
			if ($this->logger) $this->logger->debug("checkRights($filepath,$usertype,$action) ");
			
			switch($action){
				case self::READ_ACTION:
					// if user is an admin
					if ($usertype==self::ADMIN_ROLE){
						// for each path
						foreach ($serverConfig->admin_read_ok as $folderName){
							// if the file is below this path, return true
							if ($this->isInFolder($filepath,$folderName)){
								if ($this->logger) $this->logger->debug("checkRights return true");
								return true;
							}
						}
					}
					// for each path
					foreach ($serverConfig->user_read_ok as $folderName){
						$isInFolder = $this->isInFolder($filepath,$folderName);
						if ($this->logger) $this->logger->debug("checkRights isInFolder($filepath,$folderName) -> " . $isInFolder);
						// if the file is below this path, return true
						if ($isInFolder){
							if ($this->logger) $this->logger->debug("checkRights return true");
							return true;
						}
					}
				break;
				case self::WRITE_ACTION:
					// if user is an admin
					if ($usertype==self::ADMIN_ROLE){
						// for each path
						foreach ($serverConfig->admin_write_ok as $folderName){
							// if the file is below this path, return true
							if ($this->isInFolder($filepath,$folderName)){
								if ($this->logger) $this->logger->debug("checkRights return true");
								return true;
							}
						}
					}
					// for each path
					foreach ($serverConfig->user_write_ok as $folderName){
						// if the file is below this path, return true
						if ($this->isInFolder($filepath,$folderName)){
							if ($this->logger) $this->logger->debug("checkRights return true");
							return true;
						}
					}
				break;
			}
			// the file is not under a convenient path
			if ($this->logger) $this->logger->info("checkRights($filepath,$usertype,$action) returns false - the file is not under a convenient path");
			return false;
		}		
	}
?>