<?php

function bp_forum_extras_forumsdirectory_component() {
	global $bp;

	if ( $bp->forums->slug == $bp->current_component )
		return true;

	return false;
}

function bp_forum_extras_blogs_component() {
	global $bp;

	//why does is_blog_page return true within wpmu admin?
	if ( bp_core_is_multisite() && !(defined('WP_ADMIN') && WP_ADMIN) && ( $bp->current_component == $bp->blogs->slug || bp_is_blog_page() ) )
		return true;
		
	if ( bp_core_is_multisite() && (basename($_SERVER['PHP_SELF']) == 'wp-signup.php' || basename($_SERVER['PHP_SELF']) == 'register.php'  || basename($_SERVER['PHP_SELF']) == 'wp-register.php') )
		return true;

	return false;
}



//add a few more - 
function bp_forum_extras_allowed_tags( $tags ) {
	
	$new_tags = array('font','strike','center','u','blockquote','cite','pre','hr', 'ul', 'ol', 'li' );
	foreach ($new_tags as $tag) {
		$tags[$tag]=array();
	}
	$tags['font']['color'] = array();
	
	return $tags;
}

function bp_forum_extras_filter_allowedtags( ) {
	global $allowedtags;

	$forums_allowedtags = $allowedtags;
	$forums_allowedtags['span'] = array();
	$forums_allowedtags['span']['class'] = array();
	$forums_allowedtags['div'] = array();
	$forums_allowedtags['div']['class'] = array();
	$forums_allowedtags['div']['id'] = array();
	$forums_allowedtags['a']['class'] = array();
	$forums_allowedtags['br'] = array();
	$forums_allowedtags['p'] = array();
	$forums_allowedtags['code'] = array();
	$forums_allowedtags['blockquote'] = array();
	$forums_allowedtags['img'] = array();
	$forums_allowedtags['img']['src'] = array();
	$forums_allowedtags['img']['alt'] = array();
	$forums_allowedtags['img']['class'] = array();
	$forums_allowedtags['img']['width'] = array();
	$forums_allowedtags['img']['height'] = array();
	$forums_allowedtags['img']['class'] = array();
	$forums_allowedtags['img']['id'] = array();
	$forums_allowedtags['font']['color'] = array();

	return apply_filters( 'bp_forums_allowed_tags', $forums_allowedtags );
}

function _bbbp_forums_encode_bad_empty(&$text, $key, $preg) {
	if (strpos($text, '`') !== 0)
		$text = preg_replace("|&lt;($preg)\s*?/*?&gt;|i", '<$1 />', $text);
}

function _bbbp_forums_encode_bad_normal(&$text, $key, $preg) {
	if (strpos($text, '`') !== 0)
		$text = preg_replace("|&lt;(/?$preg)&gt;|i", '<$1>', $text);
}

function bbbp_forums_encode_bad( $text ) {
	$text = wp_specialchars( $text, ENT_NOQUOTES );

	$text = preg_split('@(`[^`]*`)@m', $text, -1, PREG_SPLIT_NO_EMPTY + PREG_SPLIT_DELIM_CAPTURE);

	$allowed = bp_forum_extras_filter_allowedtags();
	$empty = array( 'br' => true, 'hr' => true, 'input' => true, 'param' => true, 'area' => true, 'col' => true, 'embed' => true );

	foreach ( $allowed as $tag => $args ) {
		$preg = $args ? "$tag(?:\s.*?)?" : $tag;

		if ( isset( $empty[$tag] ) )
			array_walk($text, '_bbbp_forums_encode_bad_empty', $preg);
		else
			array_walk($text, '_bbbp_forums_encode_bad_normal', $preg);
	}

	return join('', $text);
}

function bp_forum_extras_is_first_post() {
	global $topic_template;

	if ( $topic_template->post->post_position == 1 ) {
		return true;
	} else {
		return false;
	}
}


//15 per_page is default for bp_has_forum_topic_posts - change below if you pass a different per_page to the loop
function bp_forum_extras_topic_last_post_link( $per_page = 15 ) {
	global $forum_template;

	$page = bp_forum_extras_get_page_number($forum_template->topic->topic_posts, $per_page);
	$page = (1 < $page) ? '?topic_page='. $page .'&num='. $per_page : '';
	
	return bp_get_the_topic_permalink() . $page ."#post-". $forum_template->topic->topic_last_post_id;
}
	
function bp_forum_extras_get_page_number( $item, $per_page = 15 ) {
	if ( !$per_page )
		return false;
		
	return intval( ceil( $item / $per_page ) ); // page 1 is the first page
}

function bp_forum_extras_topic_page_links( $per_page = 15, $args = null ) {
	echo bp_forum_extras_get_topic_page_links( $per_page, $args );
}
function bp_forum_extras_get_topic_page_links( $per_page = 15, $args = null ) {
	global $forum_template;

	$defaults = array(
		'show_all' => false,
		'end_size' => 3,
		'before' => ' ( ',
		'after' => ' ) '
	);

	$args = wp_parse_args( $args, $defaults );

	$_links = paginate_links(
		array(
			'base' => bp_get_the_topic_permalink() . '%_%',
			'format' => '?topic_page=%#%'.'&num='. $per_page,
			'total' => ceil( $forum_template->topic->topic_posts / $per_page ),
			'current' => 0,
			'show_all' => $args['show_all'],
			'end_size' => $args['end_size'],
			'type' => 'array'
		)
	);

	$links = $_links;

	if ( $links ) {
		if ( !$show_first ) {
			unset( $links[0] );
		}

		$r = '';
		if ( $args['before'] ) {
			$r .= $args['before'];
		}
		$r .= join(' ', $links);
		if ( $args['after'] ) {
			$r .= $args['after'];
		}
	}

	return $r;
}


function bp_forum_extras_the_topic_voices_count() {
	echo bp_forum_extras_get_the_topic_voices_count();
}
	function bp_forum_extras_get_the_topic_voices_count() {
		global $forum_template;

		if ( $forum_template->topic->voices_count == 1 )
			return apply_filters( 'bp_forum_extras_get_the_topic_voices_count', sprintf( __( '%d Voice', 'buddypress' ), $forum_template->topic->voices_count ) );
		else
			return apply_filters( 'bp_forum_extras_get_the_topic_voices_count', sprintf( __( '%d Voices', 'buddypress' ), $forum_template->topic->voices_count ) );
	}
	
	
function bp_forum_extras_activity_has_children() {
	global $activities_template;

	if ( $activities_template->activity->children )
		return true;
		
	return false;
}

function bp_forum_extras_last_poster_name() {
	echo bp_forum_extras_bp_get_last_poster_name();
}
	function bp_forum_extras_bp_get_last_poster_name() {
		global $forum_template;

		return bp_core_get_userlink( $forum_template->topic->topic_last_poster );
	}
?>