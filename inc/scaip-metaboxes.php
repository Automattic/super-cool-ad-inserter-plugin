<?php
/**
 * A metabox that explains how many ads are inserted and where as well as how to override the default behavior on a per-post basis.
 *
 * @package WordPress
 * @subpackage SCAIP
 * @since 0.1
 */

/**
 * Generate the contents of the metabox.
 */
function scaip_how_to_shortcode_callback() {
	global $post;

	$scaip_start = get_option( 'scaip_settings_start', 3 );
	$scaip_period = get_option( 'scaip_settings_period', 3 );
	$scaip_repetitions = get_option( 'scaip_settings_repetitions', 2 );
	$scaip_minimum_paragraphs = get_option( 'scaip_settings_min_paragraphs', 6 );
	wp_nonce_field( 'scaip_metabox', 'scaip_metabox_nonce' );
	?>
	<p>
		<?php
			printf(
				esc_html__( 'By default, %1$s ads will be inserted in a post, beginning %2$s blocks after the beginning and every %3$s paragraphs after that. They will not appear if this post is shorter than %4$s paragraphs long.', 'scaip' ),
				esc_html( $scaip_repetitions ),
				esc_html( $scaip_start ),
				esc_html( $scaip_period ),
				esc_html( $scaip_minimum_paragraphs )
			);
		?>
	</p>
	<p>
		<?php
		echo wp_kses(
			__( 'If the automatic positioning causes problems for any given post, you can prevent automatic placement of the ads <a href="https://github.com/Automattic/super-cool-adinserter-plugin/blob/trunk/docs/display-settings.md">using a shortcode</a> or disable them completely by checking this box:', 'scaip' ),
			array(
				'a' => array(
					'href' => array(),
				),
			)
		);
		?>
	</p>
	<?php
	if ( current_user_can( 'edit_others_posts' ) ) {
		$checked = get_post_meta( $post->ID, 'scaip_prevent_shortcode_addition', true );
		echo '<p><label class="selectit"><input type="checkbox" value="true" name="scaip_prevent_shortcode_addition"' . checked( $checked, 1, false  ) . '> ' . esc_html__( 'Prevent automatic addition of ads to this post.', 'scaip' ) . '</label></p>';
	}
}

// Only register the meta box if the user is an editor or greater
add_action( 'add_meta_boxes', function() {
	if ( current_user_can( 'edit_others_posts' ) ) {
		add_meta_box( 'scaip_docs_and_options', __( 'Super Cool Ad Inserter', 'scaip' ), 'scaip_how_to_shortcode_callback', 'post', 'normal', 'low' );
	}
});

// But always register the meta.
register_meta( 'post', 'scaip_prevent_shortcode_addition', array( 'sanitize_callback' => 'scaip_prevent_shortcode_addition_sanitize' ) );

/**
 * Sanitization callback for saving the scaip_prevent_shortcode_addition post meta option.
 *
 * @param array $args the callback args.
 */
function scaip_prevent_shortcode_addition_sanitize( $args ) {
	$args = sanitize_text_field( $args );
	if ( 1 === intval( $args ) ) {
		$ret = $args;
	} else {
		$ret = false;
	}

	return $ret;
}

/**
 * Save the options set in the metabox.
 *
 * @param int    $post_id the post ID.
 * @param object $post the post object.
 */
function _scaip_meta_box_save( $post_id, $post ) {

	// Verify the nonce before proceeding.
	if ( ! isset( $_POST['scaip_metabox_nonce'] ) || ! wp_verify_nonce( $_POST['scaip_metabox_nonce'], 'scaip_metabox' ) ) {
		return false;
	}

	// Bail if we're doing an auto save.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// If our current user can't edit this post, bail.
	if ( ! current_user_can( 'edit_post', $post->ID ) ) {
		return;
	}

	$new_meta_value = ( isset( $_POST['scaip_prevent_shortcode_addition'] ) ? sanitize_html_class( wp_unslash( $_POST['scaip_prevent_shortcode_addition'] ) ) : '' );

	/*
	 * If the checkbox was checked, update the meta_value
	 * If the checkbox was unchecked, delete the meta_value
	 */
	if ( ! empty( $new_meta_value ) ) {
		add_post_meta( $post_id, 'scaip_prevent_shortcode_addition', 1, true );
	} else {
		delete_post_meta( $post_id, 'scaip_prevent_shortcode_addition' );
	}
}
add_action( 'save_post', '_scaip_meta_box_save', 10, 2 );
