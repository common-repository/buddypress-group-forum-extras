<?php
/*
Plugin Name: BuddyPress Group Forum Extras
Plugin URI: http://wordpress.org/extend/plugins/buddypress-group-forum-extras/
Description: A wrapper plugin (required) to enable sub-plugins for Group Forums.
Author: rich fuller - rich! @ etiviti
Author URI: http://buddypress.org/developers/nuprn1/
License: GNU GENERAL PUBLIC LICENSE 3.0 http://www.gnu.org/licenses/gpl.txt
Version: 0.3.0
Text Domain: bp-forums-extras
Site Wide Only: true
Network: true
*/

if ( !defined( 'BP_FORUM_EXTRAS_URL' ) )
	define( 'BP_FORUM_EXTRAS_URL', WP_PLUGIN_URL .'/'. basename( dirname( __FILE__ ) ) );

//roll up our functions into another file
require ( dirname( __FILE__ ) . '/includes/bp-forum-extras-functions.php' );

//bread and butter for all the funky bbpress hook checks
function bp_forum_extras_bbpress_init() {
	global $bbdb, $bb_roles, $wp_roles;

	if ( is_object( $bbdb ) && is_object( $bb_roles ) ) {
		return;
	}
	if ( is_object( $bbdb ) && is_user_logged_in() ) {
		$bb_roles =& $wp_roles;
		bb_init_roles( $bb_roles );
		return;
	}

	do_action( 'bbpress_init');

}
add_action('bp_forum_extras_bbpress_init','bp_forum_extras_bbpress_init');

function bp_forum_extras_setup_globals() {

	//since we are cheating, we need to know if bp is deactivated and kill it.
	if ( !defined( 'BP_VERSION' ) )
		return;

	//don't care to load on these pages - we don't do anything and plus the bbpress_init causes function redeclaration fatal errors.
	if ( bp_is_register_page() || bp_is_activation_page() || bp_is_user_blogs() )
		return;

	if ( bp_forum_extras_blogs_component() )
		return;

	do_action('bp_forum_extras_bbpress_init');
	
	add_filter( 'bp_forums_allowed_tags', 'bp_forum_extras_allowed_tags', 1, 1 );
	
	do_action('bp_forum_extras_setup_globals');
	
}
if (defined('WP_ADMIN') && WP_ADMIN) {
	add_action( 'admin_init', 'bp_forum_extras_setup_globals', 500 );
} else {
	//add_action( 'bp_init', 'bp_forum_extras_setup_globals', 100 );

	//lets try and delay until after after after everything due to conflicts with other plugins and bbpress
	add_action( 'init', 'bp_forum_extras_setup_globals', 1000 );

}


function bp_forum_extras_after_post_content_li() {
	do_action('bp_forum_extras_add_after_post_content_li');
}
add_action('bp_forum_extras_after_post_content_li','bp_forum_extras_after_post_content_li');

/**
 * Admin menus action
 */
function bp_forum_extras_add_admin_menu() {
	global $bp;

	if ( !is_site_admin() )
		return false;

	//Add the component's administration tab under the "BuddyPress" menu for site administrators
	require ( dirname( __FILE__ ) . '/includes/admin/bp-forum-extras-admin.php' );

	add_submenu_page( 'bp-general-settings', __( 'Forum Extras', 'bp-forums-extras' ), '<span class="bp-forums-extras-admin-menu-header">' . __( 'Forum Extras', 'bp-forums-extras' ) . '&nbsp;&nbsp;&nbsp;</span>', 'manage_options', 'bp-forums-extras-settings', 'bp_forum_extras_admin' );

	do_action('bp_forum_extras_admin_menu');

}

if ( defined( 'BP_VERSION' ) ) {
	add_action( 'admin_menu', 'bp_forum_extras_init' );
} else {
	add_action( 'bp_init', 'bp_forum_extras_init');
}

function bp_forum_extras_init() {
	add_action( 'admin_menu', 'bp_forum_extras_add_admin_menu', 25 );
}


?>
