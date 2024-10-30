<?php

function bp_forum_extras_signatures_admin() {
	global $bb_signatures;

	do_action( 'bp_forum_extras_signatures_init');

	/* If the form has been submitted and the admin referrer checks out, save the settings */
	if ( isset( $_POST['submit-admin-signatures'] ) && check_admin_referer('bp_forum_extras_signatures_admin') ) {
	
		if ( isset($_POST['max_lines']) && !empty($_POST['max_lines']) && (int)$_POST['max_lines'] > 0) {
			$bb_signatures['max_lines'] = $_POST['max_lines'];
		} else {
			$bb_signatures['max_lines'] = 3;
		}

		if ( isset($_POST['max_length']) && !empty($_POST['max_length']) && (int)$_POST['max_length'] > 0 ) {
			$bb_signatures['max_length'] = $_POST['max_length'];
		} else {
			$bb_signatures['max_length'] = 300;
		}

		//check for valid cap and update - if not keep old.
		if( isset($_POST['on_profile'] ) && !empty($_POST['on_profile']) && (int)$_POST['on_profile'] == 1 ) {
			$bb_signatures['on_profile'] =true;
		} else {
			$bb_signatures['on_profile'] = false;
		}
		//check for valid cap and update - if not keep old.
		if( isset($_POST['one_per_user_per_page'] ) && !empty($_POST['one_per_user_per_page']) && (int)$_POST['one_per_user_per_page'] == 1 ) {
			$bb_signatures['one_per_user_per_page'] =true;
		} else {
			$bb_signatures['one_per_user_per_page'] = false;
		}
		
		if ( isset($_POST['allowed_tags']) && !empty($_POST['allowed_tags']) ) {
			$bb_signatures['allowed_tags'] = $_POST['allowed_tags'];
		} else {
			$bb_signatures['allowed_tags'] = '<a><strong><i><em>';
		}
		
		bb_update_option('bb_signatures', $bb_signatures);
		
		$updated = true;
	}

	$atags = bp_forum_extras_filter_allowedtags();

?>	
	<div class="wrap">
		<h2><?php _e( 'Group Forums Signatures', 'bp-forums-extras' ); ?></h2>

		<?php if ( isset($updated) ) : echo "<div id='message' class='updated fade'><p>" . __( 'Settings Updated.', 'bp-forums-extras' ) . "</p></div>"; endif; ?>

		<form action="<?php echo site_url() . '/wp-admin/admin.php?page=bp-forums-extras-settings-signatures' ?>" name="forums-signatures-settings-form" id="forums-signatures-settings-form" method="post">

			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="max_length"><?php _e( 'Max character length', 'bp-forums-extras' ) ?></label></th>
					<td><input type="text" name="max_length" id="max_length" value="<?php echo $bb_signatures['max_length']; ?>" /></td>
				</tr>
				<tr>
					<th><label for="max_lines"><?php _e('Max new lines length','bp-forums-extras') ?></label></th>
					<td><input type="text" name="max_lines" id="max_lines" value="<?php echo $bb_signatures['max_lines']; ?>" /></td>
				</tr>
				<tr>
					<th><label for="one_per_user_per_page"><?php _e('One Signature per User per Page','bp-forums-extras') ?></label></th>
					<td><input type="checkbox" name="one_per_user_per_page" id="one_per_user_per_page" value="1"<?php if ( $bb_signatures['one_per_user_per_page'] ) { ?> checked="checked"<?php } ?> /></td>
				</tr>

				<tr>
					<th><label for="on_profile"><?php _e('Display on User Profile','bp-forums-extras') ?></label></th>
					<td><input type="checkbox" name="on_profile" id="on_profile" value="1"<?php if ( $bb_signatures['on_profile'] ) { ?> checked="checked"<?php } ?> /></td>
				</tr>
				
				<tr>
					<th><label for="allowed_tags"><?php _e('Allowed html tags','bp-forums-extras') ?></label></th>
					<td><input type="text" name="allowed_tags" id="allowed_tags" value="<?php echo htmlspecialchars($bb_signatures['allowed_tags']); ?>" /> (default is <?php echo htmlspecialchars('<a><strong><i><em>'); ?>)
					<p class="description">*please note - html tags only, if extras - bbcode is enabled the allowed set here will be restricted after bbcode to html conversion. Uses <a href="http://php.net/manual/en/function.strip-tags.php">strip_tags</a> to allow a defined set of html tags - so no closing tag.<br/>
					<br/>Overall allowed set of html tags for content: (some tags will be excluded regardless: br, hr, input, param, area, col, embed )<br/>
					<?php 
					foreach ($atags as $key => $value) {
						echo '&lt;'. $key . '&gt; ';
					}
					?></p></td>
				</tr>
				
			</table>
			
			<div id='message' class="notice">
				<p>*if you have bbPress on an external install and sharing the same wp_ tables - this plugin will use the same meta options from _ck_ bb-signatures (settings and user signature data)</p>
			</div>
			
			<?php wp_nonce_field( 'bp_forum_extras_signatures_admin' ); ?>
			
			<p class="submit"><input type="submit" name="submit-admin-signatures" value="Save Settings"/></p>
			
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