<?php
/*
Plugin Name: BuddyPress Forums Extras - Topic First Post Preview
Plugin URI: http://wordpress.org/extend/plugins/buddypress-group-forum-extras/
Description: Preview the first post of a topic, inserts first post below topic title (mouseover - hoverintent) - no ajax.
Author: rich fuller - rich! @ etiviti
Author URI: http://buddypress.org/developers/nuprn1/
License: GNU GENERAL PUBLIC LICENSE 3.0 http://www.gnu.org/licenses/gpl.txt
Version: 0.3.0
Text Domain: bp-forums-extras
Site Wide Only: true
Network: true
*/

function bp_forum_extras_preview_setup_globals() {
	global $fe_options;

	//don't waste if we don't care
	if ( is_user_logged_in() && bp_is_group_forum() && !bp_is_group_forum_topic() ) {
		add_action( 'bp_directory_forums_extra_row', 'bp_forum_extras_preview_screen',1);
		add_action('bp_head','bp_forum_extras_preview_insert_head');
				
		wp_enqueue_script('hoverIntent', '/'. WPINC .'/js/hoverIntent.js', array('jquery'));
		wp_enqueue_script( "bp_forum_extras_preview", BP_FORUM_EXTRAS_URL . "/_inc/js/bp-forums-extras-preview.js", array( 'jquery','hoverIntent' ) );	
		
		$fe_options = get_option( 'bp_forums_extras_preview');
		if (!$fe_options) {
			$fe_options['excerpt'] = false;
			$fe_options['excerpt_length'] = 400;
		}
		
	}
}
add_action( 'bp_forum_extras_setup_globals', 'bp_forum_extras_preview_setup_globals',1);


function bp_forum_extras_preview_screen() {
	global $forum_template;
	
	echo '<div class="topic-preview" id="'. bp_get_the_topic_slug() .'" style="display:none"><div class="poster-meta">'. bp_get_the_topic_poster_avatar( "width=40&height=40" ) .' '. sprintf( __( "%s said %s ago:", "buddypress" ), bp_get_the_topic_poster_name(), bp_get_the_topic_time_since_created() ) .'</div><div class="post-content">'. bp_forum_extras_get_preview_post_content() .'</div></div>';
}

function bp_forum_extras_preview_insert_head() {
	echo '<style>.topic-preview { visibility:hidden; } </style>';
	echo '<style>.preview-first-post { border-bottom:1px solid #EAEAEA; } .preview-first-post .poster-meta { text-align: left; color:#888888; margin-bottom:10px; } td.preview-first-post .post-content { overflow: auto; max-height: 400px; text-align: left; }</style>';
}

function bp_forum_extras_get_preview_post_content() {
	global $forum_template, $fe_options;

	if ( $fe_options['excerpt'] ) {
		return bp_forum_extras_get_post_excerpt( 'length='. $fe_options['excerpt_length'] );
	} else {
		return apply_filters( 'bp_get_the_topic_post_content', bp_forum_extras_get_the_topic_text() );
	}

}

function bp_forum_extras_get_post_excerpt( $args = '' ) {
	global $forum_template;

	$defaults = array(
		'length' => 255
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	$post = bp_create_excerpt( bp_forum_extras_get_the_topic_text(), $length );
	return apply_filters( 'bp_get_the_topic_latest_post_excerpt', $post );
}


function bp_forum_extras_get_the_topic_text() {
	global $forum_template;

	$post = bb_get_first_post( (int)$forum_template->topic->topic_id, false );
	return $post->post_text;
}


//add admin_menu page
function bp_forum_extras_preview_add_admin_menu() {
	global $bp;

	if ( !is_site_admin() )
		return false;

	//Add the component's administration tab under the "BuddyPress" menu for site administrators
	require ( dirname( __FILE__ ) . '/includes/admin/bp-forum-extras-preview-admin.php' );

	add_submenu_page( 'bp-general-settings', __( 'Topic Preview', 'bp-forums-extras' ), '<span class="bp-forums-extras-admin-menu-item">&middot; ' . __( 'Topic Preview', 'bp-forums-extras' ) . '</span>', 'manage_options', 'bp-forums-extras-settings-preview', 'bp_forum_extras_preview_admin' );

}
add_action( 'bp_forum_extras_admin_menu', 'bp_forum_extras_preview_add_admin_menu', 20 );

function bp_forum_extras_preview_add_admin_screen() {
	global $bp;

	if ( !is_site_admin() )
		return false;

	?>
	<h4>Topic Preview enabled</h4>
	<div class="description">Preview the topic first post with a jQuery hover pop-up div (uses hoverIntent) - no ajax. <p>Tip: Use excerpt option if you use shortcodes - this will remove the shortcode from the preview.</p></div>
	<?php
}
add_action('bp_forum_extras_admin_screen','bp_forum_extras_preview_add_admin_screen');

?>