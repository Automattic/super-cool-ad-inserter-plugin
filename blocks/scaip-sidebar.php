<?php
/**
 * Functions to register client-side assets (scripts and stylesheets) for the
 * Gutenberg block.
 *
 * @package super-cool-ad-inserter-plugin
 */

/**
 * Registers all block assets so that they can be enqueued through Gutenberg in
 * the corresponding context.
 *
 * @see https://wordpress.org/gutenberg/handbook/blocks/writing-your-first-block-type/#enqueuing-block-scripts
 */
function scaip_sidebar_block_init() {
	if ( ! function_exists( 'register_block_type' ) ) {
		return false;
	}

	$dir = dirname( __FILE__ );

	$block_js = 'scaip-sidebar/block.js';
	wp_register_script(
		'scaip-sidebar-block-editor',
		plugins_url( $block_js, __FILE__ ),
		array(
			'wp-blocks',
			'wp-i18n',
			'wp-element',
			'wp-components',
		),
		filemtime( "$dir/$block_js" )
	);

	// Provide SCAIP's settings to the editor JS
	$scaip_settings = array(
		'start' => get_option( 'scaip_settings_start', 3 ),
		'period' => get_option( 'scaip_settings_period', 3 ),
		'repetitions' => get_option( 'scaip_settings_repetitions', 2 ),
		'minimum_paragraphs' => get_option( 'scaip_settings_min_paragraphs', 6 ),
		'sidebar_disabled' => scaip_is_sidebar_disabled(),
	);
	wp_localize_script(
		'scaip-sidebar-block-editor',
		'scaip',
		$scaip_settings
	);

	register_block_type( 'super-cool-ad-inserter-plugin/scaip-sidebar', array(
		'editor_script' => 'scaip-sidebar-block-editor',
		'editor_style'  => 'scaip-sidebar-block-editor',
		'style'         => 'scaip-sidebar-block',
		'render_callback' => 'scaip_sidebar_block_render',
		'attributes' => array(
			'number' => array(
				'type' => 'string',
			),
			'align' => array(
				'type' => 'string',
			),
			'class' => array(
				'type' => 'string',
			),
		),
	) );
}
add_action( 'init', 'scaip_sidebar_block_init' );

/**
 * Renders the block on front-end.
 */
function scaip_sidebar_block_render( $attrs ) {
	if ( scaip_is_sidebar_disabled() ) {
		return;
	}
	return scaip_shortcode( $attrs );
}
