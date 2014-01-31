<?php
/**
Plugin Name: County Web Blog RSS
Plugin URI: https://github.com/enishant
Description: Get RSS FEEDS for County Web Blog , shortcode [cwbrss rssurl="http://example.com/rss" feedcount="10" contentlength="100"]
Author: Nishant Vaity
Version: 1.0
Reference URL : http://web-profile.com.ua/wordpress/useful/rss-feed/
*/
include ('wp/wp-blog-header.php');	// Wordpress API for testing
function get_county_blog_rss($atts)
{

	extract( shortcode_atts( array(
		'rssurl' => 'http://web-profile.com.ua/wordpress/useful/rss-feed/',
		'feedcount' => 5,
		'contentlength' => 0,
		), $atts ) );
	
	$rss = fetch_feed( $rssurl );
	if ( is_wp_error($rss) ) 
	{
		if ( is_admin() || current_user_can('manage_options') ) 
		{
			$rss_err  = '<p>';
			$rss_err .= '<strong>RSS Error</strong>: ' . $rss->get_error_message();
			$rss_err .= '</p>';
		}
		return $rss_err;
	}
	
	if ( !$rss->get_item_quantity() ) 
	{
		$rss_err .=  '<p>Apparently, there is nothing happening on RSS!</p>';
		$rss->__destruct();
		unset($rss);
		return $rss_err;
	}
	
	$rss_feed_out = "\n<ul class='wp-rss-feed-plugin'>\n";
	if ( !isset($items) )
		$items = $feedcount;
	foreach ( $rss->get_items(0, $items) as $item ) {
		$publisher = '';
		$site_link = '';
		$link = '';
		$content = '';
		$date = '';
		$link = esc_url( strip_tags( $item->get_link() ) );
		$title = $item->get_title(); 
		$content = $item->get_content();
		$content = wp_html_excerpt($content, $contentlength);
		$rss_feed_out .= "\t<li>\n\t\t<a href='$link'>$title</a><br>\n\t\t$content\n\t</li>\n";
	}
	 
	$rss_feed_out .= "</ul>\n";
	$rss->__destruct();
	unset($rss);
	return $rss_feed_out;
}

function rss_stylesheet()
{
	echo '<style type="text/css">';
	echo '.wp-rss-feed-plugin {margin-top:10px;width:90%}';
	echo 'li {margin:10px 0px 0px auto; list-style:none;}';
	echo '</style>';
}

add_action('wp_head', 'rss_stylesheet');
add_shortcode('cwbrss','get_county_blog_rss');
add_filter('widget_text', 'do_shortcode', 11);

/* Testing Begins */
get_header();
echo do_shortcode('[cwbrss rssurl="http://www.countyweb.co.uk/blog/feed" feedcount="7" contentlength="200"]');
get_footer();
/* Testing Ends */
?>