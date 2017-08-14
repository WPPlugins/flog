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
// compute plugin name
if ( ! defined( 'SILEX_PLUGIN_NAME' ) ){
	$full_path = str_replace('\\','/',__FILE__);
	$start=strlen(WP_PLUGIN_DIR) + 1;
	$len=strrpos($full_path,basename(__FILE__)) - $start - strlen('includes') - 2;
	$pluginName = substr(__FILE__,$start,$len);

	define( 'SILEX_PLUGIN_NAME', $pluginName);
	// for use in flash theme
	add_option('silex_plugin_name');
	update_option('silex_plugin_name',$pluginName);
}
// folder, plugin and themes names
if ( ! defined( 'SILEX_THEME_DIR_NAME' ) )
	define( 'SILEX_THEME_DIR_NAME', 'silex-plugin-themes' );
if ( ! defined( 'SILEX_FLASH_THEME_NAME' ) )
	define( 'SILEX_FLASH_THEME_NAME', 'flash-theme' );
if ( ! defined( 'SILEX_FEED_THEME_NAME' ) )
	define( 'SILEX_FEED_THEME_NAME', 'feed-theme' );
if ( ! defined( 'SILEX_FRAMED_THEME_NAME' ) )
	define( 'SILEX_FRAMED_THEME_NAME', 'framed-theme' );


// URLs
if ( ! defined( 'WP_CONTENT_URL_MU_COMPATIBLE' ) ){
	if (defined('VHOST') && VHOST == 'no'){
		// wordpress mu with folders instead of subdomains
/*
		$current_site = get_current_site();
		define( 'WP_CONTENT_URL_MU_COMPATIBLE',  substr(get_option( 'siteurl' ),0,-(strlen($current_site->site_name)+1)) . '/wp-content' );
*/		
		$current_site = get_current_site();
		//echo get_option( 'siteurl' )." - ".$current_site->path." - ".strlen($current_site->site_name);
		//echo substr(get_option( 'siteurl' ),0,strrpos(get_option( 'siteurl' ),'/')) . '/wp-content';
		//echo get_bloginfo("wpurl").' - '.get_bloginfo("url");
		// remove the webstite subdomain (after the last slash - but for primaty blog)
		define( 'WP_CONTENT_URL_MU_COMPATIBLE',  substr(get_option( 'siteurl' ),0,strrpos(get_option( 'siteurl' ).'/',$current_site->path)+strlen($current_site->path)) . '/wp-content' );
//		echo substr(get_option( 'siteurl' ),0,strrpos(get_option( 'siteurl' ),$current_site->path)+strlen($current_site->path))." - ".get_option( 'siteurl' ).",".$current_site->path;
	}
	else{
		// normal use (no sub folder)
		define( 'WP_CONTENT_URL_MU_COMPATIBLE', get_option( 'siteurl' ) . '/wp-content' );
	}
}
if ( ! defined( 'WP_CONTENT_URL' ) ){
	// lets try to define it
	// it is usually allready defined by wordpress
	define( 'WP_CONTENT_URL', WP_CONTENT_URL_MU_COMPATIBLE);
}

if ( ! defined( 'WP_PLUGIN_URL_MU_COMPATIBLE' ) ){
	define( 'WP_PLUGIN_URL_MU_COMPATIBLE', WP_CONTENT_URL_MU_COMPATIBLE.'/plugins' );
}
if ( ! defined( 'WP_PLUGIN_URL' ) ){
	// lets try to define it
	// it is usually allready defined by wordpress
	define( 'WP_PLUGIN_URL', WP_PLUGIN_URL_MU_COMPATIBLE );
}

if ( ! defined( 'SILEX_THEME_URL' ) )
	define( 'SILEX_THEME_URL', WP_PLUGIN_URL_MU_COMPATIBLE.'/'.SILEX_PLUGIN_NAME.'/'.SILEX_THEME_DIR_NAME );

if ( ! defined( 'SILEX_FLASH_THEME_URL' ) )
	define( 'SILEX_FLASH_THEME_URL', SILEX_THEME_URL.'/'.SILEX_FLASH_THEME_NAME );

if ( ! defined( 'SILEX_FRAMED_THEME_URL' ) )
	define( 'SILEX_FRAMED_THEME_URL', SILEX_THEME_URL.'/'.SILEX_FRAMED_THEME_NAME );

if ( ! defined( 'SILEX_SERVER_URL' ) )
	define( 'SILEX_SERVER_URL', SILEX_FLASH_THEME_URL.'/silex_server' );

//echo WP_CONTENT_URL.' - '.WP_CONTENT_URL_MU_COMPATIBLE.' - '.WP_PLUGIN_URL_MU_COMPATIBLE.' - '.WP_PLUGIN_URL.' - '.SILEX_THEME_URL.' - '.SILEX_FLASH_THEME_URL.' - '.SILEX_SERVER_URL;

// directories
if ( ! defined( 'WP_CONTENT_DIR' ) )
      define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( ! defined( 'WP_PLUGIN_DIR' ) )
      define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );

if ( ! defined( 'SILEX_PLUGIN_DIR' ) )
	define( 'SILEX_PLUGIN_DIR', WP_PLUGIN_DIR.'/'.SILEX_PLUGIN_NAME );
if ( ! defined( 'SILEX_INCLUDE_DIR' ) )
	define( 'SILEX_INCLUDE_DIR', SILEX_PLUGIN_DIR.'/includes' );
if ( ! defined( 'SILEX_THEME_DIR' ) )
	define( 'SILEX_THEME_DIR', SILEX_PLUGIN_DIR.'/'.SILEX_THEME_DIR_NAME );

if ( ! defined( 'SILEX_FLASH_THEME_DIR' ) )
	define( 'SILEX_FLASH_THEME_DIR', SILEX_THEME_DIR.'/'.SILEX_FLASH_THEME_NAME );
if ( ! defined( 'SILEX_SERVER_DIR' ) )
	define( 'SILEX_SERVER_DIR', SILEX_FLASH_THEME_DIR.'/silex_server' );

if ( ! defined( 'SILEX_FEED_THEME_DIR' ) )
	define( 'SILEX_FEED_THEME_DIR', SILEX_PLUGIN_DIR.'/'.SILEX_FEED_THEME_NAME );
	
	


?>
