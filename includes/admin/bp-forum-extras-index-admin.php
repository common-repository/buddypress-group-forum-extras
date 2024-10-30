<?php

function bp_forum_extras_index_admin() {

	$extrasindex = get_option( 'bp_forums_extras_index');

	/* If the form has been submitted and the admin referrer checks out, save the settings */
	if ( isset( $_POST['submit-admin-index'] ) && check_admin_referer('bp_forum_extras_index_admin') ) {
	
		//check for valid cap and update - if not keep old.
		if( isset($_POST['onlypublic'] ) && !empty($_POST['onlypublic']) && (int)$_POST['onlypublic'] == 1 ) {
			$extrasindex['onlypublic'] = true;
		} else {
			$extrasindex['onlypublic'] = false;
		}
		
		if( isset($_POST['hookloop'] ) && !empty($_POST['hookloop']) && (int)$_POST['hookloop'] == 1 ) {
			$extrasindex['hook_loop'] = true;
		} else {
			$extrasindex['hook_loop'] = false;	
		}
		
		if( isset($_POST['above'] ) && !empty($_POST['above']) && (int)$_POST['above'] == 1 ) {
			$extrasindex['above'] = true;
		} else {
			$extrasindex['above'] = false;
		}
		
		if( isset($_POST['groupavatar'] ) && !empty($_POST['groupavatar']) && (int)$_POST['groupavatar'] == 1 ) {
			$extrasindex['groupavatar'] = true;
		} else {
			$extrasindex['groupavatar'] = false;
		}
		
		update_option( 'bp_forums_extras_index', $extrasindex);
		
		$updated = true;
	}

?>	
	<div class="wrap">
		<h2><?php _e( 'Group Forums Directory Index Listing', 'bp-forums-extras' ); ?></h2>

		<?php if ( isset($updated) ) : echo "<div id='message' class='updated fade'><p>" . __( 'Settings Updated.', 'bp-forums-extras' ) . "</p></div>"; endif; ?>

		<form action="<?php echo site_url() . '/wp-admin/admin.php?page=bp-forums-extras-settings-index' ?>" name="forums-index-settings-form" id="forums-index-settings-form" method="post">

			<table class="form-table">
				<tr>
					<th><label for="onlypublic"><?php _e('Display only Public Forums','bp-forums-extras') ?></label></th>
					<td><input type="checkbox" name="onlypublic" id="onlypublic" value="1"<?php if ( $extrasindex['onlypublic'] ) { ?> checked="checked"<?php } ?> /></td>
				</tr>
				<tr>
					<th><label for="hookloop"><?php _e('Display within forums-loop (Ability to display above or below the loop BUT will disappear with the first ajax call (eg, My Topics tab)','bp-forums-extras') ?></label></th>
					<td><input type="checkbox" name="hookloop" id="hookloop" value="1"<?php if ( $extrasindex['hook_loop'] ) { ?> checked="checked"<?php } ?> /></td>
				</tr>
				<tr>
					<th><label for="above"><?php _e('Display Index above forums-loop','bp-forums-extras') ?></label></th>
					<td><input type="checkbox" name="above" id="above" value="1"<?php if ( $extrasindex['above'] ) { ?> checked="checked"<?php } ?> /></td>
				</tr>
				<tr>
					<th><label for="groupavatar"><?php _e('Display Group Avatar next to forum name','bp-forums-extras') ?></label></th>
					<td><input type="checkbox" name="groupavatar" id="groupavatar" value="1"<?php if ( $extrasindex['groupavatar'] ) { ?> checked="checked"<?php } ?> /></td>
				</tr>
			</table>
			
			<div class="description">
				<p>Display a simple forum index on the forums component directory page - due to limitations of bp hooks - either displayed for all topics, my topics, tags or just forums directory index</p>
			</div>

			<?php wp_nonce_field( 'bp_forum_extras_index_admin' ); ?>
			
			<p class="submit"><input type="submit" name="submit-admin-index" value="Save Settings"/></p>
			
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