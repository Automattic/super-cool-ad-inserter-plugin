<?php
/**
 * The [ad] shortcode and its related functions
 */

/**
 * The SCAIP shortcode function
 *
 * @param Array $atts Shortcode attributes or block properties.
 * @param String $content Shortcode wrapped text; not used in this shortcode.
 * @param String $tag The complete shortcode tag; not used in this shortcode.
 * @return HTML
 * @since 0.1
 */
function scaip_shortcode( $atts = array(), $content = '', $tag = '' ) {
	// This is the shortcode that disables doing shortcodes.
	if ( isset( $atts['no'] ) ) {
		return '';
	}

	ob_start();
	do_action( 'scaip_shortcode', $atts );
	$ret = ob_get_clean();
	return $ret;
}
add_shortcode( 'scaip', 'scaip_shortcode' );
add_shortcode( 'ad', 'scaip_shortcode' );

/**
 * Outputs the sidebar 'scaip-#' where # is the 'number' argument on the shortcode.
 *
 * To prevent this happening, decrease the number of ads that should be inserted to 0, remove the ad widgets form the sidebar, or remove_action('scaip_shortcode', 'scaip_shortcode_do_sidebar');
 *
 * @param Array $args Shortcode attributes or block properties.
 * @since 0.1
 */
function scaip_shortcode_do_sidebar( $args ) {
	if ( isset( $args['number'] ) ) {
		printf(
			'<aside class="scaip scaip-%1$s %2$s %3$s %4$s %5$s">',
			esc_attr( $args['number'] ),
			( isset( $args['align'] ) ) ? esc_attr( 'align' . $args['align'] ) : '',
			( isset( $args['class'] ) ) ? esc_attr( $args['class'] ) : '',
			( isset( $args['className'] ) ) ? esc_attr( $args['className'] ) : '',
			( isset( $args['customClassName'] ) ) ? esc_attr( $args['customClassName'] ) : ''
		);
		dynamic_sidebar( 'scaip-' . esc_attr( $args['number'] ) );
		echo '</aside>';
	}
}
add_action( 'scaip_shortcode', 'scaip_shortcode_do_sidebar' );
