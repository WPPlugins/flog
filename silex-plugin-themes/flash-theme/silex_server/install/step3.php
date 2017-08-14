<?php
	set_include_path(get_include_path() . PATH_SEPARATOR . "../");
	set_include_path(get_include_path() . PATH_SEPARATOR . "../cgi/includes/");
	set_include_path(get_include_path() . PATH_SEPARATOR . "../cgi/library/");
	require_once("password_manager.php");
	require_once("localisation.php");
	
	$loc = new localisation();
	$p = new password_manager();
	$password = $_POST["password"];
	$login = $_POST["login"];
	$isFirstTime = false;
	if($p->isAuthenticationFileAvailable()){
		if(!$p->authenticate($login, $password)){
			echo "password invalid";
			exit(-1);
		}
	}else{
		if($password && $password != ''){
			$p->createFile($login, $password);
			
		}
		$isFirstTime = true;
	}

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $loc->getLocalised("TITLE")?> &gt; <?php echo $loc->getLocalised("STEP3")?></title>
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
.style7 {font-size: 16px}
.style8 {color: #666666}
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
			  <td width="100" class="style2"><div align="right" class="style5"><?php echo $loc->getLocalised("STEP2")?></div></td>
			  <td width="100" class="style2"><div align="right" class="style6">&gt; <?php echo $loc->getLocalised("STEP3")?></div></td>
			</tr>
		  </table>
		</div></td>
	  </tr>
  <tr>
    <td height="490"><div align="center">
      <table width="400" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td height="400" background="images/cadre_400x400.jpg"><div align="center">
		  <?php
			if($isFirstTime){
		  ?>
		  <span class="style7"><?php echo $loc->getLocalised("ACCOUNT_CREATED")?><br />
            <?php echo $loc->getLocalised("SERVER_INSTALLED")?></span> <br />
            <br />
            <br />
		  <?php
			}
		  ?>
            <?php echo $loc->getLocalised("LAST_STEP")?> <br />

            <br />
            <br />
            <br />
            <br />
            <iframe src="./htaccess/link.php?langCode=<?php echo $loc->languageUsed?>" width="300" height="100"  frameborder="0"/></iframe>
            <br />
            <br />
            <br />
            <br />
			<?php echo $loc->getLocalised("THATS_IT")?>
            <br />
            <form action='../?/manager'><input type='submit' value='<?php echo $loc->getLocalised("GOTO_MANAGER")?>'/></form><br />
          </div></td>
        </tr>
      </table>
    </div></td>
  </tr>
  <tr>
    <td height="10"><div align="center"><img src="images/footer_950x10.jpg" width="950" height="10" /></div></td>
  </tr>
</table>
</body>
</html>
