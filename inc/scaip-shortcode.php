<?php

/**
 * The SCAIP shortcode function
 *
 * @return HTML
 */
function scaip_shortcode() {
	return "<p>Super Cool Ad Inserter Plugin was here! Sincerely, the SCAIP Goat.</p>";
}
add_shortcode('scaip', 'scaip_shortcode');
