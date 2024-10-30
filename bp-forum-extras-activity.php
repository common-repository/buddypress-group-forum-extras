<?php
/*
Plugin Name: BuddyPress Forums Extras - View Activity Comments on Forum Posts
Plugin URI: http://wordpress.org/extend/plugins/buddypress-group-forum-extras/
Description: Display Activity Reply/Comments underneath the related Forum Post (type of new_forum_post)
Author: rich fuller - etiviti (rich!)
Author URI: http://buddypress.org/developers/nuprn1/
License: GNU GENERAL PUBLIC LICENSE 3.0 http://www.gnu.org/licenses/gpl.txt
Version: 0.3.0
Text Domain: bp-forums-extras
Site Wide Only: true
Network: true
*/

//TODO - move comment insertion to ajax and dom

function bp_forum_extras_activity_setup_globals() {

	//don't waste if we don't care
	if ( ( !(int)get_site_option( 'bp-disable-blogforum-comments' ) || false === get_site_option( 'bp-disable-blogforum-comments' ) ) && bp_is_group_forum_topic() && bp_is_active( 'activity' ) ) {
		add_action('wp_print_scripts','bp_forum_extras_activity_insert_head');
		//wp_enqueue_script( "bp_forum_extras_activity", BP_FORUM_EXTRAS_URL . "/_inc/js/bp-forums-extras-activity.js", array( 'jquery' ) );
	}

}
add_action( 'bp_forum_extras_setup_globals', 'bp_forum_extras_activity_setup_globals',1);

function bp_forum_extras_activity_add_post_screen( $args = '' ) {
	global $bp, $activities_template;

	if ( !bp_is_active( 'activity' ) )
		return;

	if ( $activities_template->disable_blogforum_replies )
		return;

	$show_hidden = false;

	/* Group filtering */
	$object = $bp->groups->id;
	$primary_id = $bp->groups->current_group->id;

	if ( 'public' != $bp->groups->current_group->status && groups_is_user_member( $bp->loggedin_user->id, $bp->groups->current_group->id ) )
		$show_hidden = true;

	/* Note: any params used for filtering can be a single value, or multiple values comma separated. */
	$defaults = array(
		'display_comments' => 'threaded', // false for none, stream/threaded - show comments in the stream or threaded under items
		'sort' => 'DESC', // sort DESC or ASC
		'page' => 1, // which page to load
		'per_page' => false, // number of items per page
		'max' => false, // max number to return
		'include' => false, // pass an activity_id or string of ID's comma separated
		'show_hidden' => $show_hidden, // Show activity items that are hidden site-wide?

		/* Filtering */
		'object' => $object, // object to filter on e.g. groups, profile, status, friends
		'primary_id' => $primary_id, // object ID to filter on e.g. a group_id or forum_id or blog_id etc.
		'action' => 'new_forum_post', // action to filter on e.g. activity_update, new_forum_post, profile_updated
		'secondary_id' => bp_get_the_topic_post_id(), // secondary object ID to filter on e.g. a post_id

		/* Searching */
		'search_terms' => false // specify terms to search on
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r );

	$filter = array( 'user_id' => false, 'object' => $object, 'action' => $action, 'primary_id' => $primary_id, 'secondary_id' => $secondary_id );

	$activities_template = new BP_Activity_Template( $page, $per_page, $max, $include, $sort, $filter, $search_terms, $display_comments, $show_hidden );
?>
	<?php while ( bp_activities() ) : bp_the_activity();
		if ( bp_forum_extras_activity_has_children() ) {?>
			<li id="activity-<?php bp_the_topic_post_id() ?>" class="forum-post-activity">
				<div class="activity">
				<ul id="activity-stream-post-<?php bp_the_topic_post_id() ?>" class="activity-list item-list">
		
					<li class="<?php bp_activity_css_class() ?>" id="activity-<?php bp_activity_id() ?>">
						<div class="activity-content">
							<div class="activity-meta">
								<a href="<?php echo $bp->root_domain . '/' . BP_ACTIVITY_SLUG . '/p/' . bp_get_activity_id() . '/' ?>" rel="nofollow" class="view-activity" id="view-activity-<?php bp_activity_id() ?>"><?php _e( 'Activity replies for this post', 'bp-forums-extras' ) ?> (<span><?php bp_activity_comment_count() ?></span>)</a>

								<?php if ( is_user_logged_in() ) : ?>
									<?php if ( !bp_get_activity_is_favorite() ) : ?>
										<a rel="nofollow" href="<?php bp_activity_favorite_link() ?>" class="fav" title="<?php _e( 'Mark as Favorite', 'buddypress' ) ?>"><?php _e( 'Favorite', 'buddypress' ) ?></a>
									<?php else : ?>
										<a rel="nofollow" href="<?php bp_activity_unfavorite_link() ?>" class="unfav" title="<?php _e( 'Remove Favorite', 'buddypress' ) ?>"><?php _e( 'Remove Favorite', 'buddypress' ) ?></a>
									<?php endif; ?>
								<?php endif;?>
								
								<?php do_action( 'bp_activity_entry_meta' ) ?>

							</div>
						</div>
						<div class="activity-comments" id="view-activity-comment-<?php bp_activity_id() ?>">
							<?php bp_forum_extras_activity_comments(); ?>
						</div>
					</li>
				</ul>
				</div>
			</li>
		<?php } ?>
	<?php endwhile; ?>

<?php
}
add_action( 'bp_forum_extras_add_after_post_content_li', 'bp_forum_extras_activity_add_post_screen', 100 );

function bp_forum_extras_activity_insert_head() {
	echo '<style>.activity li { border-bottom: none ! important; } .activity .alt { background:none ! important; } .activity .activity-content { margin: 0px 0px 0px 0px ! important; } .activity .groups { padding: 0px 0px 0px 0px ! important; } div.activity-meta { margin: 0px 0px 3px 0px ! important; } div.activity-comments { margin: 10px 0px 0px 0px ! important; } </style>';
}

function bp_forum_extras_activity_comments( $args = '' ) {
	echo bp_forum_extras_activity_get_comments( $args );
}
	function bp_forum_extras_activity_get_comments( $args = '' ) {
		global $activities_template, $bp;

		if ( !$activities_template->activity->children )
			return false;

		$comments_html = bp_forum_extras_activity_recurse_comments( $activities_template->activity );

		return apply_filters( 'bp_activity_get_comments', $comments_html );
	}
		/* TODO: The HTML in this function is temporary and will be moved to the template in a future version. */
		function bp_forum_extras_activity_recurse_comments( $comment ) {
			global $activities_template, $bp;

			if ( !$comment->children )
				return false;

			$content .= '<ul>';
			foreach ( (array)$comment->children as $comment ) {
				if ( !$comment->user_fullname )
					$comment->user_fullname = $comment->display_name;

				$content .= '<li id="acomment-' . $comment->id . '">';
				$content .= '<div class="acomment-avatar"><a href="' . bp_core_get_user_domain( $comment->user_id, $comment->user_nicename, $comment->user_login ) . '">' . bp_core_fetch_avatar( array( 'item_id' => $comment->user_id, 'width' => 25, 'height' => 25, 'email' => $comment->user_email ) ) . '</a></div>';
				$content .= '<div class="acomment-meta"><a href="' . bp_core_get_user_domain( $comment->user_id, $comment->user_nicename, $comment->user_login ) . '">' . apply_filters( 'bp_acomment_name', $comment->user_fullname, $comment ) . '</a> &middot; ' . sprintf( __( '%s ago', 'buddypress' ), bp_core_time_since( $comment->date_recorded ) );

				$content .= '</div>';
				$content .= '<div class="acomment-content">' . apply_filters( 'bp_get_activity_content', $comment->content ) . '</div>';

				$content .= bp_forum_extras_activity_recurse_comments( $comment );
				$content .= '</li>';
			}
			$content .= '</ul>';

			return apply_filters( 'bp_activity_recurse_comments', $content );
		}

//add admin_menu page
function bp_forum_extras_activity_add_admin_menu() {
	global $bp;

	if ( !is_site_admin() )
		return false;

	//Add the component's administration tab under the "BuddyPress" menu for site administrators
	require ( dirname( __FILE__ ) . '/bp-forum-extras-activity-admin.php' );

	add_submenu_page( 'bp-general-settings', __( 'Activity on Posts', 'bp-forums-extras' ), '<span class="bp-forums-extras-admin-menu-item">&middot; ' . __( 'Activity', 'bp-forums-extras' ) . '</span>', 'manage_options', 'bp-forums-extras-settings-activity', 'bp_forum_extras_activity_admin' );

}
//add_action( 'bp_forum_extras_admin_menu', 'bp_forum_extras_activity_add_admin_menu', 20 );

function bp_forum_extras_activity_add_admin_screen() {
	global $bp;

	if ( !is_site_admin() )
		return false;

	?>
	<h4>View Activity Comments on Forum Posts enabled.</h4>
	
	<div class="description"><p>Theme edit is required for this plugin to work. Please edit the child/theme file: groups/single/forum/topic.php<br/><br/>
	
	<strong>Replace:</strong><br/>
	<blockquote>
				&lt;/li&gt;

			&lt;?php endwhile; ?&gt;
		&lt;/ul&gt;&lt;!-- #topic-post-list --&gt;
	</blockquote>
	<strong>With:</strong><br/>
	<blockquote>
				&lt;/li&gt;

				&lt;?php do_action( 'bp_forum_extras_add_after_post_content_li' ); ?&gt;

			&lt;?php endwhile; ?&gt;
						
		&lt;/ul&gt;&lt;!-- #topic-post-list --&gt;
	</blockquote>
	</p>
	</div>
	<?php

}
add_action('bp_forum_extras_admin_screen','bp_forum_extras_activity_add_admin_screen');
?>