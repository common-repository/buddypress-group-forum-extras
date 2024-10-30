<?php

function bp_forum_extras_bbcode_admin() {

	/* If the form has been submitted and the admin referrer checks out, save the settings */
	if ( isset( $_POST['submit-admin-bbcode'] ) && check_admin_referer('bp_forum_extras_bbcode_admin') ) {
	
		//check for valid cap and update - if not keep old.
		if( isset($_POST['bbcodebuttons'] ) && !empty($_POST['bbcodebuttons']) && (int)$_POST['bbcodebuttons'] == 1 ) {
			bb_update_option( 'bp_bbcode_buttons', true );
		} else {
			bb_update_option( 'bp_bbcode_buttons', false );
		}
		
		$updated = true;
	}

?>	
	<div class="wrap">
		<h2><?php _e( 'Group Forums BBCode', 'bp-forums-extras' ); ?></h2>

		<?php if ( isset($updated) ) : echo "<div id='message' class='updated fade'><p>" . __( 'Settings Updated.', 'bp-forums-extras' ) . "</p></div>"; endif; ?>

		<form action="<?php echo site_url() . '/wp-admin/admin.php?page=bp-forums-extras-settings-bbcode' ?>" name="forums-bbcode-settings-form" id="forums-bbcode-settings-form" method="post">

			<table class="form-table">
				<tr>
					<th><label for="bbcodebuttons"><?php _e('BBcode Buttons','bp-forums-extras') ?></label></th>
					<td><input type="checkbox" name="bbcodebuttons" id="bbcodebuttons" value="1"<?php if ( bb_get_option( 'bp_bbcode_buttons') ) { ?> checked="checked"<?php } ?> /></td>
				</tr>
			</table>
			
			<div class="description">
				<p>Inserts a very simple (_ck_ bbcode buttons) javascript buttons above the textarea</p>
			</div>
			
			<?php wp_nonce_field( 'bp_forum_extras_bbcode_admin' ); ?>
			
			<p class="submit"><input type="submit" name="submit-admin-bbcode" value="Save Settings"/></p>
			
		</form>

		<h3>Author:</h3>
		<div id="forums-extras-admin-tips" style="margin-left:15px;">
			<p><a href="http://etivite.com">Author's Demo BuddyPress site</a></p>
			<p>
			<a href="http://blog.etiviti.com/2010/03/buddypress-group-forum-extras/">Forum Extras Plugin About Page</a><br/> 
			<a href="http://blog.etiviti.com/tag/buddypress-plugin/">My BuddyPress Plugins</a><br/>
			<a href="http://blog.etiviti.com/tag/buddypress-hack/">My BuddyPress Hacks</a><br/>
			<a href="http://twitter.com/etiviti">Follow Me on Twitter</a>
			</p>
			<p><a href="http://buddypress.org/community/groups/buddypress-group-forum-extras/">BuddyPress.org Plugin Page</a> (with donation link)</p>

		</div>

	</div>
<?php
}

?>