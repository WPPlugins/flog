<?php
	require_once("test_base.php");
	class test_file_system_rights extends test_base{

		function test_file_system_rights(){
			parent::test_base();
			$this->title = "test file system rights";
		}
		
		function checkRights($filename, $giveUserFeedback){
			//echo "<H2>".$filename." check</H2><br>";
			if(is_executable($filename) && is_writable($filename)){
				if($giveUserFeedback){
					echo "<b>" . $filename . "</b> " . $this->loc->getLocalised("TEST_RIGHTS_HAS_RIGHTS");
				}
			}
			else{
				if($giveUserFeedback){
					echo "<b>" . $filename . "</b> " . $this->loc->getLocalised("TEST_RIGHTS_HAS_NO_RIGHTS");
				}
				if (chmod ($filename, 0755)){
					if($giveUserFeedback){
						echo "<b>" . $filename . "</b> " . $this->loc->getLocalised("TEST_RIGHTS_CHMOD_OK") . "<br>";
					}
				}
				else{
					if($giveUserFeedback){
						echo "<b>" . $filename . "</b>" . $this->loc->getLocalised("TEST_RIGHTS_CHMOD_NOK") . "<br>";
					}
					return false;
				}
			}
			return true;
		}
		
		function runTest(){
			$giveUserFeedback = false; //true;
			$res1 = $this->checkRights("../media", $giveUserFeedback);
			$res2 = $this->checkRights("../contents", $giveUserFeedback);
			$res3 = $this->checkRights("../cgi/scripts/upload.cgi", $giveUserFeedback);
			$res4 = $this->checkRights("../logs/", $giveUserFeedback);		
			$this->result = $res1 && $res2 && $res3 && $res4;

		}
		
		//test isn't fatal. User can run silex, but not edit sites
		function isFatal(){
			return false;
		}
		
		function getHelp(){
			include("test_file_system_rights_help.php");
		}

	}
?>