<?php
/*
Plugin Name: BuddyPress Forums Extras - BBCode to ShortCodes Filter
Plugin URI: http://wordpress.org/extend/plugins/buddypress-group-forum-extras/
Description: Add WP-Shortcode filter support on group forum postings. (requires a BBCode Shortcode plugin) NOT recommend for external bbPress installs - do not enable this and BBCode to HTML (no ShortCodes) at the same time.
Author: rich fuller - rich! @ etiviti
Author URI: http://buddypress.org/developers/nuprn1/
License: GNU GENERAL PUBLIC LICENSE 3.0 http://www.gnu.org/licenses/gpl.txt
Version: 0.3.0
Text Domain: bp-forums-extras
Site Wide Only: true
Network: true
*/

//TODO
//create own shortcodes, use bbcode buttons

function bp_forum_extras_shortcode_setup_globals() {
	
	add_filter( 'bp_get_the_topic_post_content', 'do_shortcode' );

	if ( is_user_logged_in() && ( bp_is_group_forum() || bp_forum_extras_forumsdirectory_component() ) && bb_get_option( 'bp_sc_bbcode_buttons') ) {

		require_once( dirname( __FILE__ ) . '/includes/bp-forum-extras-bbcode-buttons.php' );

		add_action('bp_group_after_edit_forum_topic','bp_forum_extras_bbcode_buttons',11);
		add_action('bp_group_after_edit_forum_post','bp_forum_extras_bbcode_buttons',11);
		add_action('groups_forum_new_reply_after','bp_forum_extras_bbcode_buttons',11);
		add_action('groups_forum_new_topic_after','bp_forum_extras_bbcode_buttons',11);
		add_action('bp_after_group_forum_post_new','bp_forum_extras_bbcode_buttons',11);

		add_action('bp_head','bp_forum_extras_bbcode_insert_head');
	}

}
add_action( 'bp_forum_extras_setup_globals', 'bp_forum_extras_shortcode_setup_globals',1);




//add admin_menu page
function bp_forum_extras_shortcode_add_admin_menu() {
	global $bp;

	if ( !is_site_admin() )
		return false;

	//Add the component's administration tab under the "BuddyPress" menu for site administrators
	require ( dirname( __FILE__ ) . '/includes/admin/bp-forum-extras-shortcodes-admin.php' );

	add_submenu_page( 'bp-general-settings', __( 'Shortcode Filter', 'bp-forums-extras' ), '<span class="bp-forums-extras-admin-menu-item">&middot; ' . __( 'Shortcode Filter', 'bp-forums-extras' ) . '</span>', 'manage_options', 'bp-forums-extras-settings-shortcodes', 'bp_forum_extras_shortcode_admin' );

}
add_action( 'bp_forum_extras_admin_menu', 'bp_forum_extras_shortcode_add_admin_menu', 20 );

function bp_forum_extras_shortcode_add_admin_screen() {
	global $bp;

	if ( !is_site_admin() )
		return false;

	?>
	<h4>ShortCode enabled</h4>
	<div class="description">
		<p>For BBCode use - install a shortcode plugin <a href="http://wordpress.org/extend/plugins/boingball-bbcode/">boingball-bbcode</a> or <a href="http://wordpress.org/extend/plugins/bbcode/">Viper BBCode</a> (does not support full range of bbcodes)</p>
		<p>Please Note: If you use an external bbPress install - your bbPress installation outside of BuddyPress will not filter the same shortcodes. Instead Enable: <strong>BuddyPress Forums Extras - BBCode to HTML (no ShortCodes)</strong> which will convert bbcode to html prior to database updates.</p>
		<p>Enabling a BBCode Shortcode plugin - an indirect benefit is the use of bbcode syntax on activity stream updates/commenting as BuddyPress already applies the wp-shortcode filter</p>
		<p>You may enable _ck_'s bbcode buttons via the <a href="<?php echo site_url() . '/wp-admin/admin.php?page=bp-forums-extras-settings-shortcodes' ?>">Shortcodes Settings</a> page but this might conflict with other textarea editors installed (tinymce, markitup) on the forum textareas.</p>
	</div>
	<?php if ( function_exists('bp_forum_extras_bbcode_setup_globals') ) { ?>
		<div id="message" class="error">
			<strong>BuddyPress Forums Extras - BBCode to HTML (no ShortCodes)</strong> is enabled - this may cause unpredictable results when using bbcode shortcodes in tandem.
		</div>
	<?php } ?>
	
	<?php
}
add_action('bp_forum_extras_admin_screen','bp_forum_extras_shortcode_add_admin_screen');

?>