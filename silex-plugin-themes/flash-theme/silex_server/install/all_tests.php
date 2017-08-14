<?php
	require_once("test_base.php");
	require_once("test_php_version.php");
	require_once("test_file_system_rights.php");
	require_once("test_session.php");
	require_once("test_set_include_path.php");
//	require_once("test_rewrite.php");
	/**
	* instanciate this class and call runTest to run all the tests on the server. 
	* it stops at the first failed test.
	*/
	class all_tests extends test_base{
		
		var $failedTests;
		
		function all_tests(){
			parent::test_base();
			$this->title = "all tests";
		}
		
		function runTest(){
			$tests = array();
			array_push($tests, new test_php_version());
			array_push($tests, new test_file_system_rights());
			array_push($tests, new test_set_include_path());
			array_push($tests, new test_session());
//			array_push($tests, new test_rewrite());
			$this->failedTests = array();
			foreach($tests as $test){
				$test->runTest();
				//echo $test->getTitle() . "<br/>";
				if(!$test->getResult()){
					array_push($this->failedTests, $test);
					if($test->isFatal()){
						$this->result = false;
						return;
					}
				}
			}
			$this->result = true;
		}
		
		function getHelp(){
			$ret = "";
			foreach($this->failedTests as $test){
				$test->getHelp();
				echo "<br/>";
			}
		}
		
	}
?>
