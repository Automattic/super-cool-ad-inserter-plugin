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

	global $scaip_period, $scaip_repetitions, $scaip_minimum_paragraphs;

	$paragraph_positions = array();
	$last_position = -1;
	$paragraph_end = "</p>";
	// @todo can that be a </p><p>?
	
	while ( stripos( $content, $paragraph_end, $last_position +1) !== false) {
		$last_position = stripos( $content, $paragraph_end, $last_position +1 ) +3; // what does the 3 mean?
		$paragraph_positions[] = $last_position;
		// @todo
		// can this be simplified to just go off of
		// $paragraph_positions[] = $last_position + 4 
		// ?
	}
	
	// Why do they filter the paragraph positions?
	foreach ( $paragraph_positions as $index => $paragraph_position ) {
		// some magic in here that determines if a paragraph is empty?
	}

	// If the total number of paragraphs is bigger than the minimum number of paragraphs
	if ( sizeof($paragraph_positions) >= $scaip_minimum_paragraphs ) {
		// If the total number of paragraphs is bigger than the 
		if ( sizeof($paragraph_positions) > $scaip_period ) {
			
			// now do the replacing
			
			//while ( $i <= sizeof($paragraph_positions)) {
			//	$paragraph_positions[$i]

			//	$i ++
			//}
			foreach ( $paragraph_positions as $index => $paragraph_position ) {
				// some magic in here that determines if a paragraph is empty?
				if ($paragraph_position >= strlen($content) - 1) {
					// It's the end of the post, do nothing

				} else {
					// It's in the middle of the post, do something
					$shortcode = "[scaip number=$index ]";
					$content = substr_replace($content, $shortcode, $paragraph_position + 1, 0);
					
					// Increment all following paragraph positions so that they're not off.
					foreach ( $paragraph_positions as $i => $pp ) {
						if ( $i > $index ) {
							$paragraph_positions[$i] = $pp + strlen($shortcode);
						}
					}
				}
			}
		}
	}

return $content;
}
add_filter('the_content', 'scaip_insert_shortcode');
