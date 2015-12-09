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
 * Dummy test function that does not harm users' content, outputs an HTML comment.
 *
 * To remove this action, remove_action('scaip_shortcode', 'scaip_shortcode_test_comment');
 */
function scaip_shortcode_test_comment($args) {
	echo "<!-- SCAIP was here, with these arguments: ";
	echo var_dump($args);
	echo "-->";
}
add_action('scaip_shortcode', 'scaip_shortcode_test_comment');
