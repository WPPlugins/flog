<?php
	require_once("../rootdir.php");
	require_once("localisation.php");
	class test_base{
		//private
		var $result = false;
		var $title = "test base, should be set in derived test class";
		var $loc = null;
		
		function test_base(){
			$this->loc = new localisation();
		}
		
		function runTest(){
			//set test result at the end of the test.
			//$this->result = true;
		}
		
		function getResult(){
			return $this->result;
		}
		
		function getTitle(){
			return $this->title;
		}
		
		function getHelp(){
			//implement in derived class. probably an included separate file. Naming convention is <testclassname>_help.php
		}
		
		//most tests are fatal for an installation, but a couple are not. override in derived class
		function isFatal(){
			return true;
		}
	}
?>