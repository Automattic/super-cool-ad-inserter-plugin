<?php
/**
 * Functions for the automatic insertion of scaip shortcodes
 */

/**
 * Adds the scaip shortcode to post after predetermined number of paragraphs
 *
 * Borrows heavily from the GPL3 plugin Ad-Inserter's function ai_generateAfterParagraph
 *
 * Some questions in this code, because Ad-Inserter doesn't have inline docs at all.
 *
 * @link https://plugins.trac.wordpress.org/browser/ad-inserter/trunk/ad-inserter.php#L1474
 * @since 0.1
 * @param String $content The post content.
 * @return String The post content, plus shortcodes.
 */
function scaip_insert_shortcode( $content = '' ) {
	$scaip_start = (int) get_option( 'scaip_settings_start', 3 );
	$scaip_period = (int) get_option( 'scaip_settings_period', 3 );
	$scaip_repetitions = (int) get_option( 'scaip_settings_repetitions', 2 );
	$scaip_minimum_paragraphs = (int) get_option( 'scaip_settings_min_paragraphs', 6 );

	$paragraph_positions = array();
	$last_position = -1;
	$paragraph_end = '</p>';

	// if we don't have any <p> tags, we probably need to apply wpautop().
	if ( ! stripos( $content, $paragraph_end ) ) {
		$content = wpautop( $content );
	}
	while ( stripos( $content, $paragraph_end, $last_position + 1 ) !== false ) {
		// Get the position of the end of the next $paragraph_end.
		$last_position = stripos( $content, $paragraph_end, $last_position + 1 ) + 3; // what does the 3 mean?
		$paragraph_positions[] = $last_position;
	}

	// If the total number of paragraphs is at least the minimum number of paragraphs
	// and greater than the number of paragraphs before first insertion,
	// it is assumed that $scaip_minimum_paragraphs > $scaip_start + $scaip_period * $scaip_repetitions
	$number_of_paragraphs  = count( $paragraph_positions );
	$has_enough_paragraphs = $number_of_paragraphs > $scaip_minimum_paragraphs && $number_of_paragraphs > $scaip_start;

	if ( $has_enough_paragraphs ) {

		// Start outputting shortcodes only after hitting $scaip_start.
		$paragraph_positions = array_slice( $paragraph_positions, $scaip_start - 1 );

		// How many shortcodes have been added?
		$n = 1;

		// Safety check number: stores the position of the last insertion.
		$previous_position = 0;

		$i = 0;
		while ( $i < count( $paragraph_positions ) && $n <= $scaip_repetitions ) {
			// Modulo math to only output shortcode after $scaip_period closing paragraph tags.
			$insert_next_ad = 0 === $i % $scaip_period && isset( $paragraph_positions[ $i ] );

			if ( $insert_next_ad ) {

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

/**
 * Function to determine whether to insert the shortcode
 *
 * @uses scaip_insert_shortcode
 * @since 0.2
 */
function scaip_maybe_insert_shortcode( $content = '' ) {
	// Abort if this is not being called In The Loop.
	if ( ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}

	// Abort if this is not a normal post.
	global $wp_query;
	if (
		! isset( $wp_query->queried_object )
		|| !isset( $wp_query->queried_object->post_type )
		|| 'post' !== $wp_query->queried_object->post_type
	) {
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

	/*
	 * Filter to determine whether to apply the shortcode to the given post content
	 *
	 * Filters on 'scaip_whether_insert' should accept three arguments, and return a boolean true or false. The default value is true: apply the filter. Use this filter to tell SCAIP to not programmatically insert the ad in this instance.
	 *
	 * @param Bool $whether Whether to insert ads programmatically in this instance.
	 * @param String $content The post content.
	 * @param Mixed $queried_object `$wp_query->queried_object` in the current context.
	 *
	 * @since 0.2
	 * @link https://github.com/INN/super-cool-ad-inserter-plugin/issues/25
	 */
	if ( true !== apply_filters( 'scaip_whether_insert', true, $content, $wp_query->queried_object ) ) {
		return $content;
	}

	return scaip_insert_shortcode( $content );
}
add_filter( 'the_content', 'scaip_maybe_insert_shortcode', 10 );

/**
 * Remove the scaip_maybe_insert_shortcode filter on the_content when there are blocks
 *
 *
 * This is necessary because the filter scaip_maybe_insert_shortcode runs after do_blocks has run, which means there are no blocks left for has_blocks( $content ) to detect.
 * This action should run before Gutenberg's do_blocks filter, which runs at priority 7.
 * @link https://github.com/WordPress/gutenberg/blob/a696e508ad5d3566b447fc48f355e91953a17c4a/lib/blocks.php#L266
 *
 * @since 0.2
 * @see scaip_maybe_insert_shortcode
 * @uses has_blocks
 */
function scaip_maybe_remove_shortcode_inserter( $content ) {
	if ( function_exists( 'has_block' ) && has_block( 'super-cool-ad-inserter-plugin/scaip-sidebar', $content ) ) {
		remove_filter( 'the_content', 'scaip_maybe_insert_shortcode', 10 );
	}

	return $content;
}
add_filter( 'the_content', 'scaip_maybe_remove_shortcode_inserter', 5 );
