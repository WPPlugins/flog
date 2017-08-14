<?php 

// build FlashVars
$flashvars_string='';

// list of pages, categories, tags and bookmarks in flashvars
$bookmarks = get_bookmarks();
$tmp_string = '';
foreach ($bookmarks as $bookmark)
	$tmp_string.='<a href="'.$bookmark->link_url.'">'.$bookmark->link_name.'</a><br>';
$flashvars_string.='bookmarks='.urlencode($tmp_string).'&';
//----------------------
$pages = get_pages();
$tmp_string = '';
foreach ($pages as $page){
	$tmp_string.='<a href="javascript:openPage(\''.$page->ID.'\')">'.$page->post_title.'</a><br>';
}
$flashvars_string.='pages='.urlencode($tmp_string).'&';
//----------------------
$categories = get_categories('hide_empty=0');
$tmp_string = '';
foreach ($categories as $cat){
	$tmp_string.='<a href="javascript:openCategory(\'cat='.$cat->cat_ID.'\')">'.$cat->cat_name.'</a><br>';
}
$flashvars_string.='categories='.urlencode($tmp_string).'&';
//----------------------
$tags = get_tags();
$tmp_string = '';
foreach ($tags as $tag){
	$tmp_string.='<a href="javascript:openCategory(\'tag='.$tag->slug.'\')">'.$tag->name.'</a><br>';
}
$flashvars_string.='tags='.urlencode($tmp_string).'&';


// BLOG INFO - see http://codex.wordpress.org/Bloginfo#Parameters
$flashvars_string.='admin_email='.urlencode(get_bloginfo('admin_email')).'&';
$flashvars_string.='atom_url='.urlencode(get_bloginfo('atom_url')).'&';
$flashvars_string.='charset='.urlencode(get_bloginfo('charset')).'&';
$flashvars_string.='comments_atom_url='.urlencode(get_bloginfo('comments_atom_url')).'&';
$flashvars_string.='comments_rss2_url='.urlencode(get_bloginfo('comments_rss2_url')).'&';
$flashvars_string.='description='.urlencode(get_bloginfo('description')).'&';
$flashvars_string.='home='.urlencode(get_bloginfo('home')).'&';
$flashvars_string.='html_type='.urlencode(get_bloginfo('html_type')).'&';
$flashvars_string.='language='.urlencode(get_bloginfo('language')).'&';
$flashvars_string.='name='.urlencode(get_bloginfo('name')).'&';
$flashvars_string.='pingback_url='.urlencode(get_bloginfo('pingback_url')).'&';
$flashvars_string.='rdf_url='.urlencode(get_bloginfo('rdf_url')).'&';
$flashvars_string.='rss2_url='.urlencode(get_bloginfo('rss2_url')).'&';
$flashvars_string.='rss_url='.urlencode(get_bloginfo('rss_url')).'&';
$flashvars_string.='siteurl='.urlencode(get_bloginfo('siteurl')).'&';
$flashvars_string.='stylesheet_directory='.urlencode(get_bloginfo('stylesheet_directory')).'&';
$flashvars_string.='stylesheet_url='.urlencode(get_bloginfo('stylesheet_url')).'&';
$flashvars_string.='template_directory='.urlencode(get_bloginfo('template_directory')).'&';
$flashvars_string.='template_url='.urlencode(get_bloginfo('template_url')).'&';
$flashvars_string.='text_direction='.urlencode(get_bloginfo('text_direction')).'&';
$flashvars_string.='url='.urlencode(get_bloginfo('url')).'&';
$flashvars_string.='version='.urlencode(get_bloginfo('version')).'&';
$flashvars_string.='wpurl='.urlencode(get_bloginfo('wpurl')).'&';

// USER INFO - see http://codex.wordpress.org/Function_Reference/wp_get_current_user
if(is_user_logged_in()){
	global $current_user;
	get_currentuserinfo();
	$flashvars_string.='user_login='.urlencode($current_user->user_login).'&';
	$flashvars_string.='user_level='.urlencode($current_user->user_level).'&';
//	$flashvars_string.='user_ID='.urlencode($current_user->user_ID).'&';
	$flashvars_string.='user_email='.urlencode($current_user->user_url).'&';
//	$flashvars_string.='user_pass_md5='.urlencode($current_user->user_pass_md5).'&';
	$flashvars_string.='display_name='.urlencode($current_user->display_name).'&';
}
/*
// AUTHOR INFO - see http://codex.wordpress.org/Function_Reference/get_the_author
$author = get_the_author();
$flashvars_string.='author_url='.urlencode($author->user_url).'&';
$flashvars_string.='author_registered='.urlencode($author->user_registered).'&';
$flashvars_string.='author_status='.urlencode($author->user_status).'&';
$flashvars_string.='author_description='.urlencode($author->description).'&';
$flashvars_string.='author_nickname='.urlencode($author->nickname).'&';
$flashvars_string.='author_level='.urlencode($author->user_level).'&';
$flashvars_string.='author_ID='.urlencode($author->ID).'&';
*/
/*
// POST INFO - see http://codex.wordpress.org/WPMU_Functions/get_blog_post
$flashvars_string.='post_ID='.urlencode($post->ID).'&';
$flashvars_string.='post_guid='.urlencode($post->guid).'&';
$flashvars_string.='post_title='.urlencode($post->post_title).'&';
$flashvars_string.='post_name='.urlencode($post->post_name).'&';
$flashvars_string.='post_content='.urlencode($post->post_content).'&';
$flashvars_string.='post_excerpt='.urlencode($post->post_excerpt).'&';
$flashvars_string.='post_content_filtered='.urlencode($post->post_content_filtered).'&';
$flashvars_string.='post_type='.urlencode($post->post_type).'&';
$flashvars_string.='tags_input='.urlencode($post->tags_input).'&';
$flashvars_string.='post_status='.urlencode($post->post_status).'&';
$flashvars_string.='post_author='.urlencode($post->post_author).'&';
$flashvars_string.='post_parent='.urlencode($post->post_parent).'&';
$flashvars_string.='post_date='.urlencode($post->post_date).'&';
$flashvars_string.='post_date_gmt='.urlencode($post->post_date_gmt).'&';
$flashvars_string.='post_modified='.urlencode($post->post_modified).'&';
$flashvars_string.='post_modified_gmt='.urlencode($post->post_modified_gmt).'&';
$flashvars_string.='post_ping_status='.urlencode($post->ping_status).'&';
$flashvars_string.='post_to_ping='.urlencode($post->to_ping).'&';
$flashvars_string.='post_pinged='.urlencode($post->pinged).'&';
$flashvars_string.='post_menu_order='.urlencode($post->menu_order).'&';
$flashvars_string.='post_mime_type='.urlencode($post->post_mime_type).'&';
$flashvars_string.='post_comment_count='.urlencode($post->comment_count).'&';
// ! post_category is an ARRAY !
$flashvars_string.='post_category='.urlencode($post->post_category).'&';
*/
?>