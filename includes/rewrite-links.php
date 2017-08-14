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
//http://codex.wordpress.org/Plugin_API/Filter_Reference#Link_Filters 

 
/*
 * rewrite link
 * UNUSED ?
 */
function silex_post_link($link) {
	global $post;
	$link = 'javascript:parent.openPost(\''.$post->ID.'\')';
	return $link;
}
/*
 * rewrite link
 * UNUSED ?
 */
function silex_category_link($linkUrl,$categoryId) {
//	global $post;
	$link = 'javascript:parent.openCategory(\''.$categoryId.'\')';
	return $link;
}
if(is_framed()){
	add_filter('the_permalink', 'silex_post_link',10,1);
	add_filter('category_link', 'silex_category_link',10,2);
}
?>