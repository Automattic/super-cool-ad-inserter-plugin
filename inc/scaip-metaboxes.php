<?php
/**
 * Registers the scaip_prevent_shortcode_addition post meta and the block-editor
 * sidebar panel that toggles it on a per-post basis.
 *
 * @package WordPress
 * @subpackage SCAIP
 * @since 0.1
 */

/**
 * Auth callback used by register_post_meta for scaip_prevent_shortcode_addition.
 *
 * Anyone who can edit the post can toggle this meta via the REST API. This
 * matches the gating other Newspack-managed editor panels (ads, campaigns) use.
 *
 * @param bool   $allowed   Whether the user can edit the meta. Unused; we make
 *                          the determination ourselves.
 * @param string $meta_key  The meta key being modified. Unused.
 * @param int    $object_id The post being modified.
 * @param int    $user_id   The user attempting to write the meta.
 * @return bool Whether the write should be permitted.
 */
function scaip_prevent_shortcode_addition_auth_callback( $allowed, $meta_key, $object_id, $user_id ) {
	return user_can( $user_id, 'edit_post', $object_id );
}

register_post_meta(
	'post',
	'scaip_prevent_shortcode_addition',
	array(
		'type'          => 'boolean',
		'single'        => true,
		'show_in_rest'  => true,
		'default'       => false,
		'auth_callback' => 'scaip_prevent_shortcode_addition_auth_callback',
	)
);

/**
 * Enqueues the SCAIP document settings panel script in the block editor.
 *
 * Skips if the current screen isn't the block editor for a post. WordPress
 * already gates editor access on edit_post; the auth_callback above gates
 * REST writes, so no extra cap check is needed here.
 */
function scaip_enqueue_document_panel_assets() {
	$screen = get_current_screen();
	if ( ! $screen || ! $screen->is_block_editor() || 'post' !== $screen->id ) {
		return;
	}

	$plugin_dir = plugin_dir_path( SCAIP_PLUGIN_FILE );
	$plugin_url = plugin_dir_url( SCAIP_PLUGIN_FILE );
	$panel_path = 'assets/js/scaip-document-panel.js';

	wp_enqueue_script(
		'scaip-document-panel',
		$plugin_url . $panel_path,
		array(
			'wp-plugins',
			// Both wp-editor and wp-edit-post are required: panel.js falls back
			// from wp.editor.PluginDocumentSettingPanel (WP 6.6+) to
			// wp.editPost.PluginDocumentSettingPanel (older versions).
			'wp-editor',
			'wp-edit-post',
			'wp-element',
			'wp-components',
			'wp-i18n',
			'wp-core-data',
			'wp-data',
			'wp-dom-ready',
		),
		filemtime( $plugin_dir . $panel_path ),
		true
	);

	wp_localize_script(
		'scaip-document-panel',
		'scaipDocumentPanel',
		array(
			'start'              => get_option( 'scaip_settings_start', 3 ),
			'period'             => get_option( 'scaip_settings_period', 3 ),
			'repetitions'        => get_option( 'scaip_settings_repetitions', 2 ),
			'minimum_paragraphs' => get_option( 'scaip_settings_min_paragraphs', 6 ),
		)
	);
}
add_action( 'enqueue_block_editor_assets', 'scaip_enqueue_document_panel_assets' );
