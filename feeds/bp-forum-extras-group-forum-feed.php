<?php
header('Content-Type: text/xml; charset=' . get_option('blog_charset'), true);
header('Status: 200 OK');

$title = apply_filters('the_title_rss', get_blog_option( BP_ROOT_BLOG, 'blogname' ) .' | '. $bp->groups->current_group->name );

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
$topics = array_slice($topics, 0, 15);
foreach ($topics as $bb_topic) : 
	$posts = bb_get_first_post($bb_topic->topic_id);
	$topiclink = $link .'topic/'. $bb_topic->topic_slug .'/';
	
	$posttitle = apply_filters( 'the_title_rss', get_topic_author( $bb_topic->topic_id ) .' on '. get_topic_title( $bb_topic->topic_id ) );

	
?>
		<item>
			<guid isPermaLink="false"><?php echo $bb_topic->topic_id; ?>@<?php echo $topiclink; ?></guid>
			<link><?php echo $topiclink; ?></link>
			<pubDate><?php echo mysql2date('D, d M Y H:i:s O', $bb_topic->topic_time, false); ?></pubDate>
			<title><?php echo $posttitle; ?></title>
			<dc:creator><?php echo apply_filters('the_author', get_topic_author( $bb_topic->topic_id ) ); ?></dc:creator>
			<description><?php echo apply_filters('comment_text_rss', get_post_text( $posts->post_id ) ); ?></description>
			
		</item>
<?php endforeach; ?>

	</channel>
</rss>
