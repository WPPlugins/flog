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
/**
 *
 */
function silex_activation_hook(){
/*
	$path = SILEX_PLUGIN_DIR.'/'.SILEX_THEME_DIR_NAME.'/';
	$newPath = WP_CONTENT_DIR.'/themes/'.SILEX_THEME_DIR_NAME.'/';
	
	// copy the themes into the theme directory
	try{
		$error = false;
		if (is_dir($newPath)===true){
			// rename as "bkp"
			$bkp_name = WP_CONTENT_DIR.'/themes/'.SILEX_THEME_DIR_NAME.'_backup_'.date('Y-m-d_H-i-s');
			if (!rename($newPath,$bkp_name)){
				$error = true;
			}
		}
		// only do the rename action if no error
		if (!$error){
			if (!silex_duplicate($path,$newPath)){
				silex_error('file-error','Impossible to copy Silex themes to themes directory: '.$path." -> ".$newPath);
			}
		}
		else{
			silex_error('warning','The themes were allready installed. The previous version has been renamed '.$bkp_name);
		}
		
	}
	catch (Exception $e) {
		$message = $e->getMessage();
		if (!$message) $message = "Could not duplicate Silex themes directory";
		silex_error('file-error',$message);
	}
*/	// add options (function in includes/functions.php)
	add_silex_options();
}

// does not work => call it directly register_activation_hook( __FILE__, 'silex_activation_hook');
if ($_GET['activate'] == true)
{
	silex_activation_hook();
}
?>