<?php
/**
 * Functions for the automatic insertion of scaip shortcodes
 */

/**
 * Adds the scaip shortcode to post after predetermined number of blocks.
 *
 * Borrows heavily from Newspack Campaigns.
 *
 * @since 0.1
 * @param String $content The post content.
 * @return String The post content, plus shortcodes.
 */
function scaip_insert_shortcode( $content = '' ) {
	$scaip_start          = (int) get_option( 'scaip_settings_start', 3 );
	$scaip_period         = (int) get_option( 'scaip_settings_period', 3 );
	$scaip_repetitions    = (int) get_option( 'scaip_settings_repetitions', 2 );
	$scaip_minimum_blocks = (int) get_option( 'scaip_settings_min_paragraphs', 6 );

	$inserted_shortcode_index = 1;
	$block_index              = 1;

	// For certain types of blocks, their innerHTML is not a good representation of the length of their content.
	// For example, slideshows may have an arbitrary amount of slide content, but only show one slide at a time.
	// For these blocks, let's ignore their length for purposes of inserting prompts.
	$blacklisted_blocks = [ 'jetpack/slideshow', 'newspack-blocks/carousel', 'newspack-popups/single-prompt' ];
	$parsed_blocks      = parse_blocks( $content );

	$has_a_classic_block = false;

	$total_length = 0;

	// Compute total length of the block-based content.
	foreach ( $parsed_blocks as $block ) {
		if ( ! in_array( $block['blockName'], $blacklisted_blocks ) ) {
			$is_classic_block = null === $block['blockName'] || 'core/freeform' === $block['blockName']; // Classic block doesn't have a block name.
			$block_content    = $is_classic_block ? force_balance_tags( wpautop( $block['innerHTML'] ) ) : $block['innerHTML'];
			$block_length     = strlen( wp_strip_all_tags( $block_content ) );
			if ( $is_classic_block && 0 < $block_length ) {
				$has_a_classic_block = true;
			}
			$total_length += $block_length;
		} else {
			// Give blacklisted blocks a length so that prompts at 0% can still be inserted before them.
			$total_length++;
		}
	}

	if ( ! $has_a_classic_block && $scaip_minimum_blocks > count( $parsed_blocks ) ) {
		return $content;
	}

	$pos    = 0;
	$output = '';

	foreach ( $parsed_blocks as $block ) {
		$is_empty = empty( trim( $block['innerHTML'] ) );
		if ( $is_empty ) {
			continue;
		}
		$is_classic_block = null === $block['blockName']; // Classic block doesn't have a block name.

		// Classic block content: insert prompts between block-level HTML elements.
		if ( $is_classic_block ) {
			$classic_content = force_balance_tags( wpautop( $block['innerHTML'] ) ); // Ensure we have paragraph tags and valid HTML.
			if ( 0 === strlen( wp_strip_all_tags( $classic_content ) ) ) {
				continue;
			}
			$positions     = [];
			$last_position = -1;
			$block_endings = [ // Block-level elements eligble for prompt insertion.
				'</p>',
				'</ol>',
				'</ul>',
				'</h1>',
				'</h2>',
				'</h3>',
				'</h4>',
				'</h5>',
				'</h6>',
				'</div>',
				'</figure>',
				'</aside>',
				'</dl>',
				'</pre>',
				'</section>',
				'</table>',
			];

			// Parse the classic content string by block endings.
			foreach ( $block_endings as $block_ending ) {
				$last_position = -1;
				while ( stripos( $classic_content, $block_ending, $last_position + 1 ) ) {
					// Get the position of the end of the next $block_ending.
					$last_position = stripos( $classic_content, $block_ending, $last_position + 1 ) + strlen( $block_ending );
					$positions[]   = $last_position;
				}
			}

			sort( $positions, SORT_NUMERIC );
			$last_position = 0;

			// Insert prompts between block-level elements.
			foreach ( $positions as $position_index => $position ) {
				if (
					scaip_should_insert(
						$scaip_start,
						$position_index + 1,
						$inserted_shortcode_index,
						$scaip_repetitions,
						$scaip_period
					)
				) {
					$output .= scaip_generate_shortcode( $inserted_shortcode_index );
					$inserted_shortcode_index++;
				}

				$output       .= substr( $classic_content, $last_position, $position - $last_position );
				$last_position = $position;
			}

			$pos += strlen( $classic_content );
			continue;
		}

		// Regular block content: insert prompts between blocks.
		if ( ! in_array( $block['blockName'], $blacklisted_blocks ) ) {
			$pos += strlen( wp_strip_all_tags( $block['innerHTML'] ) );
		} else {
			$pos++;
		}

		if (
			scaip_should_insert(
				$scaip_start,
				$block_index,
				$inserted_shortcode_index,
				$scaip_repetitions,
				$scaip_period
			)
		) {
			$output .= scaip_generate_shortcode( $inserted_shortcode_index );
			$inserted_shortcode_index++;
		}

		$block_content = render_block( $block );
		$output       .= $block_content;
		$block_index++;
	}

	return $output;
}

/**
 * Generate shortcode markup.
 *
 * @param string $index Shortcode index.
 */
function scaip_generate_shortcode( $index ) {
	return '<!-- wp:shortcode -->[ad number="' . $index . '"]<!-- /wp:shortcode -->';
}

/**
 * Should shortcode be inserted?
 *
 * @param number $start Min. index to insert at.
 * @param number $block_index Current block index.
 * @param number $insertion_index Current insertion index.
 * @param number $repetitions Max. no. of insertions.
 * @param number $period Period between insertions.
 */
function scaip_should_insert( $start, $block_index, $insertion_index, $repetitions, $period ) {
	return ( $start < $block_index
	&& $insertion_index <= $repetitions
	&& (
		// First insertion should not take period into account.
		1 === $insertion_index
		||
		0 === ( $block_index + $start ) % $period
	) );
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
		|| ! isset( $wp_query->queried_object->post_type )
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
	 * @link https://github.com/Automattic/super-cool-adinserter-plugin/issues/25
	 */
	if ( true !== apply_filters( 'scaip_whether_insert', true, $content, $wp_query->queried_object ) ) {
		return $content;
	}

	return scaip_insert_shortcode( $content );
}
add_filter( 'the_content', 'scaip_maybe_insert_shortcode', 1 );

/**
 * Remove the scaip_maybe_insert_shortcode filter on the_content when there are blocks
 *
 * This is necessary because the filter scaip_maybe_insert_shortcode runs after do_blocks has run, which means there are no blocks left for has_blocks( $content ) to detect.
 * This action should run before Gutenberg's do_blocks filter, which runs at priority 7.
 *
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
