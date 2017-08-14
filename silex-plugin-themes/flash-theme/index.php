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
require_once(ABSPATH."wp-content/plugins/".get_option('silex_plugin_name').'/includes/constants.php');
set_include_path(get_include_path() . PATH_SEPARATOR . SILEX_SERVER_DIR);

//define( 'ROOTURL' , SILEX_SERVER_URL . '/');
$ROOTURL = SILEX_SERVER_URL . '/';
?>
<?php
/**
 * Silex hook for head tag
 */
function head_hook(){
	$do_redirect = false;
?>
<!-- head_hook -->
<script type="text/javascript">
		$rootUrl = "<?php echo $ROOTURL; ?>";
</script>

<title><?php wp_title('&laquo;', true, 'right'); ?> <?php bloginfo('name'); ?></title>
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<?php 
	wp_get_archives('type=monthly&format=link');
	wp_head();
?>
<!-- end head_hook -->
<?php 
	return true;
}
?><?php
/**
 * Silex hook for noscript tag
 */
function noembed_hook(){
?>
<!-- noembed_hook -->
<h1><a href="<?php bloginfo('url'); ?>/"><?php bloginfo('name'); ?></a></h1>
<!-- end header -->
<?php 
echo "<!-- pages<br>";
wp_list_pages();
echo "<!-- authors --><br>";
wp_list_authors(); 
echo "<!-- categories --><br>";
wp_list_categories(); 
echo "<!-- bookmarks --><br>";
wp_list_bookmarks(); 
echo "<!-- archives --><br>";
wp_get_archives(); 
echo "<!-- menu --><br>";
wp_page_menu(); 

if (have_posts()) : while (have_posts()) : the_post(); 
	the_date('','<h2>','</h2>'); ?>
<h3><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></h3>
<?php the_category(',') ?>
<?php the_tags(__('Tags: '), ', ', ' &#8212; '); ?>
<?php the_author() ?> @ <?php the_time() ?>
<?php the_content(__('(more...)')); ?>
<?php wp_link_pages(); ?>

<?php endwhile; else: ?>
<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
<?php endif; ?>
<!-- end noembed_hook -->
<?php 
	return true;
}
?><?php
/**
 * Silex hook for the script tag
 */
function script_hook(){
?>
<!-- script_hook -->
<script type="text/javascript">
		//trace("script_hook "+openSilexPage);
		// redirection
		<?php 
			global $websiteConfig; 
			
			$redirect_url = get_redirect_url(urldecode($websiteConfig['homeDeeplink']),urldecode($websiteConfig['singleDeeplink']),urldecode($websiteConfig['pageDeeplink']),urldecode($websiteConfig['archiveDeeplink']),urldecode($websiteConfig['searchDeeplink']),urldecode($websiteConfig['error404Deeplink']));
			
			if(isset($redirect_url) && $redirect_url != ''){
				// permalink for "no flash plugin" case - used after js redirection to page with deeplink
				silex_set_no_flash_permalink();
				//echo 'alert("set session '.$_SESSION['no_flash_permalink'].'");';
				echo 'window.location="'.$redirect_url.'";';
			}
			else{
				// permailink in get for "no flash plugin" case
				if (silex_isset_no_flash_permalink()){
					//echo 'alert("has session '.$_SESSION['no_flash_permalink'].'");';
					$no_flash_permalink = silex_get_no_flash_permalink();
					silex_reset_no_flash_permalink();
				?>
				$no_flash_page='<?php echo bloginfo('url').'/?'.$no_flash_permalink; ?>';
				<?php 
				} 
				else{
					?>
					$no_flash_page='<?php echo bloginfo('url').'/?'.silex_get_link_to_this_page(false); ?>';
					<?php 
				}
			}
			?>
		function openPage($id)
		{
			//alert("open page" + $id+" - "+openSilexPage);
			$page_to_open = "<?php echo $websiteConfig['CONFIG_START_SECTION'].'/'.urldecode($websiteConfig["pageDeeplink"]); ?>";
			$page_to_open = $page_to_open.replace(new RegExp("%", "g"),$id);
			openSilexPage($page_to_open);
			silex_refresh_event();
		}
		function openPost($id)
		{
			//alert("open post" + $id+" - "+openSilexPage);
			$page_to_open = "<?php echo $websiteConfig['CONFIG_START_SECTION'].'/'.urldecode($websiteConfig["singleDeeplink"]); ?>";
			$page_to_open = $page_to_open.replace(new RegExp("%", "g"),$id);
			openSilexPage($page_to_open);
			silex_refresh_event();
		}
		function openCategory($id)
		{
			//alert("open cat" + $id);
			$page_to_open = "<?php echo $websiteConfig['CONFIG_START_SECTION'].'/'.urldecode($websiteConfig["archiveDeeplink"]); ?>";
			$page_to_open = $page_to_open.replace(new RegExp("%", "g"),$id);
			openSilexPage($page_to_open);
			silex_refresh_event();
		}
		/**
		 * refresh
		 * called after submitting a post for example
		 * do as if the refresh_btn was clicked (part of the API) / do nothing if there is no refresh_btn on the scene
		 */
		function silex_refresh_event()
		{
			//alert("silex_refresh_event ");
			// do as if the refresh_btn was clicked (part of the API) / do nothing if there is no refresh_btn on the scene
			document.getElementById('silex').SetVariable('silex_exec_str','refresh_btn._onRelease');
		}
		// subscribe tosilex hash change event
		silexNS.HookManager.addHook("openSilexPage",openSilexPageFlogHook,null);
		/**
		 * subscribe tosilex hash change event<br>
		 * set the post or page id / the search or archive params
		 */
		function openSilexPageFlogHook($event)
		{
			
			//alert('openSilexPageFlogHook '+$event.hashValue);
						
			//console.log('openSilexPage '+$hashValue);
			// update the section data in silex
			setFlashVarsForSilexPage($event.hashValue);
			// open the page in Silex
			// allready in silex openSilexPage function :
			//		document.getElementById('silex').SetVariable('silex_exec_str','open:'+$hashValue);
			//silex_refresh_event();
		}
		/**
		 * pass a given variable to silex DataContainer
		 */
		function passFlashVar($data_container_section,$var_name,$value){
			$silex_object_tmp = document.getElementById('silex');
			if ($silex_object_tmp)
				$silex_object_tmp.SetVariable('silex_exec_str','DataContainer.'+$data_container_section+'.'+$var_name+'='+$value);
			//console.log('passFlashVar DataContainer.'+$data_container_section+'.'+$var_name+'='+$value);
		}
		
		/**
		 * set the post or page id / the search or archive params
		 * pass all variable from deeplink to flashvars
		 * variables in the deeplink are with this format: /varname=value/ OR &varname=value&
		 */
		function setFlashVarsForSilexPage($hashValue,$additional_flashvars_string){
			//console.log("setFlashVarsForSilexPage "+$hashValue);
			//$additional_flashvars = '';
			
			// replace "/" by "&" in order to handle /varname=value/ and &varname=value&
			$hashValueWithAmperstand = $hashValue.replace(new RegExp("/", "g"),"&");
			
			// adds additionnal variable (optionnal, used for 1st init)
			if ($additional_flashvars_string != undefined)
				$hashValueWithAmperstand += "&"+$additional_flashvars_string;
			// init archive and search vars
			passFlashVar('selection','cat','');
			passFlashVar('selection','tag','');
			passFlashVar('selection','s','');
			/* TO DO ALL OTHER TAGS ? */
			
			// split and extract key/value pairs
			var $vars_array = $hashValueWithAmperstand.split("&");
			for ($i = 0; $i < $vars_array.length; $i++)
			{
				//console.log("setFlashVarsForSilexPage Start loop "+$vars_array[$i]);
				var $varsPair_array = $vars_array[$i].split("=");
				if ($varsPair_array && $varsPair_array[0]!=undefined && $varsPair_array[1]!=undefined){
					switch($varsPair_array[0]){
						case "p":
						case "page":
							//alert('passFlashVar("post","ID",'+$varsPair_array[1]+")");
							//$additional_flashvars += "post_ID="+$varsPair_array[1]+"&";
							passFlashVar("post","ID",$varsPair_array[1]);
							// console.log("setFlashVarsForSilexPage post.ID,"+$varsPair_array[0]+","+$varsPair_array[1]);
							break;
						default:
							//$additional_flashvars += $varsPair_array[0]+"="+$varsPair_array[1]+"&";
							passFlashVar('selection',$varsPair_array[0],$varsPair_array[1]);
							// console.log("setFlashVarsForSilexPage "+$varsPair_array[0]+","+$varsPair_array[1]);
					}
				}
			}
			//return $additional_flashvars;
		}
		function initFlashVars(){
			// flash vars from url
			//$additional_flashvars = setFlashVarsForSilexPage(getUrlHash());
			
			// build FlashVars from wordpress data
			<?php 
				$flashvars_string='';
				include_once SILEX_INCLUDE_DIR.'/build_flashvars.php'; 
			?>
			$additional_flashvars = "<?php echo $flashvars_string; ?>";
			
			$DEFAULT_WEBSITE="<?php echo get_option('silex_selected_template'); ?>";
			// to do :
			//		$htmlTitle

		}
		/**
		 * callback for silex template initialisation
		 * action on DataContainer: onLoad javascript:silex_data_container_ready(); 
		 */
		function silex_data_container_ready(){
			silexJsObj.firebug=true;
			//console.log("silex_data_container_ready");
			// add FlashVars from wordpress data -> set the DataContainer
			setFlashVarsForSilexPage(getUrlHash(), "<?php echo $flashvars_string; ?>");
		}
		initFlashVars();
</script>
<!-- end script_hook -->
<?php
	return true;
}
?>
<!-- index_body_end_hook -->
<?php
/**
 * Silex hook for the script tag
 */
function index_body_end_hook(){
?>
<script type="text/javascript">
	//silexJsObj.firebug=false;
	
	// prevent from redirecting - the id_site is not in the url, and it is not after the # (which was the case in silex <v1.5)
	function compatibility_check(){
	}
</script>
<?php
	return true;
}
?>
<!-- end index_body_end_hook -->
<?php
set_include_path(get_include_path() . PATH_SEPARATOR . SILEX_SERVER_DIR.'/cgi/library/');

require_once SILEX_SERVER_DIR.'/cgi/includes/hook_manager.php';
$hook_manager = new hook_manager();
$hook_manager->add_hook('index-head', 'head_hook');
$hook_manager->add_hook('index-noembed', 'noembed_hook');
$hook_manager->add_hook('index-script', 'script_hook');
$hook_manager->add_hook('index-body-end', 'index_body_end_hook');

include (SILEX_SERVER_DIR.'/index.php');
?>
