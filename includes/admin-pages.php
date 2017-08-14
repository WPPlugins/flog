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
function silex_options() {
	if (isset($_POST["silex_selected_template"])){
		update_silex_options();
		silex_success('Configuration Updated!');
	}
/* code for override_wp_url_rewrite_rules option
			<tr valign="center">
			<th scope="row">Override WordPress URL rewrite rule</th>
				<td><input type="checkbox" name="override_wp_url_rewrite_rules" <?php echo get_option('override_wp_url_rewrite_rules')==1?'checked="checked"':''; ?> /></td>
			</tr>*/
			
require_once(ABSPATH.'wp-admin/includes/class-wp-filesystem-base.php');
require_once(ABSPATH.'wp-admin/includes/class-wp-filesystem-direct.php');
function get_silex_themes_list(){

	$wp_filesystem = new WP_Filesystem_Direct('');
	$path = SILEX_SERVER_DIR.'/contents/';
	$result = Array();

	$dirlist = $wp_filesystem->dirlist($path);
	foreach ( (array) $dirlist as $filename => $fileinfo ) {
		if ( 'd' == $fileinfo['type'] && $fileinfo['name']!='not.found') {
			$result[] = $fileinfo['name'];
		}
	}
	return $result;
}
function display_silex_themes_combo(){
	$themes_list = get_silex_themes_list();
	$current_selection = get_option('silex_selected_template');
	
	?>
	<td>
		<select name="silex_selected_template" STYLE="width:80%">
		
	<?php
	foreach ($themes_list as $theme_name){
	?>
		<option value="<?php echo $theme_name; ?>" <?php if ($current_selection==$theme_name) echo 'SELECTED'; ?>><?php echo $theme_name; ?></option>
	<?php
	}
	?>
		</select>
	</td>
	<?php
}
?>
<div class="wrap">
	<h2>Settings of Silex plugin</h2>
	<h3>Settings</h3>
	<form method="post" action="">
		<?php wp_nonce_field('update-options'); ?>
		<table class="form-table">
			<tr valign="center">
			<th scope="row">Selected Silex Theme</th>
				<?php display_silex_themes_combo(); ?>
			</tr>
			<tr valign="center">
			<th scope="row">Use Flash version by default</th>
				<td><input type="checkbox" name="use_flash_by_default" <?php echo get_option('use_flash_by_default')==1?'checked="checked"':''; ?>" /></td>
			</tr>
			<tr valign="center">
			<th scope="row">Add a link to Flash version at the bottom of the HTML version</th>
				<td><input type="checkbox" name="silex_add_link_to_flash_version" <?php echo get_option('silex_add_link_to_flash_version')==1?'checked="checked"':''; ?>" /></td>
			</tr>
		</table>
		<input type="hidden" name="action" value="update" />
		<input type="hidden" name="page_options" value="silex_selected_template ,override_wp_url_rewrite_rules,use_flash_by_default" />
		<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
		</p>
	</form>
	<br />
	<h3>Useful links</h3>
	<ul>
		<li type="circle">This <a href="http://wordpress.org/extend/plugins/flog/">plugin page at WordPress.org</a></li>
		<li type="circle">Create your own templates with <a href="http://silex-ria.org/">Silex</a></li>
		<ol>
			<li type="circle"><a href="http://en.wikipedia.org/wiki/Silex_Flash_CMS">Silex at wikipedia</a></li>
			<li type="circle"><a href="http://silex-ria.org/help/">Silex online help</a> (<a href="http://silex-ria.org/help/">Aide en Francais</a>)</li>
			<li type="circle"><a href="http://silex-ria.org/intro">Silex video demo</a></li>
			<li type="circle"><a href="http://hoyau.info/demos/silex_server/manager-demo/start">Silex online demo</a></li>
			<li type="circle"><a href="http://silex-ria.org/open.source.flash.cms/silex/telecharger" title="Download Silex">Download Silex</a></li> 
			<li type="circle"><a href="http://silex.hoyau.info/forum_en/" title="Silex forum in English" >Silex forum in English</a></li> 
			<li type="circle"><a href="http://silex.hoyau.info/forum/" title="Silex forum in French" >Silex forum in French / Forum en francais</a></li> 
			<li type="circle"><a href="https://silex.svn.sourceforge.net/svnroot/silex/trunk" title="Silex source code" >Silex source code</a></li> 
			<li type="circle"><a href="http://sourceforge.net/mail/?group_id=192954" title="Silex mailing lists" >Silex mailing lists</a></li> 
			<li type="circle"><a href="http://www.gnu.org/licenses/gpl.html" title="Silex GPL license" >Silex GPL license</a></li> 
		</ol>
	</ul>
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<small>
	___________________________________________
	<br />Plugin author: <a href="http://silex-ria.org/lex">Alexandre Hoyau</a>
	<br />Powered by <a href="http://silex-ria.org/">Silex</a>
	<br />Special thanks to <a href="http://silexlabs.com/">the Silex team</a>
	</small>
</div>
<?php
}


function silex_menu() {
  add_options_page('Silex Plugin Options', 'Silex Plugin', 8, 'silex-admin-page', 'silex_options');
}

function register_silex_settings() { // whitelist options
	register_silex_options();
}

if ( is_admin() ){ // admin actions
	add_action('admin_menu', 'silex_menu');
	add_action( 'admin_init', 'register_silex_settings' );
} else {
  // non-admin enqueues, actions, and filters
}
?>
