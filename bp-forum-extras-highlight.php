<?php
/*
Plugin Name: BuddyPress Forums Extras - Add CSS classes for Posts/Topics per user level
Plugin URI: http://wordpress.org/extend/plugins/buddypress-group-forum-extras/
Description: Highlight forum topics and posts with css class at the user level for group admin/mod, topic author, friend, following (warning no wp_cache yet)
Author: rich fuller - rich! @ etiviti
Author URI: http://buddypress.org/developers/nuprn1/
License: GNU GENERAL PUBLIC LICENSE 3.0 http://www.gnu.org/licenses/gpl.txt
Version: 0.3.0
Text Domain: bp-forums-extras
Site Wide Only: true
Network: true
*/

//wp_cache check for - since multiples could be for that one page

function bp_forum_extras_highlight_topic_post( $class ) {
	global $bp, $topic_template;
	
	if ( !is_user_logged_in() )
		return $class;
	
	//don't care about the first post
	if ( bp_forum_extras_is_first_post() )
		return $class;

	if ( groups_is_user_admin( $topic_template->post->poster_id, $bp->groups->current_group->id ) )
		return $class . ' highlightpost-admin';

	if ( groups_is_user_mod( $topic_template->post->poster_id, $bp->groups->current_group->id ) )
		return $class . ' highlightpost-mod';

	if ( groups_is_user_banned( $topic_template->post->poster_id, $bp->groups->current_group->id ) )
		return $class . ' highlightpost-banned';

	if ( bp_is_active('friends') && !bp_get_the_topic_post_is_mine() ) {
		if ( friends_check_friendship_status( $bp->loggedin_user->id, $topic_template->post->poster_id ) == 'is_friend' )
			return $class . ' highlightpost-friend';
	}
	
	if ( function_exists('bp_follow_is_following') && !bp_get_the_topic_post_is_mine() ) {
		if ( bp_follow_is_following( array( 'leader_id' => $topic_template->post->poster_id, 'follower_id' => $bp->loggedin_user->id ) ) )
			return $class . ' highlightpost-follow';
	}
	
	if ( bp_get_the_topic_post_is_mine() )
		return $class .' highlightpost-mine';

	return $class;

}
add_filter( 'bp_get_the_topic_post_css_class', 'bp_forum_extras_highlight_topic_post' );



function bp_forum_extras_highlight_topic( $class ) {
	global $bp, $forum_template;
	
	if ( !is_user_logged_in() )
		return $class;

	if ( groups_is_user_admin( $forum_template->topic->topic_poster, $bp->groups->current_group->id ) )
		return $class . ' highlighttopic-admin';

	if ( groups_is_user_mod( $forum_template->topic->topic_poster, $bp->groups->current_group->id ) )
		return $class . ' highlighttopic-mod';

	if ( groups_is_user_banned( $forum_template->topic->topic_poster, $bp->groups->current_group->id ) )
		return $class . ' highlighttopic-banned';

	if ( bp_is_active('friends') && !bp_get_the_topic_is_mine() ) {
		if ( friends_check_friendship_status( $bp->loggedin_user->id, $forum_template->topic->topic_poster ) == 'is_friend' )
			return $class . ' highlighttopic-friend';
	}
	
	if ( function_exists('bp_follow_is_following') && !bp_get_the_topic_is_mine() ) {
		if ( bp_follow_is_following( array( 'leader_id' => $forum_template->topic->topic_poster, 'follower_id' => $bp->loggedin_user->id ) ) )
			return $class . ' highlighttopic-follow';
	}
	
	if ( bp_get_the_topic_is_mine() )
		return $class . ' highlighttopic-mine';

	return $class;

}
add_filter( 'bp_get_the_topic_css_class', 'bp_forum_extras_highlight_topic' );


function bp_forum_extras_highlight_add_admin_screen() {
	global $bp;

	if ( !is_site_admin() )
		return false;

	?>
	<h4>Highlight (css) forum topics and posts enabled.</h4>
	<div class="description">
		<p>You will need to add css definitions to your child/theme.<br/>
		<blockquote>
		.highlightpost-admin .highlighttopic-admin .highlightpost-mod .highlighttopic-mod .highlightpost-banned .highlighttopic-banned .highlightpost-friend .highlighttopic-friend .highlightpost-follow .highlighttopic-follow .highlightpost-mine .highlighttopic-mine
		</blockquote>
		</p>
		<p><strong>Warning</strong>: Some bp-core functions do not have a cache set/check against them. (groups_is_user_admin, groups_is_user_mod, groups_is_user_banned) - this may decrease performance.</p>
	</div>
	<?php

}
add_action('bp_forum_extras_admin_screen','bp_forum_extras_highlight_add_admin_screen');
?>