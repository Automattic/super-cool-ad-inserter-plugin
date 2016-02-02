<?php

/**
 * Adds the scaip shortcode to post after predetermined number of paragraphs
 *
 * Borrows heavily from the GPL3 plugin Ad-Inserter's function ai_generateAfterParagraph
 *
 * Some questions in this code, because Ad-Inserter doesn't have inline docs at all.
 *
 * @link https://plugins.trac.wordpress.org/browser/ad-inserter/trunk/ad-inserter.php#L1474
 */
function scaip_insert_shortcode($content = '') {
	// abort if this is not being called In The Loop.
	if ( !in_the_loop() || !is_main_query() ) {
		return $content;
	}

	// abort if this is not a normal post
	global $wp_query;
	if ( $wp_query->queried_object->post_type !== 'post' ) {
		return $content;
	}

	/*
	 * Abort if this post has the option set to not add ads.
	 */
	if ( get_post_meta($wp_query->queried_object->ID, 'scaip_prevent_shortcode_addition', true) === 'on') {
		return $content;
	}

	/*
	 * Check that there isn't a line starting with `[ad`. If there is, abort! The content must be passed to the shortcode parser without adding more shortcodes. The user may have set a shortcode manually or set the `[ad no]` shortcode.
	 */
	if ( preg_match( "/^\[ad/m", $content )) {
		return $content;
	}
	// Support for development-era `[scaip` shortcode.
	if ( preg_match( "/^\[scaip/m", $content )) {
		return $content;
	}

	$scaip_period = get_option('scaip_settings_period', 3);
	$scaip_repetitions = get_option('scaip_settings_repetitions', 2);
	$scaip_minimum_paragraphs = get_option('scaip_settings_min_paragraphs', 6);

	$paragraph_positions = array();
	$last_position = -1;
	$paragraph_end = "</p>";

	while ( stripos( $content, $paragraph_end, $last_position +1) !== false) {
		// Get the position of the end of the next $paragraph_end.
		$last_position = stripos( $content, $paragraph_end, $last_position +1 ) +3; // what does the 3 mean?
		$paragraph_positions[] = $last_position;
		// Can this be simplified to just go off of: ?
		//     $paragraph_positions[] = $last_position + 4
		//
		// Maybe? 1 + 3 is the length of '</p>', and putting it in the offset argument for strpos() would
		// make the strpos start looking for the opening '<' after the ending '>' instead of in the middle of the '</p>'.
		// Or it might not. Not going to mess with this today.
	}

	// If the total number of paragraphs is bigger than the minimum number of paragraphs
	// It is assumed that $scaip_minimum_paragraphs > $scaip_period * $scaip_repetitions
	if ( sizeof($paragraph_positions) >= $scaip_minimum_paragraphs ) {

		// How many shortcodes have been added;
		$n = 1;
		
		// Safety check number: stores the position of the last insertion
		$previous_position = 0;

		$i = 0;
		while ( $i <= sizeof($paragraph_positions) && $n <= $scaip_repetitions ) {
			// Modulo math to only output shortcode after $scaip_period closing paragraph tags.
			// +1 because of zero-based indexing
			if ( ($i + 1 ) % $scaip_period == 0 && isset( $paragraph_positions[$i] ) ) {

				// make a shortcode using the number of the shorcode that will be added.
				$shortcode = "[ad number=$n ]";

				$position = $paragraph_positions[$i] + 1;

				// Safety check:
				// If the position we're adding the shortcode is at a lower point in the story than the position we're adding,
				// Then something has gone wrong and we should insert no more shortcodes.
				if ( $position > $previous_position ) {
					$content = substr_replace($content, $shortcode, $paragraph_positions[$i] + 1, 0);

					// Increase the saved last position.
					$previous_position = $position;

					// Increment number of shortcodes added to the post
					$n++;
				}

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
