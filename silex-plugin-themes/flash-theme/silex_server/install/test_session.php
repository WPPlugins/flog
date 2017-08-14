<?php
	require_once("test_base.php");
	class test_session extends test_base{

		function test_session(){
			parent::test_base();
			$this->title = "test sessions enabled";
		}
		
		function runTest(){
			$this->result = session_start(); //note on free this returns true even if the session start fails!

		}
		
		function getHelp(){
			include("test_session_help.php");
		}
	}
?>