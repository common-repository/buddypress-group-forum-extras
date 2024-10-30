<?php
/*
Plugin Name: BuddyPress Forums Extras - Tag Index
Plugin URI: http://wordpress.org/extend/plugins/buddypress-group-forum-extras/
Description: Display a tag index listing on the Forums Directory (includes a widget)
Author: rich fuller - rich! @ etiviti
Author URI: http://buddypress.org/developers/nuprn1/
License: GNU GENERAL PUBLIC LICENSE 3.0 http://www.gnu.org/licenses/gpl.txt
Version: 0.3.0
Text Domain: bp-forums-extras
Site Wide Only: true
Network: true
*/

function bp_forum_extras_tags_setup_globals() {

	//don't waste if we don't care
	if ( bp_forum_extras_forumsdirectory_component() ) {
	
		$extrasindex = get_option( 'bp_forums_extras_index');
	
		if ( $extrasindex['hook_loop'] ) {
			add_action( 'bp_after_directory_forums_list', 'bp_forum_extras_tags_screen' );
		} else {
			add_action( 'bp_directory_forums_content', 'bp_forum_extras_tags_screen' );
		}
	}
}
add_action( 'bp_forum_extras_setup_globals', 'bp_forum_extras_tags_setup_globals',1);

function bp_forum_extras_tags_screen() {
	global $bp; 
	 ?>
		<div id="tags"></div>
		<div class="clear"></div>
		<h4><?php _e( 'Topic Tags', 'bp-forums-extras' ); ?></h4>
		
		<div id="tag-text">
			<?php bp_forums_tag_heat_map( 10, 46, 'pt', 90 ); ?>
		</div>
		
	<?php

}

function bp_forum_extras_tags_widgets_init() {
	add_action('widgets_init', create_function('', 'return register_widget("bp_forum_extras_tags_Widget");') );
}
add_action( 'bp_init', 'bp_forum_extras_tags_widgets_init', 15 );





class bp_forum_extras_tags_Widget extends WP_Widget {
	
	
	function bp_forum_extras_tags_widget() {
		
		parent::WP_Widget( false, $name = __( 'Forum Tags', 'buddypress' ) );
		//if ( is_active_widget( false, false, $this->id_base ) )
			
	}

	function widget($args, $instance) {
		global $bbdb, $bp;
		
		if ( !$bbdb )
			return;
		
		//don't care to load on these pages - we don't do anything and plus the bbpress_init causes function redeclaration fatal errors.
		if ( bp_is_register_page() || bp_is_activation_page() || bp_is_user_blogs() )
			return;

		if ( bp_forum_extras_blogs_component() )
			return;
			
		if ( BP_FORUMS_SLUG == bp_current_component() && bp_is_directory() )
			return;
		
	    extract( $args );

		if ( !is_numeric( $instance['forum_tags_smallest'] ) )
			$instance['forum_tags_smallest'] = 10;
		if ( !is_numeric( $instance['forum_tags_largest'] ) )
			$instance['forum_tags_largest'] = 42;
		if ( !is_numeric( $instance['forum_tags_limit'] ) )
			$instance['forum_tags_limit'] = 50;
		
		echo $before_widget;
		echo $before_title
		   . $widget_name 
		   . $after_title;
		
		?>
			<?php if ( function_exists('bp_forums_tag_heat_map') ) : ?>
				<div id="tag-text"><?php bp_forums_tag_heat_map(); ?></div>
			<?php endif; ?>
		<?php
			
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		
		$instance['forum_tags_smallest'] =  strip_tags( $new_instance['forum_tags_smallest'] );
		$instance['forum_tags_largest'] = strip_tags( $new_instance['forum_tags_largest'] );
		$instance['forum_tags_limit'] = strip_tags( $new_instance['forum_tags_limit'] );

		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'forum_tags_smallest' => 10, 'forum_tags_largest' => 42, 'forum_tags_limit' => 50 ) );
		$forum_tags_largest = strip_tags( $instance['forum_tags_largest'] );
		$forum_tags_smallest = strip_tags( $instance['forum_tags_smallest'] );
		$forum_tags_limit = strip_tags( $instance['forum_tags_limit'] );
		?>
			<table class="form-table">
				<tr>
					<th><label for="<?php echo $this->get_field_id( 'forum_tags_smallest' ); ?>"><?php _e('Smallest Font Size (px):', 'buddypress'); ?></label></th>
					<td><input id="<?php echo $this->get_field_id( 'forum_tags_smallest' ); ?>" name="<?php echo $this->get_field_name( 'forum_tags_smallest' ); ?>" type="text" value="<?php echo attribute_escape( $forum_tags_smallest ); ?>" size="4" /></td>
				</tr>
				<tr>
					<th><label for="<?php echo $this->get_field_id( 'forum_tags_largest' ); ?>"><?php _e('Largest Font Size (px):','bp-forums-extras') ?></label></th>
					<td><input name="<?php echo $this->get_field_name( 'forum_tags_largest' ); ?>" id="<?php echo $this->get_field_id( 'forum_tags_largest' ); ?>" type="text" value="<?php echo attribute_escape( $forum_tags_largest ); ?>" size="4" /></td>
				</tr>
				<tr>
					<th><label for="<?php echo $this->get_field_id( 'forum_tags_limit' ); ?>"><?php _e('Tag Limit','bp-forums-extras') ?></label></th>
					<td><input name="<?php echo $this->get_field_name( 'forum_tags_limit' ); ?>" id="<?php echo $this->get_field_id( 'forum_tags_limit' ); ?>" type="text" value="<?php echo attribute_escape( $forum_tags_limit ); ?>" size="4" /></td>
				</tr>
			</table>
	<?php
	}
}


function bp_forum_extras_tags_add_admin_screen() {
	global $bp;

	if ( !is_site_admin() )
		return false;

	?>
	<h4>Tag Index enabled</h4>
	<div class="description"><p>A simple larger tag display on the forums component page - includes a widget</p></div>
	<?php
}
add_action('bp_forum_extras_admin_screen','bp_forum_extras_tags_add_admin_screen');
?>