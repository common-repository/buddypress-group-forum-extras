<?php
/*
 Plugin Name: BuddyPress Forums Extras - RSS Feeds
 Plugin URI: http://wordpress.org/extend/plugins/buddypress-group-forum-extras/
 Description: Enable Forum and Topic RSS Feeds for public groups. Theme edit optional.
Author: rich fuller - rich! @ etiviti
 Author URI: http://buddypress.org/developers/nuprn1/
 License: GNU GENERAL PUBLIC LICENSE 3.0 http://www.gnu.org/licenses/gpl.txt
Version: 0.3.0
Text Domain: bp-forums-extras
Site Wide Only: true
Network: true
*/

function bp_forum_extras_rssfeeds_setup_globals() {

	add_action( 'bp_after_group_forum_content', 'bp_forum_extras_rssfeeds_forum_feed_link' );
	add_action('bp_head','bp_forum_extras_rssfeeds_insert_topic_rel_head');
	
	add_action( 'bp_after_group_forum_topic', 'bp_forum_extras_rssfeeds_topic_feed_link' );
	add_action('bp_head','bp_forum_extras_rssfeeds_insert_forum_rel_head');
	
}
add_action( 'bp_forum_extras_setup_globals', 'bp_forum_extras_rssfeeds_setup_globals',1);

//rss autodisc
function bp_forum_extras_rssfeeds_insert_forum_rel_head() {
	global $bp;

	if ( !( !bp_is_group_forum_topic() && bp_is_group_forum() ) )
		return false;

	if ( $bp->groups->current_group->status == 'hidden' || $bp->groups->current_group->status == 'private' )
		return false;
		
	$link = $bp->root_domain . '/' . $bp->current_component . '/' . $bp->current_item . '/'. 'forum/feed/';

	echo '<link rel="alternate" type="application/rss+xml" title="'. get_blog_option( BP_ROOT_BLOG, 'blogname' ) .' | '. $bp->groups->current_group->name .' | Forum Topics" href="'. $link .'" />';
}
function bp_forum_extras_rssfeeds_insert_topic_rel_head() {
	global $bp;

	if ( !bp_is_group_forum_topic() )
		return false;

	if ( $bp->groups->current_group->status == 'hidden' || $bp->groups->current_group->status == 'private' )
		return false;
		
	$link = $bp->root_domain . '/' . $bp->current_component . '/' . $bp->current_item . '/'. 'forum/topic/' . $bp->action_variables[1] . '/feed/';

	echo '<link rel="alternate" type="application/rss+xml" title="'. get_blog_option( BP_ROOT_BLOG, 'blogname' ) .' | '. $bp->groups->current_group->name .' | Topic" href="'. $link .'" />';
}



//catchuris for forum and topic level feeds
function bp_forum_extras_rssfeeds_forum_feed() {
	global $bp, $wp_query;

	if ( !function_exists('bp_forum_extras_setup_globals') )
		return false;

	if ( !bp_forum_extras_rssfeeds_forum_feed_component() )
		return false;

	if ( $bp->groups->current_group->status == 'hidden' || $bp->groups->current_group->status == 'private' )
		return false;

	do_action('bp_forum_extras_bbpress_init');

	$forum_id = groups_get_groupmeta( $bp->groups->current_group->id, 'forum_id' );

	if ( !$topics = get_latest_topics( $forum_id ) )
		return false;
			
	$link = $bp->root_domain . '/' . $bp->current_component . '/' . $bp->current_item . '/'. 'forum/';
	$link_self = $bp->root_domain . '/' . $bp->current_component . '/' . $bp->current_item . '/'. 'forum/feed/';
			
	$wp_query->is_404 = false;
	status_header( 200 );

	include_once( dirname( __FILE__ ) . '/feeds/bp-forum-extras-group-forum-feed.php' );
	die;
}
add_action( 'bp_init', 'bp_forum_extras_rssfeeds_forum_feed', 1 );

function bp_forum_extras_rssfeeds_topic_feed() {
	global $bp, $wp_query;

	if ( !function_exists('bp_forum_extras_setup_globals') )
		return false;

	if ( !bp_forum_extras_rssfeeds_topic_feed_component()  )
		return false;
		
	if ( $bp->groups->current_group->status == 'hidden' || $bp->groups->current_group->status == 'private' )
		return false;
		
	$topic_id = bp_forums_get_topic_id_from_slug( $bp->action_variables[1] );
	
	if ( !is_numeric( $topic_id ) )
		return false;

	do_action('bp_forum_extras_bbpress_init');

	if ( !$topic = get_topic ( $topic_id ) )
		return false;
	
	if ( !$posts = get_thread( $topic_id, 0, 1 ) )
		return false;
	
	$link = $bp->root_domain . '/' . $bp->current_component . '/' . $bp->current_item . '/'. 'forum/topic/' . $topic->topic_slug . '/';
	$link_self = $bp->root_domain . '/' . $bp->current_component . '/' . $bp->current_item . '/'. 'forum/topic/' . $topic->topic_slug . '/feed/';

	$wp_query->is_404 = false;
	status_header( 200 );

	include_once( dirname( __FILE__ ) . '/feeds/bp-forum-extras-group-topic-feed.php' );
	die;
}
add_action( 'bp_init', 'bp_forum_extras_rssfeeds_topic_feed', 1 );



//rss links
function bp_forum_extras_rssfeeds_topic_feed_link() {
	global $bp;

	if ( !bp_is_group_forum_topic() )
		return false;

	if ( $bp->groups->current_group->status == 'hidden' || $bp->groups->current_group->status == 'private' )
		return false;

	$link_self = $bp->root_domain . '/' . $bp->current_component . '/' . $bp->current_item . '/'. 'forum/topic/' . $bp->action_variables[1] . '/feed/';
?>
<div id="subnav" class="item-list-tabs no-ajax">
	<ul>
		<li class="feed"><a title="RSS Feed" href="<?php echo $link_self; ?>">RSS</a></li>
	</ul>
</div>
<?php
}
function bp_forum_extras_rssfeeds_forum_feed_link() {
	global $bp;

	if ( !( !bp_is_group_forum_topic() && bp_is_group_forum() ) )
		return false;

	if ( $bp->groups->current_group->status == 'hidden' || $bp->groups->current_group->status == 'private' )
		return false;

	$link_self = $bp->root_domain . '/' . $bp->current_component . '/' . $bp->current_item . '/'. 'forum/feed/';
?>
<div id="subnav" class="item-list-tabs no-ajax">
	<ul>
		<li class="feed"><a title="RSS Feed" href="<?php echo $link_self; ?>">RSS</a></li>
	</ul>
</div>
<?php
}



//helpers
function bp_forum_extras_rssfeeds_forum_feed_component() {
	global $bp;

	if ( !bp_is_group_forum_topic() && bp_is_group_forum() && 'feed' == $bp->action_variables[0] )
		return true;

	return false;
}
function bp_forum_extras_rssfeeds_topic_feed_component() {
	global $bp;

	if ( bp_is_group_forum_topic() && 'feed' == $bp->action_variables[2] )
		return true;

	return false;
}


//add admin_menu page
function bp_forum_extras_rssfeeds_add_admin_menu() {
	global $bp;

	if ( !is_site_admin() )
		return false;

	//Add the component's administration tab under the "BuddyPress" menu for site administrators
	require ( dirname( __FILE__ ) . '/bp-forum-extras-rssfeeds-admin.php' );

	add_submenu_page( 'bp-general-settings', __( 'RSS Feeds', 'bp-forums-extras' ), '<span class="bp-forums-extras-admin-menu-item">&middot; ' . __( 'RSS', 'bp-forums-extras' ) . '</span>', 'manage_options', 'bp-forums-extras-settings-rssfeeds', 'bp_forum_extras_rssfeeds_admin' );

}
//add_action( 'bp_forum_extras_admin_menu', 'bp_forum_extras_rssfeeds_add_admin_menu', 20 );

function bp_forum_extras_rssfeeds_add_admin_screen() {
	global $bp;

	if ( !is_site_admin() )
		return false;

	?>
	<h4>RSS Feeds enabled.</h4>
	
	<?php
}
add_action('bp_forum_extras_admin_screen','bp_forum_extras_rssfeeds_add_admin_screen');
?>