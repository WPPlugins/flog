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
 * display and throw an error 
 */
function silex_error($error_code,$error_message){
	echo '<div class="updated fade error" style="font-weight:bold;color:red;"><p>'.$error_message.'</p></div>';
	new WP_Error($error_code, __($error_message));
	//user_error(__($error_message),E_USER_WARNING);
}
/**
 * display a success message 
 */
function silex_success($message){
	echo '<div class="updated fade success" style="font-weight:bold;color:green;"><p>'.$message.'</p></div>';
}
/** 
 * do the copy of silex themes into theme dir
 *//*
function silex_duplicate($folder,$newFolder){
//	echo "silex_duplicate($folder,$newFolder)<br>";
	mkdir($newFolder);
	// list folder and copy
	$tmpFolder = opendir($folder);
	while ($tmpFile = readdir($tmpFolder)) {
		//if ($tmpFile != "." && $tmpFile != ".." ){
		if (is_file($folder.$tmpFile)){
			if (!copy($folder.$tmpFile,$newFolder.$tmpFile)){
				return FALSE;
			}
		}
		else if (is_dir($folder.$tmpFile)===TRUE){
			// do not copy if it begins with a dot
			if (strpos($tmpFile,'.')!==0){
				// recursively duplicate folders
				if(!silex_duplicate($folder.$tmpFile.'/',$newFolder.$tmpFile.'/')){
					return FALSE;
				}
			}
		}
	}
	return TRUE;
}*/
function add_silex_options(){
	// add options
	add_option("silex_selected_template", 'wp-default', '', 'yes');
	//add_option("override_wp_url_rewrite_rules", '0', '', 'yes');
	add_option("use_flash_by_default", '0', '', 'yes');
	add_option("silex_add_link_to_flash_version", '1', '', 'yes');
}
function update_silex_options(){
	update_option('silex_selected_template', (string)$_POST["silex_selected_template"]);
	//update_option('override_wp_url_rewrite_rules', (boolean)$_POST["override_wp_url_rewrite_rules"]);
	update_option('use_flash_by_default', (boolean)$_POST["use_flash_by_default"]);
	update_option('silex_add_link_to_flash_version', (boolean)$_POST["silex_add_link_to_flash_version"]);
}
function register_silex_options(){
  register_setting( 'silex-option-group', 'silex_selected_template' );
  //register_setting( 'silex-option-group', 'override_wp_url_rewrite_rules' );
  register_setting( 'silex-option-group', 'use_flash_by_default' );
  register_setting( 'silex-option-group', 'silex_add_link_to_flash_version' );
}
/* code for override_wp_url_rewrite_rules option
			<tr valign="center">
			<th scope="row">Override WordPress URL rewrite rule</th>
				<td><input type="checkbox" name="override_wp_url_rewrite_rules" <?php echo get_option('override_wp_url_rewrite_rules')==1?'checked="checked"':''; ?> /></td>
			</tr>*/
/**
 * returns the total number of posts
 * not used for now
 */
function get_total_number_of_posts(){
  $num = 0;
  $categories = get_categories(); 
  foreach ($categories as $cat) {
	$num += $cat->category_count;
  }
  return $num;
}
/**
 * retrieve a link to the current page, eventually forcing value of no_flash/flash
 * $force_flash_version can be true (force flash), false (force no_flash), nulll (do not force, keep going with current value)
 */
function silex_get_link_to_this_page($force_flash_version){
	global $query_string;
	$return_string = $query_string;
	
	if ($force_flash_version === true){
		$return_string = remove_query_arg('no_flash',$return_string);
		$return_string = add_query_arg('flash','1',$return_string);
	}
	else if ($force_flash_version === false){
		$return_string = remove_query_arg('flash',$return_string);
		$return_string = add_query_arg('no_flash','1',$return_string);
	}
	return $return_string;
}
?>