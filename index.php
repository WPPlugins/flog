<?php
/*  Copyright 2009  Alexandre Hoyau  (email : lex [at] silex-ria . org)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
/**
 * @package flog
 * @author Lexa Yo
 * @version 0.1
 */
/*
Plugin Name: flog
Plugin URI: http://wordpress.org/extend/plugins/flog/
Description: flog
Author: Lexa Yo
Version: 0.1
Author URI: http://silex-ria.org/lex
*/

require_once(dirname(__FILE__).'/includes/constants.php');
require_once(SILEX_INCLUDE_DIR.'/functions.php');
if (version_compare(PHP_VERSION, '5.0', '<'))
{
	if ($_GET['activate'] == true)
	{
//		silex_error('php-error','Error: Silex requires PHP 5.0 or newer and you are running '.PHP_VERSION);
		silex_error('php-error','Error: Silex requires PHP 5.0 or newer and you are running '.PHP_VERSION.'<br />You can try to <a href="'.SILEX_SERVER_URL.'/install">run Silex installer to solve this problem</a>');
	}
}
else
{
	// check if Silex themes are properly installed (after activation)
/*	if (!is_dir(WP_CONTENT_DIR.'/themes/'.SILEX_THEMES_DIR_NAME.'/') && $_GET['activate'] != true){
		silex_error('file-error','Silex themes not found');
	}
	else{
*/
		require_once(SILEX_INCLUDE_DIR.'/sessions.php');
		require_once(SILEX_INCLUDE_DIR.'/redirect-functions.php');
		require_once(SILEX_INCLUDE_DIR.'/rss-functions.php');
		require_once(SILEX_INCLUDE_DIR.'/feed-index.php');
		require_once(SILEX_INCLUDE_DIR.'/theme-switcher-index.php');
		require_once(SILEX_INCLUDE_DIR.'/install-index.php');
		require_once(SILEX_INCLUDE_DIR.'/admin-pages.php');
		require_once(SILEX_INCLUDE_DIR.'/rewrite-links.php');
//	}
}
?>
