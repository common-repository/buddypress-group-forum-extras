<?php
/*
Plugin Name: BuddyPress Forums Extras - BBCode to HTML (no ShortCodes)
Plugin URI: http://wordpress.org/extend/plugins/buddypress-group-forum-extras/
Description: Add BBCode to html support on group forum postings. (based on _ck_ bbcodelite) Converts BBCode syntax to html prior to database update.
Author: rich fuller - rich! @ etiviti
Author URI: http://buddypress.org/developers/nuprn1/
License: GNU GENERAL PUBLIC LICENSE 3.0 http://www.gnu.org/licenses/gpl.txt
Version: 0.3.0
Text Domain: bp-forums-extras
Site Wide Only: true
Network: true
*/

//TODO - hook bbcode_it to other filters as an option

function bp_forum_extras_bbcode_setup_globals() {

	add_filter('group_forum_topic_text_before_save', 'bp_forum_extras_bbcode_it',7, 1);
	add_filter('group_forum_post_text_before_save', 'bp_forum_extras_bbcode_it',7, 1);
	add_filter('bp_forum_extras_signatures_text_before_save', 'bp_forum_extras_bbcode_it', 1);

	if ( is_user_logged_in() && ( bp_is_group_forum() || bp_forum_extras_forumsdirectory_component() ) && bb_get_option( 'bp_bbcode_buttons') ) {

		require_once( dirname( __FILE__ ) . '/includes/bp-forum-extras-bbcode-buttons.php' );

		add_action('bp_group_after_edit_forum_topic','bp_forum_extras_bbcode_buttons',11);
		add_action('bp_group_after_edit_forum_post','bp_forum_extras_bbcode_buttons',11);
		add_action('groups_forum_new_reply_after','bp_forum_extras_bbcode_buttons',11);
		add_action('groups_forum_new_topic_after','bp_forum_extras_bbcode_buttons',11);
		add_action('bp_after_group_forum_post_new','bp_forum_extras_bbcode_buttons',11);

		add_action('bp_head','bp_forum_extras_bbcode_insert_head');
	}

}
add_action( 'bp_forum_extras_setup_globals', 'bp_forum_extras_bbcode_setup_globals',1);

function bp_forum_extras_bbcode_it($text) {
	$bbcode_lite = array();
	
	$bbcode_lite['complex']['url'] = array('a','href');
	$bbcode_lite['complex']['img'] = array('img','src');
	$bbcode_lite['wrap'] = array('color' => array('font','color'),'size' => array('font','size'),'url' => array('a','href'), 'list' => array('ol','type'));	
	$bbcode_lite['simple'] = array('pre'=>'pre','b' => 'strong','i' => 'em','u' => 'u','center'=>'center','quote' => 'blockquote','strike' => 'strike','s' => 'strike','list' => 'ul', 'code' => 'code');
	
	$counter=0;  // filter out all backtick code first

	if ( preg_match_all("|\<code\>(.*?)\<\/code\>|sim", $text, $backticks) ) {
		foreach ($backticks[0] as $backtick) {
			++$counter;
			$text = str_replace($backtick,"_bbcode_lite_".$counter."_",$text);
		}
	}

	$text=preg_replace('/(\<br \/\>|[\s])*?\[(\*|li)\](.+?)(\<br \/\>|[\s])*?(\[\/(\*|li)\](\<br \/\>|[\s])*?|(?=(\[(\*|li)\](\<br \/\>|[\s])*?|\[\/list\])))/sim','<li>$3</li>',$text); // * = li, a very special case since they may not be closed

	foreach ($bbcode_lite['wrap'] as $bbcode=>$html) {
		$text = preg_replace('/\['.$bbcode.'=(.+?)\](.+?)\[\/'.$bbcode.'\]/is','<'.$html[0].' '.$html[1].'="$1">$2</'.$html[0].'>',$text);
	}

	foreach ($bbcode_lite['simple'] as $bbcode=>$html){
		$text = preg_replace('/\['.$bbcode.'\](.+?)\[\/'.$bbcode.'\]/is','<'.$html.'>$1</'.$html.'>',$text);
	}

	foreach ($bbcode_lite['complex'] as $bbcode=>$html) {
		if ( $bbcode!='url' ) {
			$text = preg_replace('/\['.$bbcode.'\](.+?)\[\/'.$bbcode.'\]/is','<'.$html[0].' '.$html[1].'="$1" />',$text);
		} else {
			$text = preg_replace('/\['.$bbcode.'\](.+?)\[\/'.$bbcode.'\]/is','<'.$html[0].' '.$html[1].'="$1">$1</'.$html[0].'>',$text);
		}
	}

	if ($counter) {
		$counter=0;
		foreach ($backticks[0] as $backtick) {
			++$counter;
			$text=str_replace("_bbcode_lite_".$counter."_",$backtick,$text);
		}
	}	// undo backticks

	return $text;
}







//add admin_menu page
function bp_forum_extras_bbcode_add_admin_menu() {
	global $bp;

	if ( !is_site_admin() )
		return false;

	//Add the component's administration tab under the "BuddyPress" menu for site administrators
	require ( dirname( __FILE__ ) . '/includes/admin/bp-forum-extras-bbcode-admin.php' );

	add_submenu_page( 'bp-general-settings', __( 'BBCode', 'bp-forums-extras' ), '<span class="bp-forums-extras-admin-menu-item">&middot; ' . __( 'BBCode', 'bp-forums-extras' ) . '</span>', 'manage_options', 'bp-forums-extras-settings-bbcode', 'bp_forum_extras_bbcode_admin' );

}
add_action( 'bp_forum_extras_admin_menu', 'bp_forum_extras_bbcode_add_admin_menu', 20 );

function bp_forum_extras_bbcode_add_admin_screen() {
	global $bp;

	if ( !is_site_admin() )
		return false;

	?>
	<h4>BBCode enabled.</h4>
	<div class="description">This plugin will convert bbcode to html prior to database updates and is recommend for external bbPress installs. <p>You may enable _ck_'s bbcode buttons via the <a href="<?php echo site_url() . '/wp-admin/admin.php?page=bp-forums-extras-settings-bbcode'; ?>">BBCode Settings</a> page but this might conflict with other textarea editors installed (tinymce, markitup) on the forum textareas.</p></div>
	<?php

}
add_action('bp_forum_extras_admin_screen','bp_forum_extras_bbcode_add_admin_screen');

?>