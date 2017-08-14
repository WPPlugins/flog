<?php
	require_once("rootdir.php");
	require_once("silex_config.php");
	require_once("logger.php");
	//loads, parses and holds the config of the silex server
	class server_config{
		var $logger = null;	
	
		var $silex_server_ini = null;
		var $silex_client_ini = null;
		
		var $admin_write_ok;
		var $admin_read_ok;
		var $user_write_ok;
		var $user_read_ok;	
		
		var $sepCharForDeeplinks="."; // read in ini file
	
		// ***********
		// constructor
		function server_config(){
			$this->logger = new logger("server_config");
            $conf = new silex_config;
			// **
			// read ini files
			// silex_server.ini
				$fullPath = ROOTPATH . "conf/silex_server.ini";
				if(!file_exists($fullPath)){
					$this->logger->alert("wanted conf file $fullPath does not exist");
				}else{
					$parsedConfig = $conf->parseConfig( $fullPath, 'inicommented');        
					$parsedConfigAsArray = $parsedConfig->toArray();
					$confRoot = $parsedConfigAsArray["root"];
					if ($confRoot){
						$this->silex_server_ini = $confRoot;
					}
				}
		
							
			// **
			// rights
			$this->admin_write_ok=explode(",", $this->silex_server_ini["admin_write_ok"]);
			$this->admin_read_ok=explode(",", $this->silex_server_ini["admin_read_ok"]);
			$this->user_write_ok=explode(",", $this->silex_server_ini["user_write_ok"]);
			$this->user_read_ok=explode(",", $this->silex_server_ini["user_read_ok"]);
			
			// **
			// read client side ini files
			// silex.ini (client side ini file)
			$this->silex_client_ini = Array();
			$client_ini_files = explode(",", $this->silex_server_ini["SILEX_CLIENT_CONF_FILES_LIST"]);
			
			foreach ($client_ini_files as $ini_file_name){
				$fullPath = ROOTPATH . $ini_file_name;
				if(!file_exists($fullPath)){
					$this->logger->alert("wanted conf file $fullPath does not exist");
				}else{
					$parsedConfig = $conf->parseConfig($fullPath, 'flashvars');        
					$parsedConfigAsArray = $parsedConfig->toArray();
					$confRoot = $parsedConfigAsArray["root"];
					if ($confRoot){
						$this->silex_client_ini = array_merge($this->silex_client_ini,$confRoot);
					}
				}
			}
			
			// init constants from ini file
			if (isset($this->silex_client_ini["sepchar"])){
				$this->sepCharForDeeplinks=$this->silex_client_ini["sepchar"];
			}
		
		}

	
	}
	
?>
