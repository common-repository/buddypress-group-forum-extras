<?php
/*
Plugin Name: BuddyPress Forums Extras - Forums Index
Plugin URI: http://wordpress.org/extend/plugins/buddypress-group-forum-extras/
Description: Display a forum index listing on the Forums Directory (includes forum index and latest topic widgets)
Author: rich fuller - rich! @ etiviti
Author URI: http://buddypress.org/developers/nuprn1/
License: GNU GENERAL PUBLIC LICENSE 3.0 http://www.gnu.org/licenses/gpl.txt
Version: 0.3.0
Text Domain: bp-forums-extras
Site Wide Only: true
Network: true
*/

//change up per_page - a user can't change this within the plugin file easy

function bp_forum_extras_index_setup_globals() {

	//don't waste if we don't care
	if ( bp_forum_extras_forumsdirectory_component() ) {
	
		$extrasindex = get_option( 'bp_forums_extras_index');
	
		if ( $extrasindex['hook_loop'] ) {
			if ( $extrasindex['above'] ) {
				add_action( 'bp_before_directory_forums_list', 'bp_forum_extras_index_screen' );
			} else {
				add_action( 'bp_after_directory_forums_list', 'bp_forum_extras_index_screen' );
			}
		} else {
			if ( $extrasindex['above'] ) {
				add_action( 'bp_directory_forums_content', 'bp_forum_extras_index_screen' );
			} else {
				add_action( 'bp_after_directory_forums_content', 'bp_forum_extras_index_screen' );
			}		
		}
	}
}
add_action( 'bp_forum_extras_setup_globals', 'bp_forum_extras_index_setup_globals',1);

function bp_forum_extras_index_screen() {
	global $bp; 
	
	$extrasindex = get_option( 'bp_forums_extras_index'); 
	$groupavatar = $extrasindex['groupavatar']; ?>

	<?php if ( $forumlisting = bp_forum_extras_index_get_forums( BP_FORUMS_PARENT_FORUM_ID ) ) { ?>
		
		<div class="clear"></div>
		<h4><?php _e( 'Forums Index', 'bp-forums-extras' ); ?></h4>
		<div class="pagination"><div class="pag-count" id="forum-count">Viewing <?php echo count($forumlisting); ?> total forums</div></div>
		<table id="forumlist" class="forum">
			<thead>
				<tr>
					<th id="th-title"><?php _e('Forum', 'bp-forums-extras' ); ?></th>
					<th id="th-topiccount"><?php _e('Topics', 'bp-forums-extras' ); ?></th>
					<th id="th-postcount"><?php _e('Posts', 'bp-forums-extras' ); ?></th>
					<th id="th-members"><?php _e('Members', 'bp-forums-extras' ); ?></th>
					<th id="th-lastpost"><?php _e('Last Post', 'bp-forums-extras' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php 
				$f = 0;
				foreach ( (array)$forumlisting as $listing ) { 
				
					if (!is_user_logged_in() && $listing->status == 'private') {
						$forumlink = $bp->root_domain . '/' . $bp->groups->slug . '/' . $listing->slug . '/';
					} else {
						$forumlink = $bp->root_domain . '/' . $bp->groups->slug . '/' . $listing->slug . '/forum/';
					}?>
					<tr <?php if ($f % 2 == 1) echo 'class="alt"'; ?>>
						<td class="td-title"><?php if ($groupavatar) bp_forum_extras_the_forum_avatar( 'item_id='. $listing->id ) ?><h5><a href="<?php echo $forumlink; ?>"><?php echo $listing->forum_name; ?></a></h5><small> &ndash; <?php echo bp_create_excerpt( $listing->forum_desc, 255 ); ?></small></td>
						<td class="num td-topiccount alt"><?php echo $listing->topics; ?></td>
						<td class="num td-postcount"><?php echo $listing->posts; ?></td>
						<td class="num td-members alt"><?php echo $listing->total_member_count; ?></td>
						<td class="td-lastpost"><?php bp_forum_extras_index_the_forum_last_updated( 'forum_id='. $listing->forum_id .'&per_page=15&forumlink='. $forumlink ); ?></td>
					</tr>
				<?php 
				$f++;
				} ?>
			</tbody>
		</table>
	<?php }
}


function bp_forum_extras_index_last_topic_update( $post_id ) {
	global $wpdb, $bp, $bbdb;
	
	if ( !is_numeric( $post_id ) )
		return;
	
	if ( !$post = bp_forums_get_post( $post_id ) ) 
		return;

	$last_topic = $bbdb->get_row("SELECT s1.forum_id, s1.topic_id FROM $bbdb->topics AS s1 JOIN (SELECT MAX(topic_last_post_id) AS topic_last_post_id FROM $bbdb->topics WHERE topic_status=0 AND forum_id = $post->forum_id GROUP BY forum_id) AS s2 ON s1.topic_last_post_id = s2.topic_last_post_id"); 
	
	bb_update_forummeta( $post->forum_id, 'groupforumindex_last_topic_id', $last_topic->topic_id );
}
add_action( 'bb_new_post', 'bp_forum_extras_index_last_topic_update');
add_action( 'bb_delete_post','bp_forum_extras_index_last_topic_update');


function bp_forum_extras_index_the_forum_last_updated( $args = '' ) {
	echo bp_forum_extras_index_get_the_forum_last_updated( $args );
}
	function bp_forum_extras_index_get_the_forum_last_updated( $args = '' ) {
		global $bp, $bbdb;

		$defaults = array(
			'width' => '20',
			'height' => '20',
			'per_page' => 15,
		);

		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_SKIP );

		if ( !$forum_id )
			return;

		$groupforumindex_last_topic_id = bb_get_forummeta( $forum_id, 'groupforumindex_last_topic_id' );

		if ( !$groupforumindex_last_topic_id)
			return;

		$topic = get_topic( $groupforumindex_last_topic_id );

		return apply_filters( 'bp_get_the_topic_post_poster_avatar', bp_core_fetch_avatar( array( 'item_id' => $topic->topic_last_poster, 'type' => 'thumb', 'width' => $width, 'height' => $height ) ) ) .'<div class="poster-name">'. bp_core_get_userlink( $topic->topic_last_poster ) .'</div><small> &ndash; <a href="'. $forumlink .'topic/' . $topic->topic_slug . '/' . bp_forum_extras_index_last_post_link($per_page, $topic->topic_posts, $topic->topic_last_post_id) .'">'. bp_core_time_since( strtotime( $topic->topic_time) ) .'</a></small>';

	}

function bp_forum_extras_index_last_post_link( $per_page = 15, $topic_posts, $topic_last_post_id ) {
	$page = bp_forum_extras_get_page_number($topic_posts, $per_page);
	$page = (1 < $page) ? '?topic_page='. $page .'&num='. $per_page : '';
	
	return $page ."#post-". $topic_last_post_id;
}


function bp_forum_extras_the_forum_avatar( $args = '' ) {
	echo bp_forum_extras_get_the_forum_avatar( $args );
}
	function bp_forum_extras_get_the_forum_avatar( $args = '' ) {

		$defaults = array(
			'width' => '20',
			'height' => '20',
		);

		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_SKIP );

		return bp_core_fetch_avatar( array( 'item_id' => $item_id, 'type' => 'thumb', 'object' => 'group', 'width' => $width, 'height' => $height ) );
	}


/* Register widgets for the core component */
function bp_forum_extras_index_widgets_init() {
	add_action('widgets_init', create_function('', 'return register_widget("BP_Forum_Extras_Index_Widget");') );
	add_action('widgets_init', create_function('', 'return register_widget("BP_Forum_Extras_Index_Latest_Topics_Widget");') );
}
add_action( 'bp_register_widgets', 'bp_forum_extras_index_widgets_init', 15 );



class BP_Forum_Extras_Index_Widget extends WP_Widget {
	
	
	function bp_forum_extras_index_widget() {
		
		parent::WP_Widget( false, $name = __( 'Forum Index', 'buddypress' ) );
		//if ( is_active_widget( false, false, $this->id_base ) )
			
	}

	function widget($args, $instance) {
		global $bbdb,$bp;
		
		if ( !$bbdb )
			return;
		
		//don't care to load on these pages - we don't do anything and plus the bbpress_init causes function redeclaration fatal errors.
		if ( bp_is_register_page() || bp_is_activation_page() || bp_is_user_blogs() )
			return;

		if ( bp_forum_extras_blogs_component() )
			return;
		
	    extract( $args );
		
		if ( !is_numeric( $instance['forum_groupavatar_size'] ) )
			$instance['forum_groupavatar_size'] = 20;
		
		echo $before_widget;
		echo $before_title
		   . $widget_name 
		   . $after_title; ?>

		<?php if ( $forumlisting = bp_forum_extras_index_get_forums( BP_FORUMS_PARENT_FORUM_ID ) ) { 
			$forumlisting = array_slice( $forumlisting, 0, $instance['forum_max'] ); ?>
			<ul id="forums-list" class="item-list">
				<?php foreach ( (array)$forumlisting as $listing ) { 
			
					if (!is_user_logged_in() && $listing->status == 'private') {
						$forumlink = $bp->root_domain . '/' . $bp->groups->slug . '/' . $listing->slug . '/';
					} else {
						$forumlink = $bp->root_domain . '/' . $bp->groups->slug . '/' . $listing->slug . '/forum/';
					} ?>
					<li>
						<?php if ( $instance['forum_groupavatar'] ) { ?>
							<div class="item-avatar">
								<?php bp_forum_extras_the_forum_avatar( 'item_id='. $listing->id .'&width=' . $instance['forum_groupavatar_size'] .'&height=' . $instance['forum_groupavatar_size'] ) ?>
							</div>
						<?php } ?>

						<div class="item">
							<div class="item-title"><a href="<?php echo $forumlink; ?>"><?php echo $listing->forum_name; ?></a></div>
							<?php if ( $instance['forum_groupdesc'] ) { ?>
								<div class="item-meta"><span class="description">&ndash; <?php echo bp_create_excerpt( $listing->forum_desc, 20 ); ?></span>
								</div>
							<?php } ?>
						</div>
					</li>
				<?php } ?>
			</ul>
		<?php } else { ?>
			<div class="widget-error">
				<?php _e('No forums', 'bp-forums-extras') ?>
			</div>
		<?php }
			
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['forum_groupavatar'] =  strip_tags( $new_instance['forum_groupavatar'] );
		$instance['forum_groupdesc'] = strip_tags( $new_instance['forum_groupdesc'] );
		$instance['forum_max'] = strip_tags( $new_instance['forum_max'] );
		$instance['forum_groupavatar_size'] = strip_tags( $new_instance['forum_groupavatar_size'] );

		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'forum_max' => 5, 'forum_groupavatar' => true, 'forum_groupdesc' => true, 'forum_groupavatar_size' => 20 ) );
		$forum_groupavatar = strip_tags( $instance['forum_groupavatar'] );
		$forum_groupdesc = strip_tags( $instance['forum_groupdesc'] );
		$forum_max = strip_tags( $instance['forum_max'] );
		$forum_groupavatar_size = strip_tags( $instance['forum_groupavatar_size'] );
		
		?>

			<table class="form-table">
				<tr>
					<th><label for="<?php echo $this->get_field_id( 'forum_max' ); ?>"><?php _e('Max Forums to show:', 'buddypress'); ?></label></th>
					<td><input id="<?php echo $this->get_field_id( 'forum_max' ); ?>" name="<?php echo $this->get_field_name( 'forum_max' ); ?>" type="text" value="<?php echo attribute_escape( $forum_max ); ?>" size="2" /></td>
				</tr>
				<tr>
					<th><label for="<?php echo $this->get_field_id( 'forum_groupavatar' ); ?>"><?php _e('Display Group Avatar next to forum name','bp-forums-extras') ?></label></th>
					<td><input type="checkbox" name="<?php echo $this->get_field_name( 'forum_groupavatar' ); ?>" id="<?php echo $this->get_field_id( 'forum_groupavatar' ); ?>" value="1"<?php if ( $forum_groupavatar ) { ?> checked="checked"<?php } ?> /></td>
				</tr>
				<tr>
					<th><label for="<?php echo $this->get_field_id( 'forum_groupavatar_size' ); ?>"><?php _e('Avatar Width/Height px Size (same ratio):', 'buddypress'); ?></label></th>
					<td><input id="<?php echo $this->get_field_id( 'forum_groupavatar_size' ); ?>" name="<?php echo $this->get_field_name( 'forum_groupavatar_size' ); ?>" type="text" value="<?php echo attribute_escape( $forum_groupavatar_size ); ?>" size="3" /></td>
				</tr>
				<tr>
					<th><label for="<?php echo $this->get_field_id( 'forum_groupdesc' ); ?>"><?php _e('Display Group Description under forum name','bp-forums-extras') ?></label></th>
					<td><input type="checkbox" name="<?php echo $this->get_field_name( 'forum_groupdesc' ); ?>" id="<?php echo $this->get_field_id( 'forum_groupdesc' ); ?>" value="1"<?php if ( $forum_groupdesc ) { ?> checked="checked"<?php } ?> /></td>
				</tr>
			</table>
			<p class="description"><small>Internal sort order is taken from the db table: _bb_forums.forum_order ASC</small></p>
	<?php
	}
}

class BP_Forum_Extras_Index_Latest_Topics_Widget extends WP_Widget {
	
	
	function bp_forum_extras_index_latest_topics_widget() {
		
		parent::WP_Widget( false, $name = __( 'Latest Topics', 'buddypress' ) );
		//if ( is_active_widget( false, false, $this->id_base ) )
			
	}

	function widget($args, $instance) {
		global $bp;
		
		//don't care to load on these pages - we don't do anything and plus the bbpress_init causes function redeclaration fatal errors.
		if ( bp_is_register_page() || bp_is_activation_page() || bp_is_user_blogs() )
			return;

		if ( bp_forum_extras_blogs_component() )
			return;
			
		if ( BP_FORUMS_SLUG == bp_current_component() && bp_is_directory() )
			return;
		
	    extract( $args );
		
		if ( !is_numeric( $instance['forum_latest_limit'] ) )
			$instance['forum_latest_limit'] = 10;
		
		echo $before_widget;
		echo $before_title
		   . $widget_name 
		   . $after_title;

		if ( bp_has_forum_topics( 'page=false&max='. $instance['forum_latest_limit'] ) ) : ?>

			<ul id="widget-topic-list" class="item-list">
				<?php while ( bp_forum_topics() ) : bp_the_forum_topic(); ?>
					<li>
						<div class="item">
							<div class="item-title"><a class="topic-title" href="<?php bp_the_topic_permalink() ?>" title="<?php bp_the_topic_title() ?> - <?php _e( 'Permalink', 'buddypress' ) ?>"><?php echo bp_create_excerpt( bp_get_the_topic_title(), 8) ?></a></div>
							<span class="description">&ndash; posted in <a href="<?php bp_the_topic_object_permalink() ?>" title="<?php bp_the_topic_object_name() ?>"><?php bp_the_topic_object_name() ?></a></span>
						</div>
					</li>
				<?php endwhile; ?>
			</ul>
		<?php else: ?>
			<div>
				<p><?php _e( 'Sorry, there were no forum topics found.', 'buddypress' ) ?></p>
			</div>
		<?php endif;
			
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['forum_latest_limit'] =  strip_tags( $new_instance['forum_latest_limit'] );

		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'forum_latest_limit' => 10 ) );
		$forum_latest_limit = strip_tags( $instance['forum_latest_limit'] );
		
		?>
			<table class="form-table">
				<tr>
					<th><label for="<?php echo $this->get_field_id( 'forum_latest_limit' ); ?>"><?php _e('Max Newest Topics to show:', 'buddypress'); ?></label></th>
					<td><input id="<?php echo $this->get_field_id( 'forum_latest_limit' ); ?>" name="<?php echo $this->get_field_name( 'forum_latest_limit' ); ?>" type="text" value="<?php echo attribute_escape( $forum_latest_limit ); ?>" size="4" /></td>
				</tr>
			</table>
	<?php
	}
}

function bp_forum_extras_index_get_forums( $parent_forum_id = BP_FORUMS_PARENT_FORUM_ID ) {
	global $wpdb, $bp, $bbdb;

	$extrasindex = get_option( 'bp_forums_extras_index');

	if ( is_user_logged_in() ) {
		$members_sql = " {$bp->groups->table_name_members} m,";
		
		if ( $extrasindex['onlypublic'] ) {
			$loggedin_sql = " AND ( ( g.id = m.group_id AND m.user_id = {$bp->loggedin_user->id} AND m.is_confirmed = 1 AND m.is_banned = 0 AND g.status='hidden' ) || ( g.id = m.group_id AND m.user_id = {$bp->loggedin_user->id} AND m.is_confirmed = 1 AND m.is_banned = 0 AND g.status='private' ) || g.status = 'public' )";
		} else {
			$loggedin_sql = " AND ( ( g.id = m.group_id AND m.user_id = {$bp->loggedin_user->id} AND m.is_confirmed = 1 AND m.is_banned = 0 AND g.status='hidden' ) || g.status != 'hidden')";
		}
		
	} else {
	
		if ( $extrasindex['onlypublic'] ) {
			$loggedin_sql = " AND g.status = 'public'";
		} else {
			$loggedin_sql = " AND g.status != 'hidden'";
		}
	}
	
	$paged_groups = $wpdb->get_results( "SELECT DISTINCT g.*, f.*, gm1.meta_value as total_member_count FROM {$bp->groups->table_name_groupmeta} gm1, {$bp->groups->table_name_groupmeta} gm3, {$members_sql} {$bbdb->forums} f, {$bp->groups->table_name} g WHERE g.enable_forum = 1 AND g.id = gm1.group_id AND g.id = gm3.group_id AND gm1.meta_key = 'total_member_count' AND (gm3.meta_key = 'forum_id' AND gm3.meta_value = f.forum_id AND f.forum_parent = {$parent_forum_id}) {$loggedin_sql} ORDER BY f.forum_order ASC" );

	if ( !empty( $populate_extras ) ) {
		foreach ( (array)$paged_groups as $group ) $group_ids[] = $group->id;
		$group_ids = $wpdb->escape( join( ',', (array)$group_ids ) );
		$paged_groups = BP_Groups_Group::get_group_extras( &$paged_groups, $group_ids, 'index' );
	}

	return $paged_groups;
}

//add admin_menu page
function bp_forum_extras_index_add_admin_menu() {
	global $bp;

	if ( !is_site_admin() )
		return false;

	//Add the component's administration tab under the "BuddyPress" menu for site administrators
	require ( dirname( __FILE__ ) . '/includes/admin/bp-forum-extras-index-admin.php' );

	add_submenu_page( 'bp-general-settings', __( 'Forums Index', 'bp-forums-extras' ), '<span class="bp-forums-extras-admin-menu-item">&middot; ' . __( 'Forums Index', 'bp-forums-extras' ) . '</span>', 'manage_options', 'bp-forums-extras-settings-index', 'bp_forum_extras_index_admin' );

}
add_action( 'bp_forum_extras_admin_menu', 'bp_forum_extras_index_add_admin_menu', 20 );

function bp_forum_extras_index_add_admin_screen() {
	global $bp;

	if ( !is_site_admin() )
		return false;

	?>
	<h4>Forums Index enabled</h4>
	<div class="description">Creates a simple (but limited in placement) forums index listing on the /forums/ directory component page.<p>You may change the <a href="<?php echo site_url() . '/wp-admin/admin.php?page=bp-forums-extras-settings-index' ?>">Forum Index Settings</a> for public only (logged in users will see their private and hidden forums).</p></div>
	<?php
}
add_action('bp_forum_extras_admin_screen','bp_forum_extras_index_add_admin_screen');

?>