<?php

function bp_forum_extras_preview_admin() {

	$fe_options = get_option( 'bp_forums_extras_preview');

	/* If the form has been submitted and the admin referrer checks out, save the settings */
	if ( isset( $_POST['submit-admin-preview'] ) && check_admin_referer('bp_forum_extras_preview_admin') ) {
	
		//check for valid cap and update - if not keep old.
		if( isset($_POST['pexcerpt'] ) && !empty($_POST['pexcerpt']) && (int)$_POST['pexcerpt'] == 1 ) {
			$fe_options['excerpt'] = true;
		} else {
			$fe_options['excerpt'] = false;
		}
		
		if( isset($_POST['excerpt_length'] ) && !empty($_POST['excerpt_length']) ) {
			$fe_options['excerpt_length'] = (int)$_POST['excerpt_length'];
		} else {
			$fe_options['excerpt_length'] = 400;
		}		
		
		update_option( 'bp_forums_extras_preview', $fe_options);
		
		$updated = true;
	}

?>	
	<div class="wrap">
		<h2><?php _e( 'Group Forums Directory Index Listing', 'bp-forums-extras' ); ?></h2>

		<?php if ( isset($updated) ) : echo "<div id='message' class='updated fade'><p>" . __( 'Settings Updated.', 'bp-forums-extras' ) . "</p></div>"; endif; ?>

		<form action="<?php echo site_url() . '/wp-admin/admin.php?page=bp-forums-extras-settings-preview' ?>" name="forums-preview-settings-form" id="forums-preview-settings-form" method="post">

			<table class="form-table">
				<tr>
					<th><label for="pexcerpt"><?php _e('Use Post Excerpt','bp-forums-extras') ?></label></th>
					<td><input type="checkbox" name="pexcerpt" id="pexcerpt" value="1"<?php if ( $fe_options['excerpt'] ) { ?> checked="checked"<?php } ?> /></td>
				</tr>
				<tr>
					<th><label for="excerpt_length"><?php _e('Post Excerpt length','bp-forums-extras') ?></label></th>
					<td><input type="text" name="excerpt_length" id="excerpt_length" value="<?php echo $fe_options['excerpt_length']; ?>" /></td>
				</tr>
			</table>
			
			<div class="description">
				<p>Uses jQuery and HoverIntent plugin - you may adjust the timeout and intent settings in /_inc/js/bp-forums-extras-preview.js (but will reset with plugin updates)</p>
			</div>

			<?php wp_nonce_field( 'bp_forum_extras_preview_admin' ); ?>
			
			<p class="submit"><input type="submit" name="submit-admin-preview" value="Save Settings"/></p>
			
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