<?php
header('Content-Type: text/xml; charset=' . get_option('blog_charset'), true);
header('Status: 200 OK');

$title = apply_filters('the_title_rss', get_blog_option( BP_ROOT_BLOG, 'blogname' ) .' | '. $bp->groups->current_group->name .' | '. get_topic_title( $topic->topic_id ) );

?>
<?php echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>'; ?>

<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom">

	<channel>
		<title><?php echo $title; ?></title>
		<atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
		<link><?php echo $link; ?></link>
		<pubDate><?php echo mysql2date('D, d M Y H:i:s O', $topic->topic_time, false); ?></pubDate>
		<generator>http://buddypress.org/?v=<?php echo BP_VERSION ?></generator>
		<language><?php echo get_option('rss_language'); ?></language>
		<description></description>
<?php 
$posts = array_slice($posts, 0, 15);

foreach ($posts as $bb_post) :

	$page = bp_forum_extras_get_page_number( $bb_post->post_position );
	$page = (1 < $page) ? '?topic_page='. $page .'&num=15' : '';
	$postlink = $link . $page ."#post-". $bb_post->post_id; 
	
	$postlink = apply_filters( 'link_rss', $postlink );
	$posttitle = apply_filters( 'the_title_rss', get_post_author( $bb_post->post_id ) .' on '. get_topic_title( $bb_post->topic_id ) );

	?>
		<item>
			<guid isPermaLink="false"><?php echo $bb_post->post_id; ?>@<?php echo $postlink; ?></guid>
			<title><?php echo $posttitle; ?></title>
			<link><?php echo $postlink; ?></link>
			<pubDate><?php echo mysql2date('D, d M Y H:i:s O', $bb_post->post_time, false); ?></pubDate>
			<dc:creator><?php echo apply_filters('the_author', get_post_author( $bb_post->post_id ) ); ?></dc:creator>
			<description><?php echo apply_filters('comment_text_rss', get_post_text( $bb_post->post_id ) ); ?></description>
		</item>
<?php endforeach; ?>

	</channel>
</rss>