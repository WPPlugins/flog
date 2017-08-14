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
 * determine the name of the theme to display
 * the user theme, the flash_theme or the framed_theme
 */
function silex_get_theme(){
	//global $wp_query;
	if ( !is_admin() ){ // outside admin pannel
		// used in case of urlrewriting : strpos($_SERVER['REQUEST_URI'],"is_framed")>0 
		if (isset( $_GET['is_framed']) || strpos($_SERVER['REQUEST_URI'],"is_framed")>0){
			return SILEX_FRAMED_THEME_NAME;
		}
		else if (!isset( $_SESSION['no_flash'])){
			return SILEX_FLASH_THEME_NAME;
		}
	}
	return '';
}
function is_framed(){
	return isset( $_GET['is_framed'] );
}
/*
 * force the theme to the appropriate theme template
 */
function silex_get_template($template) {
	$theme = silex_get_theme();
	if (!empty($theme)) {
		// echo 'silex_get_template '.$template.' -> '.$theme.'<br>';
		return $theme;
//		$theme = get_theme($theme);
//		return $theme['Template'];
	}
	return $template;
}
add_filter('template', 'silex_get_template');

/*
 * force the theme to the appropriate theme css
 */
function silex_get_stylesheet($stylesheet) {
	$theme = silex_get_theme();
	if (!empty($theme)) {
		// echo 'silex_get_stylesheet '.$stylesheet.' -> '.$theme.'<br>';
		//$theme = 'wp-content/plugins/'.SILEX_PLUGIN_NAME.'/'.SILEX_THEME_DIR_NAME.'/'.$theme;
		return $theme;
//		$theme = get_theme($theme);
//		return $theme['Stylesheet'];
	}
	return $stylesheet;
}
add_filter('stylesheet', 'silex_get_stylesheet');

/**
 * Sets the theme root to the appropriate theme
 */
function silex_get_theme_root($theme_root)
{
	$theme = silex_get_theme();
	if (!empty($theme)) {
		// echo 'silex_get_theme_root -> '.$theme_root.' -> '.SILEX_THEME_DIR.'<br>';
		return SILEX_THEME_DIR;
	}
	return $theme_root;
}
add_filter('theme_root', 'silex_get_theme_root');
/**
 * Sets the theme uri to the appropriate theme
 */
function silex_get_theme_url($theme_url,$site_url_not_used)
{
	$theme = silex_get_theme();
	if (!empty($theme)) {
		// echo 'silex_get_theme_url '.$theme_url.' -> '.SILEX_THEME_DIR.'<br>';
		return SILEX_THEME_URL;
	}
	return $theme_url;	
}
add_filter('theme_root_uri', 'silex_get_theme_url',10,2);
?>