<?php

	require_once("../rootdir.php");
	require_once("localisation.php");
	$loc = new localisation();
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $loc->getLocalised("TITLE")?> &gt; <?php echo $loc->getLocalised("STEP1")?></title>
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
.style7 {
	color: #FF6600;
	font-weight: bold;
}
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
          <td width="100" class="style2"><div align="right" class="style6">&gt; <?php echo $loc->getLocalised("STEP1")?></div></td>
          <td width="100" class="style2"><div align="right" class="style5"><?php echo $loc->getLocalised("STEP2")?></div></td>
          <td width="100" class="style2"><div align="right" class="style5"><?php echo $loc->getLocalised("STEP3")?></div></td>
        </tr>
      </table>
    </div></td>
  </tr>
  <tr>
    <td height="490"><div align="center">
      <table width="400" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td height="200" background="images/cadre_400x200.jpg"><div align="center"><?php echo $loc->getLocalised("WELCOME_INTRO_1")?> <span class="style7">silex</span> <?php echo $loc->getLocalised("WELCOME_INTRO_2")?> <br />
            <?php echo $loc->getLocalised("WELCOME_RUN_TESTS")?> <br />
            <br />
            <br />
            <form action="step2.php?langCode=<?php echo $loc->languageUsed?>" method="post"><input name="Submit" type="submit" value="<?php echo $loc->getLocalised("WELCOME_LABEL_BUTTON")?>" />
            </form>
            <br />
			<?php echo $loc->getLocalised("WELCOME_CHOOSE_LANGUAGE_DESCRIPTION")?>
			<select id="langSelect">
				<?php 
					foreach($loc->availableLanguages as $langCode){
						if($langCode == $loc->languageUsed){
							echo "<option selected='yes' value='" . $langCode . "'>" . $langCode . "</option>\n";
						}else{
							echo "<option value='" . $langCode . "'>" . $langCode . "</option>\n";
						}
					}
				?>
			</select>
			<input type="submit" onclick="window.location.href='index.php?langCode=' + getElementById('langSelect').value;"
					value="<?php echo $loc->getLocalised("WELCOME_CHOOSE_LANGUAGE_BUTTON")?>"/>
          </div></td>
        </tr>
      </table>
    </div></td>
  </tr>
  <tr>
    <td height="10"><div align="center"><img src="images/footer_950x10.jpg" width="950" height="10" /></div></td>
  </tr>
</table>
<?php
	if (version_compare(PHP_VERSION,'5','<')){
?>
<!--
		//tests for different ways to force php 5
	//the closing </iframe> tags is so that firefox shows all the iframes and not just the first
-->
	<iframe src="./php_version_test/setenv/" frameborder="0" width="0" height="0"></iframe>
	<iframe src="./php_version_test/addtype/" frameborder="0" width="0" height="0"></iframe>	
	<iframe src="./php_version_test/addtype2/" frameborder="0" width="0" height="0"></iframe>	

<?php
	}
?>
</body>
</html>

