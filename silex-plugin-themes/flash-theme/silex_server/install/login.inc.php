<br/><?php echo $loc->getLocalised("ACCOUNT_SERVER_ALREADY_INSTALLED")?><br />
<?php echo $loc->getLocalised("ACCOUNT_ASK_LOGIN")?><br />
<br />
<br />
<form action="step3.php?langCode=<?php echo $loc->languageUsed?>" method="post">

	<?php echo $loc->getLocalised("ACCOUNT_LOGIN")?>
	<input name="login"><br />
	<br />
	<?php echo $loc->getLocalised("ACCOUNT_PASSWORD")?>
	<input name="password" type="password"/>
	<br /><br />
	<input type="submit" value="<?php echo $loc->getLocalised("ACCOUNT_NEXT")?>" />
	<br />
</form>