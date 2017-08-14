<?php
	require_once("../rootdir.php");
	require_once("localisation.php");
	$loc = new localisation();
	
	require_once("all_tests.php");
	$allTests = new all_tests();
	$allTests->runTest();
	$testsOk = $allTests->getResult();
	$serverAlreadyInstalled = false;
	if($testsOk){
		set_include_path(get_include_path() . PATH_SEPARATOR . "../");
		set_include_path(get_include_path() . PATH_SEPARATOR . "../cgi/includes/");
		set_include_path(get_include_path() . PATH_SEPARATOR . "../cgi/library/");
		require_once("password_manager.php");
		$p = new password_manager();
		
		if($p->isAuthenticationFileAvailable()){
			$serverAlreadyInstalled = true;
		}
	
	}
	
	//server is ok, ask for login if no account available, ask for account creation otherwise
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $loc->getLocalised("TITLE")?> &gt; <?php echo $loc->getLocalised("STEP2")?></title>
<style type="text/css">
<!--
body,td,th {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
}
body {
	background-color: #303030;
	margin-top: 0px;
	margin-left: 0px;
	margin-right: 0px;
}
.style2 {
	font-size: 19px;
	color: #FFFFFF;
}
.style5 {color: #FF9900; font-size: 17px; }
.style6 {font-size: 17px}
.style7 {color: #FFFFFF; font-size: 17px; }
-->
</style></head>

<body>
	<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
	  <tr>
		<td height="100" background="images/header_950x100.jpg"><div align="center">
		  <table width="950" border="0" cellspacing="0" cellpadding="0">
			<tr>
			  <td width="550" class="style2"><div align="left"><?php echo $loc->getLocalised("TITLE")?></div></td>
			  <td width="100" class="style2"><div align="right"></div></td>
			  <td width="100" class="style2"><div align="right" class="style5"><?php echo $loc->getLocalised("STEP1")?></div></td>
			  <td width="100" class="style2"><div align="right" class="style6">&gt; <?php echo $loc->getLocalised("STEP2")?></div></td>
			  <td width="100" class="style2"><div align="right" class="style5"><?php echo $loc->getLocalised("STEP3")?></div></td>
			</tr>
		  </table>
		</div></td>
	  </tr>
	  <tr>
		<tr>
			<td height="490">
				<div align="center">
					<table width="400" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td height="200" background="images/cadre_400x200.jpg">
								<div align="center">  
<?php
	$allTests->getHelp();	
	if($testsOk){
		if($serverAlreadyInstalled){
			require_once("login.inc.php");
		}else{
			require_once("create_account.inc.php");
		}
  
	}
?>


								</div>
							</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
		<tr>
			<td height="10"><div align="center"><img src="images/footer_950x10.jpg" width="950" height="10" /></div></td>
		</tr>
	</table>
</body>
</html>