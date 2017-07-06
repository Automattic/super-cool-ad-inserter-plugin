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
function scaip_insert_shortcode( $content = '' ) {
	// Abort if this is not being called In The Loop.
	if ( ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}

	// Abort if this is not a normal post.
	global $wp_query;
	if ( 'post' !== $wp_query->queried_object->post_type ) {
		return $content;
	}

	/*
	 * Abort if this post has the option set to not add ads.
	 */
	$skip = get_post_meta( $wp_query->queried_object->ID, 'scaip_prevent_shortcode_addition', true );
	/*
	 * Usually the meta field won't exist unless the checkbox to skip ads on any given post is checked.
	 * An older version of the plugin saved it even when the box wasn't checked
	 * so we need an extra check here for backwards compatibility.
	 * the previous version saved the meta value as "on" if checked and empty if not.
	 */
	if ( ! empty( $skip ) || 'on' === $skip ) {
		return $content;
	}

	/*
	 * If we have a manual shortcode, bail.
	 * (scaip was the older shortcode, retained here for backwards compatibility)
	 */
	if ( has_shortcode( $content, 'ad' ) || has_shortcode( $content, 'scaip' ) ) {
		return $content;
	}

	$scaip_period = get_option( 'scaip_settings_period', 3 );
	$scaip_repetitions = get_option( 'scaip_settings_repetitions', 2 );
	$scaip_minimum_paragraphs = get_option( 'scaip_settings_min_paragraphs', 6 );

	$paragraph_positions = array();
	$last_position = -1;
	$paragraph_end = '</p>';

	// if we don't have an <p> tags, we probably need to apply wpautop().
	if ( ! stripos( $content, $paragraph_end ) ) {
		$content = wpautop( $content );
	}
	while ( stripos( $content, $paragraph_end, $last_position + 1 ) !== false ) {
		// Get the position of the end of the next $paragraph_end.
		$last_position = stripos( $content, $paragraph_end, $last_position + 1 ) + 3; // what does the 3 mean?
		$paragraph_positions[] = $last_position;
	}

	// If the total number of paragraphs is bigger than the minimum number of paragraphs
	// It is assumed that $scaip_minimum_paragraphs > $scaip_period * $scaip_repetitions
	if ( count( $paragraph_positions ) >= $scaip_minimum_paragraphs ) {

		// How many shortcodes have been added?
		$n = 1;

		// Safety check number: stores the position of the last insertion.
		$previous_position = 0;

		$i = 0;
		while ( $i < count( $paragraph_positions ) && $n <= $scaip_repetitions ) {
			// Modulo math to only output shortcode after $scaip_period closing paragraph tags.
			// +1 because of zero-based indexing.
			if ( ( $i + 1 ) % 0 === $scaip_period && isset( $paragraph_positions[ $i ] ) ) {

				// make a shortcode using the number of the shorcode that will be added.
				// Using "" here so we can interpolate the variable.
				$shortcode = "[ad number=$n ]";

				$position = $paragraph_positions[ $i ] + 1;

				// Safety check:
				// If the position we're adding the shortcode is at a lower point in the story than the position we're adding,
				// Then something has gone wrong and we should insert no more shortcodes.
				if ( $position > $previous_position ) {
					$content = substr_replace( $content, $shortcode, $paragraph_positions[ $i ] + 1, 0 );

					// Increase the saved last position.
					$previous_position = $position;

					// Increment number of shortcodes added to the post.
					$n++;
				}

				// Increase the position of later shortcodes by the length of the current shortcode.
				foreach ( $paragraph_positions as $j => $pp ) {
					if ( $j > $i ) {
						$paragraph_positions[ $j ] = $pp + strlen( $shortcode );
					}
				}
			}

			$i++;
		}
	}
	return $content;
}
add_filter( 'the_content', 'scaip_insert_shortcode', 10 );
