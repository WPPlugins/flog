<?php
	require_once("test_base.php");
	class test_php_version extends test_base{

		function test_php_version(){
			parent::test_base();
			$this->title = "test php version";
		}
		
		function runTest(){
			if (version_compare(PHP_VERSION,'5','>=')){
				$this->result = true;
			}else{
				$this->result = false;
			}
		}
		
		function getHelp(){
			include("test_php_version_help.php");
		}
	}
?>