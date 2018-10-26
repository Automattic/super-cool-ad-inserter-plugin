<?php

/**
 * The SCAIP shortcode function
 *
 * @return HTML
 */
function scaip_shortcode( $atts = array(), $content = '', $tag = '' ) {
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
 * @since 0.1
 */
function scaip_shortcode_do_sidebar( $args ) {
	if ( isset( $args['number'] ) ) {
		echo '<aside class="scaip scaip-' . esc_attr( $args['number'] ) . '">';
		dynamic_sidebar( 'scaip-' . $args['number'] );
		echo '</aside>';
	}
}
add_action( 'scaip_shortcode', 'scaip_shortcode_do_sidebar' );
