<?php
/*
	this file is part of SILEX
	SILEX : RIA developement tool - see http://silex.sourceforge.net/

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
	require_once("logger.php");
	/**
	 * This class is a kind of event dispatcher <br />
	 * Hooks are provided by Silex to allow your plugins to 'hook into' the rest of Silex, i.e. to call functions in your plugin at specific times<br />
	 */
	class hook_manager{
		/**
		 * reference to a logger object
		 */
		private $logger = NULL;
		/**
		 * reference to a logger object
		 */
		private static $_silex_hooks_array = NULL;

		/**
		 * constructor<br />
		 * initialize the static variable self::$_silex_hooks_array
		 */
		function hook_manager(){
			$this->logger = new logger("hook_manager");
			if (!isset(self::$_silex_hooks_array))
				self::$_silex_hooks_array = Array();
			//$this->logger->debug($action . " $siteName/$fileName to $siteName/$newFileName");
			//$this->logger->err("modifying $siteFolderPath/$fileName not allowed");
		}
		/**
		 * call the functions registered for the given hook<br />
		 * this method is called by Silex<br />
		 */
		function call_hooks($hook_name){
			$this->logger->debug("call_hooks  $hook_name");
			
			if (isset(self::$_silex_hooks_array[$hook_name])){
				// loop on registered hooks
				foreach(self::$_silex_hooks_array[$hook_name] as $hook_obj){
					$hook_obj['hook_function']($hook_obj['params']);
				}
			}
		}
		/**
		 * call the functions registered for the given hook<br />
		 * this method is called by Silex<br />
		 */
		function remove_hook($hook_name, $callback, $params=NULL){
			$this->logger->debug("remove_hook  $hook_name");
			
			if (isset(self::$_silex_hooks_array[$hook_name])){
				// loop on registered hooks
				foreach(self::$_silex_hooks_array[$hook_name] as $idx => $hook_obj){
					if ($hook_obj['hook_function'] == $callback){
						unset(self::$_silex_hooks_array[$hook_name][$idx]);
						return;
					}
				}
			}
		}
		/**
		 * register a function for the given hook<br />
		 * call this method in your plugins to be notified when the hook occures<br />
		 */
		function add_hook($hook_name, $callback, $params=NULL){
			$this->logger->debug("add_hook  $hook_name");
			
			// init the hook placeholder
			if (!isset(self::$_silex_hooks_array[$hook_name])) self::$_silex_hooks_array[$hook_name] = Array();
			// add the hook callback
			$hook_obj = Array();
			$hook_obj['hook_name'] = $hook_obj;
			$hook_obj['hook_function'] = $callback;
			$hook_obj['params'] = $params;
			self::$_silex_hooks_array[$hook_name][] = $hook_obj;
		}
	}
?>