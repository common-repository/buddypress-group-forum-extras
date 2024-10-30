<?php
/*
Plugin Name: BuddyPress Forums Extras - Signatures
Plugin URI: http://wordpress.org/extend/plugins/buddypress-group-forum-extras/
Description: Add user signatures to group forums postings and xprofile page. (based on _ck_ bb-signatures)
Author: rich fuller - rich! @ etiviti
Author URI: http://buddypress.org/developers/nuprn1/
License: GNU GENERAL PUBLIC LICENSE 3.0 http://www.gnu.org/licenses/gpl.txt
Version: 0.3.0
Text Domain: bp-forums-extras
Site Wide Only: true
Network: true
*/

function bp_forum_extras_signatures_setup_globals() {
	
	add_action('bp_head','bp_forum_extras_signatures_insert_head');
	
	add_filter('bp_get_the_topic_post_content', 'bp_forum_extras_signatures_add_signature_to_post', 5);
	add_action('bp_profile_header_meta', 'bp_forum_extras_signatures_add_signature_to_profile', 1000);
	
}
add_action( 'bp_forum_extras_setup_globals', 'bp_forum_extras_signatures_setup_globals',2);



function bp_forum_extras_signatures_init() {
	global $bb_signatures;
	
	if ( isset($bb_signatures) && !empty($bb_signatures) )
		return;
	
	$bb_signatures = bb_get_option( 'bb_signatures' );

	if ( !$bb_signatures ) { //just in case - set the defaults
		$bb_signatures['max_length'] = 300;
		$bb_signatures['max_lines'] = 3;
		$bb_signatures['on_profile'] = true;
		$bb_signatures['one_per_user_per_page'] = true;
		$bb_signatures['allowed_tags'] = '<a><strong><i><em>';
	}
	
}
add_action( 'bp_forum_extras_signatures_init', 'bp_forum_extras_signatures_init');

//add signature link to xprofile page
function bp_forum_extras_signatures_xprofile_setup_nav() {
	global $bp;
	
	if ( !bp_is_my_profile() && !is_site_admin() )
		return false;
	
	//some reason we can't add to the subnav on the xprofile during bp_init - so if the main wrapper is disabled we need to kill it
	if ( !function_exists('bp_forum_extras_setup_globals') )
		return false;
	
	bp_core_new_subnav_item( array( 'name' => __( 'Change Signature', 'bp-forums-extras' ), 'slug' => 'forum-signature', 'parent_url' => $bp->loggedin_user->domain . $bp->profile->slug . '/', 'parent_slug' => $bp->profile->slug, 'screen_function' => 'bp_forum_extras_signatures_xprofile_screen_change_signature', 'position' => 40 ) );

}
add_action( 'xprofile_setup_nav', 'bp_forum_extras_signatures_xprofile_setup_nav', 100 );	

//xprofile page to change user signature
function bp_forum_extras_signatures_xprofile_screen_change_signature() {
	global $bp;

	if ( !bp_is_my_profile() && !is_site_admin() )
		return false;

	require_once( BP_PLUGIN_DIR . '/bp-forums/bbpress/bb-includes/functions.bb-formatting.php' );

	if ( isset( $_POST['signature-submit'] ) && check_admin_referer( 'bp_forum_extras_signatures' ) ) {

		global $bb_signatures;
		
		do_action( 'bp_forum_extras_signatures_init');
		
		$signature = trim( substr($_POST['signature'], 0, $bb_signatures['max_length']) );
		if ($signature) {

			$signature = apply_filters( 'bp_forum_extras_signatures_text_before_save', $signature );

			$signature = stripslashes( bp_forums_filter_kses( force_balance_tags( bb_code_trick( bbbp_forums_encode_bad($signature) ) ) ) );
			$signature = strip_tags( $signature, $bb_signatures['allowed_tags'] );
			$signature = implode("\n", array_slice( explode("\n",$signature), 0, $bb_signatures['max_lines']) );	

			update_usermeta($bp->displayed_user->id, "signature", $signature);
			bp_core_add_message( __( 'Signature updated!', 'bp-forums-extras' ) );
			
		} else {
			bp_core_add_message( __( 'Signature removed!', 'bp-forums-extras' ) );
			delete_usermeta($bp->displayed_user->id, "signature");
		}
	}

	add_action( 'bp_template_title', 'bp_forum_extras_signatures_xprofile_screen_title' );
	add_action( 'bp_template_content', 'bp_forum_extras_signatures_xprofile_screen_content' );
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function bp_forum_extras_signatures_xprofile_screen_title() {
	__( 'Signature', 'bp-forums-extras' );
}

function bp_forum_extras_signatures_xprofile_screen_content() { 
	global $bp, $bb_signatures;
	
	do_action( 'bp_forum_extras_signatures_init');
	
	$signature = bp_forum_extras_signatures_fetch_user_signature($bp->displayed_user->id);
	?>

	<h4><?php _e( 'Change Signature', 'bp-forums-extras' ) ?></h4>

	<p><?php _e( 'Your signature will be used on your profile and throughout the group forums.', 'bp-forums-extras') ?></p>

	<form method="post" id="bp-forums-extras-form" name="bp-forums-extras-form" class="standard-form" action="">

		<div class="clear"></div>

		<div class="editfield field_signature">
			<label for="signature">Signature:</label>
			<br/><textarea style="overflow:auto;height:5em;width:75%;" name="signature" id="signature" type="text" rows="<?php echo $bb_signatures['max_lines']; ?>" wrap="off" onkeyup="if (this.value.length > <?php echo $bb_signatures['max_length']; ?>) {this.value=this.value.substring(0,<?php echo $bb_signatures['max_length']; ?>)}"><?php echo $signature; ?></textarea>
			<div class="description"><p>Max of <?php echo $bb_signatures['max_lines']; ?> lines and <?php echo $bb_signatures['max_length']; ?> characters.</p><p>Allowed tags: <?php echo htmlspecialchars($bb_signatures['allowed_tags']); ?></p></div>
		</div>
			
		<?php do_action( 'bp_forum_extras_signatures_after_post' ) ?>
			
		<?php wp_nonce_field( 'bp_forum_extras_signatures' ) ?>

		<div class="clear"></div>

		<input type="submit" name="signature-submit" value="<?php echo __( 'Save settings', 'bp-forums-extras' );?>">

	</form><?php

}


function bp_forum_extras_signatures_add_signature_to_post($text) {
	global $topic_template, $bb_signatures, $bb_signatures_on_page;
		
	do_action( 'bp_forum_extras_signatures_init');
		
	$user_id = $topic_template->post->poster_id;
	
	if ($bb_signatures['one_per_user_per_page'] && $bb_signatures_on_page[$user_id])
		return $text;
		
	if ( $signature = bp_forum_extras_signatures_fetch_user_signature($user_id) )  :	
		$text.='<div class="signature">'.nl2br($signature).'</div>';
		$bb_signatures_on_page[$user_id] = true;
	endif;

	return $text;
}


function bp_forum_extras_signatures_insert_head() {
	if ( bp_is_profile_component() || bp_is_group_forum() || bp_is_member() )
		echo '<style type="text/css">#item-meta-signature { margin-top: 50px } .signature { width: 75%; padding:5px 0px 0px 5px; border-top:1px solid #ccc; font-size:90%; color:#444; }</style>';
}

function bp_forum_extras_signatures_add_signature_to_profile() {
	global $bp, $bb_signatures;
	
	do_action( 'bp_forum_extras_signatures_init');

	if (!$bb_signatures['on_profile'])
		return;

	if ( $signature = bp_forum_extras_signatures_fetch_user_signature($bp->displayed_user->id) )
		 echo '<div id="item-meta-signature"><div class="signature">'.nl2br($signature).'</div></div>';
}



//simple lookup function
function bp_forum_extras_signatures_fetch_user_signature($user_id) {

	$user = get_userdata( $user_id );
	
	if (!$user)
		return false;
	
	$signature = $user->signature;
	
	if ($signature)
		return $signature;
	
	return false;
}

//add admin_menu page
function bp_forum_extras_signatures_add_admin_menu() {
	global $bp;

	if ( !is_site_admin() )
		return false;

	//Add the component's administration tab under the "BuddyPress" menu for site administrators
	require ( dirname( __FILE__ ) . '/includes/admin/bp-forum-extras-signatures-admin.php' );

	add_submenu_page( 'bp-general-settings', __( 'Signatures', 'bp-forums-extras' ), '<span class="bp-forums-extras-admin-menu-item">&middot; ' . __( 'Signatures', 'bp-forums-extras' ) . '</span>', 'manage_options', 'bp-forums-extras-settings-signatures', 'bp_forum_extras_signatures_admin' );

}
add_action( 'bp_forum_extras_admin_menu', 'bp_forum_extras_signatures_add_admin_menu', 20 );

function bp_forum_extras_signatures_add_admin_screen() {
	global $bp;

	if ( !is_site_admin() )
		return false;

	?>
	<h4>Signatures enabled.</h4>
	<div class="description"><p>Users can access the edit signature page under my profile :: xprofile (profile) :: signature</p><p>You may change the <a href="<?php echo site_url() . '/wp-admin/admin.php?page=bp-forums-extras-settings-signatures' ?>">Signature Settings</a> for max length, lines, and display on profile, and allowable html tags.</p></div>
	<?php

}
add_action('bp_forum_extras_admin_screen','bp_forum_extras_signatures_add_admin_screen');

?>