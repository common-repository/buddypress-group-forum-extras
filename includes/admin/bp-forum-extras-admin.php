<?php

function bp_forum_extras_admin() {
	global $bp
	
	/* If the form has been submitted and the admin referrer checks out, save the settings */
	//if ( isset( $_POST['submit'] ) && check_admin_referer('bp_forum_extras_admin') ) {
	//
	//	$updated = true;
	//}

?>	
	<div class="wrap">
		<h2><?php _e( 'Group Forum Extras', 'bp-forums-extras' ); ?></h2>

		<div class="description"><a href="http://buddypress.org/forums/topic/new-plugin-buddypress-group-forum-extras">Please report any issues</a> - this plugin enables BP internal bbPress which has caused some function naming errors between wordpress and bbpress.</div>

		<h3>Enabled subplugins:</h3>
		
		<div id="forums-extras-admin" style="margin-left:15px;">
			<?php do_action('bp_forum_extras_admin_screen'); ?>
		</div>
		
		<h3>Extra Functions:</h3>
		<div id="forums-extras-admin-tips" style="margin-left:15px;">
			<h4>Link the freshness time_since on group forum pages to the last post</h4>
			<p class="description">
			Add this function and filter to your bp-custom.php file</p>

			function bp_forum_extras_get_the_topic_time_since_last_post( $topic_time ) {<br/>
				return '&lt;a href="'. bp_forum_extras_topic_last_post_link( 15 ) .'"&gt;'. $topic_time .'&lt;/a&gt;';<br/>
			}<br/>
			add_filter('bp_get_the_topic_time_since_last_post','bp_forum_extras_get_the_topic_time_since_last_post');<br/>

			<p class="description">
			Note: 15 per_page is default for bp_has_forum_topic_posts - you may need to change this if you use a different per_page in the loop.
			</p>			
			
		</div>
		
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