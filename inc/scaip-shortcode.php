<?php

/**
 * The SCAIP shortcode function
 *
 * @return HTML
 */
function scaip_shortcode( $atts, $content, $tag) {
	if ( isset($atts['number'])) {
		$atts['number'] = "DAAAAAD!";
	}
	return "<p>Super Cool Ad Inserter Plugin was here! This is shortcode number " . $atts['number'] . ". Sincerely, the SCAIP Goat.</p>";
}
add_shortcode('scaip', 'scaip_shortcode');
