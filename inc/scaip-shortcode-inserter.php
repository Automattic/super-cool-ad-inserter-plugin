<?php

/**
 * Adds the scaip shortcode to post after predetermined number of paragraphs
 *
 * Borrows heavily from the GPL3 plugin Ad-Inserter's function ai_generateAfterParagraph
 *
 * @link https://plugins.trac.wordpress.org/browser/ad-inserter/trunk/ad-inserter.php#L1474
 * @global $scaip_period, $scaip_repetitions, $scaip_minimum_paragraphs
 */
function scaip_insert_shortcode($content = '') {
	if (! in_the_loop() ) {
		return $content;
	}

	global $scaip_period, $scaip_repetitions, $scaip_minimum_paragraphs;

	$paragraph_positions = array();
	$last_position = -1;
	$paragraph_end = "</p>";

	while ( stripos( $content, $paragraph_end, $last_position +1) !== false) {
		$last_position = stripos( $content, $paragraph_end, $last_position +1 ) +3; // what does the 3 mean?
		$paragraph_positions[] = $last_position;
		// @todo
		// can this be simplified to just go off of
		// $paragraph_positions[] = $last_position + 4 
		// ?
	}

	// If the total number of paragraphs is bigger than the minimum number of paragraphs
	// It is assumed that $scaip_minimum_paragraphs > $scaip_period * $scaip_repetitions
	if ( sizeof($paragraph_positions) >= $scaip_minimum_paragraphs ) {

		// How many shortcodes have been added;
		$n = 0;

		while ( $i <= sizeof($paragraph_positions)) {
			// Modulo math to only output shortcode after $scaip_period closing paragraph tags.
			// +1 because of zero-based indexing
			if ( ($i + 1 ) % $scaip_period == 0 ) {

				// Increment number of shortcodes added, make a shortcode
				$n++;
				$shortcode = "[scaip number=$n ]";

				$content = substr_replace($content, $shortcode, $paragraph_positions[$i] + 1, 0);

				// Increase the position of later shortcodes by the length of the current shortcode
				foreach ( $paragraph_positions as $j => $pp ) {
					if ( $j > $i ) {
						$paragraph_positions[$j] = $pp + strlen($shortcode);
					}
				}
			}

			$i++;
		}
	}
	return $content;
}
add_filter('the_content', 'scaip_insert_shortcode');
