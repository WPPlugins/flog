<?php 
	$this->methodTable = array(
		"listToolsFolderContent" => array(
			"description" => "list tools folder content",
			"arguments" => array("path" => array("type" => "string","required" => true)),
			"access" => "remote",
			"returns" => "array"
		),
		"listFtpFolderContent" => array(
			"description" => "list ftp folder content",
			"arguments" => array("path" => array("type" => "string","required" => true)),
			"access" => "remote",
			"returns" => "array"
		),
		"listWebsiteFolderContent" => array(
			"description" => "list all xml files in the current website's content folder",
			"arguments" => array("id_site" => array("type" => "string","required" => true)),
			"access" => "remote",
			"returns" => "array"
		),
		"getLanguagesList" => array(
			"description" => "list all files in the lang folder and returns it as a string. used by the manager and by index.php",
			"access" => "remote",
			"returns" => "string"
		),
		"listLanguageFolderContent" => array(
			"description" => "list all files in the lang folder",
			"access" => "remote",
			"returns" => "array"
		),
		"listFolderContent" => array(
			"description" => "list a folder content",
			"arguments" => array("path" => array("type" => "string","required" => true), "isRecursive" => array("type" => "boolean","required" => false)),
			"access" => "private",//"remote",//
			"returns" => "array"
		),
		"getFolderSize" => array(
			"description" => "returns the size of a folder in a readable form",
			"arguments" => array("path" => array("type" => "string","required" => true)),
			"access" => "remote",
			"returns" => "string"
		),
		"getDynData" => array(
			"description" => "returns an array containing the data corresponding to the apps . The data returned could be passed through javascript setVariable but it would result in a huge index.php file. Which is not good for SEO and for user experience.",
			"arguments" => array("wesiteInfo" => array("type" => "object","required" => true),"filesList" => array("type" => "array","required" => false)),
			"access" => "remote",//"private",//
			"returns" => "array"
		),
		"getWebsiteConfig" => array(
			"description" => "read a website config file and the server config",
			"arguments" => array("id_site" => array("type" => "string","required" => true),"mergeWithServerConfig" => array("type" => "string","required" => false)),
			"access" => "remote",
			"returns" => "array"
		),
		"deleteWebsite" => array(
			"description" => "delete a website",
			"arguments" => array("id_site"),
			"access" => "remote",
			"roles" => AUTH_ROLE_USER,
			"returns" => "boolean"
		),
		"renameWebsite" => array(
			"description" => "rename a website",
			"arguments" => array("old_id_site","new_id_site"),
			"access" => "remote",
			"roles" => AUTH_ROLE_USER,
			"returns" => "boolean"
		),
		"createWebsite" => array(
			"description" => "create a website",
			"arguments" => array("id_site"),
			"access" => "remote",
			"roles" => AUTH_ROLE_USER,
			"returns" => "boolean"
		),
		"writeWebsiteConfig" => array(
			"description" => "write into a website config file or to create a website",
			"arguments" => array("websiteInfo", "id_site"),
			"access" => "remote",
			"roles" => AUTH_ROLE_USER,
			"returns" => "boolean"
		),
		"duplicateWebsite" => array(
			"description" => "No description given.",
			"arguments" => array("id_site", "newName"),
			"access" => "remote",
			"roles" => AUTH_ROLE_USER,
			"returns" => "string"
		),
		"doDuplicateWebsite" => array(
			"description" => "duplicate a website folder",
			"arguments" => array("folder", "newFolder"),
			"access" => "private"
		),
		"createWebsiteIndex" => array(
			"description" => "create the search index",
			"arguments" => array("id_site" => array("type" => "string")),
			"access" => "remote",
			"roles" => AUTH_ROLE_USER,
			"returns" => "integer"
		),
		"writeSectionData" => array(
			"description" => "write xml data to a file and store html data in db",
			"arguments" => array("xmlData", "xmlFileName", "sectionName", "id_site", "seoObject", "domObject"),
			"access" => "remote",//"private",//
			"roles" => AUTH_ROLE_USER,
			"returns" => "string"
		), 
		"renameSection" => array(
			"description" => "rename all files corresponding to a site's section",
			"arguments" => array("siteName" => array("type" => "string","required" => true), "oldSectionName" => array("type" => "string","required" => true), "newSectionName" => array("type" => "string","required" => true)),
			"access" => "remote",//"private",//
			"roles" => AUTH_ROLE_USER
		), 
		"duplicateSection" => array(
			"description" => "duplicate all files corresponding to a site's section",
			"arguments" => array("siteName" => array("type" => "string","required" => true), "oldSectionName" => array("type" => "string","required" => true), "newSectionName" => array("type" => "string","required" => true)),
			"access" => "remote",//"private",//
			"roles" => AUTH_ROLE_USER
		), 
		"readConfigFile" => array(
			"description" => "read a config file",
			"arguments" => array("filePath" => array("type" => "string","required" => true), "fileFormat" => array("type" => "string","required" => true)),
			"access" => "remote",
			"returns" => "array"
		),
		"updateConfigFile" => array(
			"description" => "update a config file. doesn't create it(yet?)",
			"arguments" => array("filePath" => array("type" => "string","required" => true), "fileFormat" => array("type" => "string","required" => true),"dataToMerge" => array("type" => "array","required" => true)),
			"access" => "remote",
			"roles" => AUTH_ROLE_USER,
			"returns" => "boolean"
		),
		"getLogins" => array(
			"description" => "get logins array, data provider",
			"access" => "remote",
			"roles" => AUTH_ROLE_USER,
			"returns" => "array"
		),
		"setPassword" => array(
			"description" => "set a password",
			"arguments" => array("login" => array("type" => "string","required" => true), "password" => array("type" => "string","required" => true)),
			"access" => "remote",
			"roles" => AUTH_ROLE_USER
		),
		"deleteAccount" => array(
			"description" => "delete an account",
			"arguments" => array("login" => array("type" => "string","required" => true)),
			"access" => "remote",
			"roles" => AUTH_ROLE_USER
		),
		"doLogin" => array(
			"description" => "check login and read role in silex_users database",
			"access" => "remote",
			"roles" => AUTH_ROLE_USER,
			"returns" => "boolean"
		),
		"doLogout" => array(
			"description" => "logout",
			"access" => "remote",
			"returns" => "boolean"
		)
	);
?>