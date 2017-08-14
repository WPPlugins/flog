<?php
/**
 * NOT USED IN SILEX TEMPLATE?
 */

// Do not delete these lines
	if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die ('Please do not load this page directly. Thanks!');

	if ( post_password_required() ) { ?>
		<p class="nocomments">This post is password protected. Enter the password to view comments.</p>
	<?php
		return;
	}
function mytheme_comment($comment, $args, $depth) {
   $GLOBALS['comment'] = $comment; ?>
	 <?php printf(__('<cite class="fn">%s</cite> <span class="says">says:</span>'), get_comment_author_link()) ?>
      <?php if ($comment->comment_approved == '0') : ?>
         <em><?php _e('Your comment is awaiting moderation.') ?></em>
         <br />
      <?php endif; ?>
      <?php comment_text() ?>
     </div>
<?php
        }
?> 

<!-- You can start editing here. -->
<?php wp_list_comments('type=comment&callback=mytheme_comment'); ?>
