<?php
	require_once("rootdir.php");
	require_once("server_config.php");
	require_once("logger.php");
	require_once("file_system_tools.php");
	//class for retrieving info about the server's available content: languages, plugins, sites etc.
	class server_content{
		var $logger = null;
		var $server_config = null;
		
		
		function server_content(){
			$this->logger = new logger("server_content");
			$this->server_config = new server_config();
		
		}
		/**
		 * used by the manager to list languages and by index.php
		 */
		function getLanguagesList()
		{
			if ($this->logger) $this->logger->debug("getLanguagesList() ");
			$res = "";
			$_array=$this->listLanguageFolderContent();
			foreach( $_array as  $file)
			{
				if($res!="")
					$res .= ",";
				$res .= $file[file_system_tools::itemNameNoExtField];
			}
			return $res;
		}
		function listLanguageFolderContent()
		{
			$fst = new file_system_tools();
			if ($this->logger) $this->logger->debug("listLanguageFolderContent() ");
			$_array=$fst->listFolderContent(ROOTPATH . $this->server_config->silex_server_ini["LANG_FOLDER"],false);
			return $_array;
		}
		function listWebsiteFolderContent($id_site)
		{
			$fst = new file_system_tools();
			if ($this->logger) $this->logger->debug("listWebsiteFolderContent($id_site) ");
			$_array=$fst->listFolderContent(ROOTPATH . $this->server_config->silex_server_ini["CONTENT_FOLDER"].$id_site."/");
			return $_array;
		}
		function listToolsFolderContent($path)
		{
			$fst = new file_system_tools();
			if ($this->logger) $this->logger->debug("listToolsFolderContent($path) ");
			$_array=$fst->listFolderContent(ROOTPATH . $this->server_config->silex_server_ini["TOOLS_FOLDER"].$path);
			return $_array;
		}
		function listFtpFolderContent($path)
		{
			$fst = new file_system_tools();
			if ($this->logger) $this->logger->debug("listFtpFolderContent($path) ");
			$_array=$fst->listFolderContent(ROOTPATH . $this->server_config->silex_server_ini["MEDIA_FOLDER"].$path);
			return $_array;
		}
		
	}
	
?>
