<?php
/*
Plugin Name: BuddyPress Forums Extras - Quote
Plugin URI: http://wordpress.org/extend/plugins/buddypress-group-forum-extras/
Description: Quote posts on the Group Forums. (based on bbPress Ajaxed Quote). Theme edit required.
Author: rich fuller - rich! @ etiviti
Author URI: http://buddypress.org/developers/nuprn1/
License: GNU GENERAL PUBLIC LICENSE 3.0 http://www.gnu.org/licenses/gpl.txt
Version: 0.3.0
Text Domain: bp-forums-extras
Site Wide Only: true
Network: true
*/

//TODO - kill images in quoted posts (or limit to x amount)

function bp_forum_extras_ajaxquote_setup_globals() {

	//don't waste if we don't care
	if ( is_user_logged_in() && bp_is_group_forum_topic() ) {
		//in the action function below - uncomment the extra style for ACTIVITY STREAM CSS FOR BLOCKQUOTE and move this hook outside the if statement.
		add_action('bp_head','bp_forum_extras_ajaxquote_insert_head');	
		
		wp_enqueue_script( "bp_forum_extras_ajaxquote", BP_FORUM_EXTRAS_URL . "/_inc/js/bp-forums-extras-ajaxquote.js", array( 'jquery' ) );
	}
}
add_action( 'bp_forum_extras_setup_globals', 'bp_forum_extras_ajaxquote_setup_globals',1);


function bp_forum_extras_ajaxquote_process_ajax() {

	$id = (int) $_POST['id'];
	$postlink = $_POST['postlink'];

	if ( $_POST['type'] == 'ajaxquote' )
		bp_forum_extras_ajaxquote_process_ajax_get_post( $id, $postlink );

	die();
}
add_action( 'wp_ajax_bpforums_ajaxquote', 'bp_forum_extras_ajaxquote_process_ajax' );

function bp_forum_extras_ajaxquote_process_ajax_get_post( $postid, $postlink ) {
	global $bp;


	if (!$postid)
		die();

	if ( !is_user_logged_in() )
		die();

	if ( !check_ajax_referer("bp_forum_extras_ajaxquote_". $postid ."_post_quote") )
		die();

	do_action('bp_forum_extras_bbpress_init');

	$post = bb_get_post( $postid );

	if (!$post)
		die();

	$text = preg_replace( '/<blockquote>((.|[\n\r])*?)<\/blockquote>/', '',$post->post_text );

	$text = trim( bb_code_trick_reverse( $text ) ) . "\n";		

	$quoted = bp_core_get_username( $post->poster_id );
	
	printf( "<blockquote><cite>@%s <a href='%s'>%s</a>:</cite>\n%s</blockquote>\n", $quoted, $postlink, __('said', 'bp-forums-extras'), $text );
		
}

function bp_forum_extras_ajaxquote_link() {

	if ( !is_user_logged_in() )
		return;

	?> <a rel="nofollow" href="#ajaxquote-post-<?php bp_the_topic_post_id(); ?>" class="ajaxquote" id="ajaxquote-post-<?php bp_the_topic_post_id(); ?>" title="<?php _e( 'Quote this post', 'bp-forums-extra' ) ?>"><?php _e( 'Quote', 'bp-forums-extra' ); ?></a> |<?php
	echo wp_nonce_field("bp_forum_extras_ajaxquote_". bp_get_the_topic_post_id() ."_post_quote", bp_get_the_topic_post_id() ."_post_nonce", true, false);
	echo '<input type="hidden" name="'. bp_get_the_topic_post_id() .'_post_url" id="'. bp_get_the_topic_post_id() .'_post_url" value="'. bp_forum_extras_ajaxquote_get_post_anchor_link() .'" />';
}
add_action( 'bp_group_forum_post_meta', 'bp_forum_extras_ajaxquote_link', 50 );

	//pre 1.2.5 support
	//add links to the <div class="admin-links"> - theme edit required for this action
	function bp_forum_extras_topic_links() {
		bp_forum_extras_ajaxquote_link();
		echo ' ';
	}
	add_action('bp_forum_extras_topic_links','bp_forum_extras_topic_links');



function bp_forum_extras_ajaxquote_insert_head() {
	echo '<style>.post-content blockquote { width: 75%; padding: 5px; margin: 5px 5px 5px 25px; border: 1px solid #EAEAEA } .post-content blockquote cite { font-weight: bold; } .post-content blockquote img { max-width: 75% }</style>';
//ACTIVITY STREAM CSS FOR BLOCKQUOTE
//echo '<style>.activity-inner blockquote { padding: 5px; margin: 5px 5px 5px 25px; border-bottom: 1px solid #EAEAEA } .activity-inner blockquote cite { font-weight: bold; }</style>';
}


function bp_forum_extras_ajaxquote_get_post_anchor_link() {
	global $topic_template;
	
	$page = $topic_template->pag_page;
	$page = (1 < $page) ? '?topic_page='. $page .'&num='. $topic_template->pag_num : '';

	return bp_get_the_topic_permalink() . $page ."#post-". bp_get_the_topic_post_id();
}

//add admin_menu page
function bp_forum_extras_ajaxquote_add_admin_menu() {
	global $bp;

	if ( !is_site_admin() )
		return false;

	//Add the component's administration tab under the "BuddyPress" menu for site administrators
	require ( dirname( __FILE__ ) . '/bp-forum-extras-ajaxquote-admin.php' );

	add_submenu_page( 'bp-general-settings', __( 'Ajax Quote', 'bp-forums-extras' ), '<span class="bp-forums-extras-admin-menu-item">&middot; ' . __( 'Quotes', 'bp-forums-extras' ) . '</span>', 'manage_options', 'bp-forums-extras-settings-ajaxquote', 'bp_forum_extras_ajaxquote_admin' );

}
//add_action( 'bp_forum_extras_admin_menu', 'bp_forum_extras_ajaxquote_add_admin_menu', 20 );

function bp_forum_extras_ajaxquote_add_admin_screen() {
	global $bp;

	if ( !is_site_admin() )
		return false;

	?>
	<h4>Ajaxed Quote enabled.</h4>
	
	<div class="description"><p>Theme edit is required for this plugin to work (may not work with rich textarea editors - tinymce). A reply textarea on each topic page is required for Ajax Quote to work. Please remove the following if conditional statement from group/single/forum/topic.php<br/><br/>
	
	<strong>Remove:</strong><br/>
	
	<blockquote>
	&lt;?php if ( bp_get_the_topic_is_last_page() ) : ?&gt;
	<br/>
	(don't forget to remove the corresponding endif statement: &lt;?php endif; ?&gt;)
	</blockquote>
	</p>
	<p>If your theme was not updated for 1.2.5 - then an action hook bp_group_forum_post_meta is missing from the topic template file. Please update your theme.</p>
	</div>
	<?php

}
add_action('bp_forum_extras_admin_screen','bp_forum_extras_ajaxquote_add_admin_screen');
?>