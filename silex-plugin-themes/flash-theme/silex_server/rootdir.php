<?php
/*
	this file is part of SILEX
	SILEX : RIA developement tool - see http://silex-ria.org/

	SILEX is (c) 2004-2007 Alexandre Hoyau and is released under the GPL License:

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License (GPL)
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.

	To read the license please visit http://www.gnu.org/copyleft/gpl.html
*/
	define( 'ROOTPATH', dirname(__FILE__) . "/");

	if (!isset($ROOTURL))
	{
		// compute url base
		$scriptUrl=$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];
		// supress the get arguments of the querry
		$qmPos=strpos($scriptUrl,'?');
		if ($qmPos > 0) $scriptUrl = substr($scriptUrl,0,$qmPos);
		
		$lastSlashPos=strrpos($scriptUrl,'/');
		$ROOTURL = 'http://'.substr($scriptUrl,0,$lastSlashPos+1);
	}
?>
