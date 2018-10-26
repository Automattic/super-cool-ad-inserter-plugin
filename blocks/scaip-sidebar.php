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
	$dir = dirname( __FILE__ );

	$block_js = 'scaip-sidebar/block.js';
	wp_register_script(
		'scaip-sidebar-block-editor',
		plugins_url( $block_js, __FILE__ ),
		array(
			'wp-blocks',
			'wp-i18n',
			'wp-element',
		),
		filemtime( "$dir/$block_js" )
	);

	// Provide SCAIP's settings to the editor JS
	$scaip_settings = array(
		'period' => get_option( 'scaip_settings_period', 3 ),
		'repetitions' => get_option( 'scaip_settings_repetitions', 2 ),
		'minimum_paragraphs' => get_option( 'scaip_settings_min_paragraphs', 6 ),
	);
	wp_localize_script(
		'scaip-sidebar-block-editor',
		'scaip',
		$scaip_settings
	);

	$editor_css = 'scaip-sidebar/editor.css';
	wp_register_style(
		'scaip-sidebar-block-editor',
		plugins_url( $editor_css, __FILE__ ),
		array(
			'wp-blocks',
		),
		filemtime( "$dir/$editor_css" )
	);

	register_block_type( 'super-cool-ad-inserter-plugin/scaip-sidebar', array(
		'editor_script' => 'scaip-sidebar-block-editor',
		'editor_style'  => 'scaip-sidebar-block-editor',
		'style'         => 'scaip-sidebar-block',
		'render_callback' => 'scaip_shortcode',
	) );
}
add_action( 'init', 'scaip_sidebar_block_init' );