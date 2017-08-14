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
session_start();
if (isset( $_GET['do_reset'] )){
	unset($_SESSION['session_variables_initialized']);
	unset($_SESSION['no_flash_permalink']);
	//echo 'Session reinitialized<br>';
	silex_error('debug','Session reinitialized');
}
// init session variables
if (!isset( $_SESSION['session_variables_initialized']) && !isset( $_GET['no_flash']) && !isset( $_GET['flash']))
	if (get_option('use_flash_by_default')==1)
		unset ($_SESSION['no_flash']);
	else
		$_SESSION['no_flash'] = true;
// init done once only
$_SESSION['session_variables_initialized'] = true;
// update no_flash flag
if (isset( $_GET['no_flash'] ))
	$_SESSION['no_flash'] = true;
if (isset( $_GET['flash'] ))
	unset ($_SESSION['no_flash']);
	
/**
 * permalink for "no flash plugin" case - used after js redirection to page with deeplink
 * used in wp-content/plugins/flog/silex-plugin-themes/flash-theme/index.php
 */
function silex_set_no_flash_permalink(){
	$_SESSION['no_flash_permalink'] = silex_get_link_to_this_page(false);
}
/**
 * retrieve permalink for "no flash plugin" case after js redirection to page with deeplink
 * used in wp-content/plugins/flog/silex-plugin-themes/flash-theme/index.php
 */
function silex_get_no_flash_permalink(){
	return $_SESSION['no_flash_permalink'];
}
/**
 * reset permalink for "no flash plugin" case after js redirection to page with deeplink
 * used in wp-content/plugins/flog/silex-plugin-themes/flash-theme/index.php
 */
function silex_reset_no_flash_permalink(){
	unset($_SESSION['no_flash_permalink']);
}
/**
 * is the permalink for "no flash plugin" case set?
 * used in wp-content/plugins/flog/silex-plugin-themes/flash-theme/index.php
 */
function silex_isset_no_flash_permalink(){
	return isset($_SESSION['no_flash_permalink']);
}

?>