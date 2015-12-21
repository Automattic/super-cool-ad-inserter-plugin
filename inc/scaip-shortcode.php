<?php

/**
 * The SCAIP shortcode function
 *
 * @return HTML
 */
function scaip_shortcode( $atts, $content, $tag) {
	if ( isset($atts['no'])) {
		return '';
	}
	ob_start();
	do_action('scaip_shortcode', $atts);
	$ret = ob_get_clean();
	return $ret;
}
add_shortcode('scaip', 'scaip_shortcode');

/**
 * Dummy test function that outputs the shortcode's attributes in an HTML comment.
 *
 * To remove this action, remove_action('scaip_shortcode', 'scaip_shortcode_test_comment');
 * @global bool WP_DEBUG whether this is running as debug or not.
 */
function scaip_shortcode_test_comment($args) {
	if ( WP_DEBUG ) {
		echo "<!-- SCAIP was here, with these arguments: ";
		echo var_dump($args);
		echo "-->";
	}
}
add_action('scaip_shortcode', 'scaip_shortcode_test_comment');

/**
 * Outputs the sidebar 'scaip-#' where # is the 'number' argument on the shortcode.
 *
 * To prevent this happening, decrease the number of ads that should be inserted to 0, remove the ad widgets form the sidebar, or remove_action('scaip_shortcode', 'scaip_shortcode_do_sidebar');
 * @since 0.1
 */
function scaip_shortcode_do_sidebar($args) {
	if ( isset($args['number']) ) {
		dynamic_sidebar('scaip-' . $args['number']);
	}
}
add_action('scaip_shortcode', 'scaip_shortcode_do_sidebar');
