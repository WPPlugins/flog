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
 * make the feeds available with url rewrite
 */
function silex_rewrite_rules( $wp_rewrite ) {
  $new_rules = array(
    'feed/(.+)' => 'index.php?feed='.$wp_rewrite->preg_index(1)
  );
  $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
}
/**
 * declare the feeds for listing of the categories, tags, ...
 */
function silex_add_feeds() {
  global $wp_rewrite;
  // add feeds
  add_feed('silex_posts_feed', 'silex_create_posts_feed');
  add_feed('silex_categories_feed', 'silex_create_categories_feed');
  add_feed('silex_tags_feed', 'silex_create_tags_feed');
  add_feed('silex_pages_feed', 'silex_create_pages_feed');
  add_feed('silex_bookmarks_feed', 'silex_create_bookmarks_feed');
  add_feed('silex_paged_feed', 'silex_create_paged_feed');
  // to do : add_feed('silex_archives_feed', 'silex_create_archives_feed');

  // add rewrite rule action
  add_action('generate_rewrite_rules', 'silex_rewrite_rules');
  $wp_rewrite->flush_rules();
}
add_action('init', 'silex_add_feeds');

?>