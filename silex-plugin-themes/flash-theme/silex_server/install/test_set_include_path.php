<?php
	require_once("test_base.php");
	class test_set_include_path extends test_base{

		function test_set_include_path(){
			parent::test_base();
			$this->title = "test set_include_path";
		}
		
		function runTest(){
			$normalIncludePath = get_include_path();
			set_include_path($normalIncludePath . PATH_SEPARATOR . "./testingforsilex");
			if(get_include_path() == $normalIncludePath){
				$this->result = false;
			}else{
				$this->result = true;
			}
		}
		
		function getHelp(){
			include("test_set_include_path_help.php");
		}
	}
?>