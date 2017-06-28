<?php

/**
 * Short explanation of what the SCAIP shortcode is and how to use it in posts.
 */
function scaip_how_to_shortcode_callback( $post = null ) {
	$post = get_post( $post );

	$scaip_period = get_option( 'scaip_settings_period', 3 );
	$scaip_repetitions = get_option( 'scaip_settings_repetitions', 2 );
	$scaip_minimum_paragraphs = get_option( 'scaip_settings_min_paragraphs', 6 );

	?>
	<p>
		<?php
			printf(
				__( 'By default, %1$s ads will be inserted in a post, beginning %2$s paragraphs after the beginning and every %2$s paragraphs after that. They will not appear if this post is shorter than %3$s paragraphs long.', 'scaip' ),
				$scaip_repetitions,
				$scaip_period,
				$scaip_minimum_paragraphs
			);
		?>
	</p>
	<p><?php _e( 'If the automatic positioning is terrible, you can <a href="https://github.com/INN/super-cool-ad-inserter-plugin/blob/master/docs/display-settings.md">prevent automatic placement of the ads</a>.', 'scaip' );?></p>
	<?php

	// Only show the override option on posts.

	if ( $post != null && current_user_can( 'edit_others_posts' ) ) {
		$value = get_post_meta( $post->ID, 'scaip_prevent_shortcode_addition', true );
		?>
		<?php _e( '<p>If you want to disable automatic addition of ads, disable it here:</p>', 'scaip' ); ?>
		<p><input type='checkbox' class='checkbox' id='scaip_prevent_shortcode_addition' name='scaip_prevent_shortcode_addition' <?php checked( $value, 'on' ); ?> /><label for='scaip_prevent_shortcode_addition'><?php _e( 'Prevent automatic addition of ads to this post.', 'scaip' ); ?></label></p>

		<?php
	}
}

// Only register the meta box if the user is an editor or greater
add_action( 'add_meta_boxes', function() {
	if ( current_user_can( 'edit_others_posts' ) ) {
		add_meta_box( 'scaip_docs_and_options', __( 'Super Cool Ad Inserter', 'scaip' ), 'scaip_how_to_shortcode_callback', 'post', 'normal', 'low' );
	}
});

// But always register the meta.
register_meta( 'post', 'scaip_prevent_shortcode_addition', 'scaip_prevent_shortcode_addition_sanitize' );

/**
 * Sanitization callback for saving the scaip_prevent_shortcode_addition post meta option.
 */
function scaip_prevent_shortcode_addition_sanitize( $args ) {
	$args = sanitize_text_field( $args );
	if ( $args === 'on' ) {
		$ret = $args;
	} else {
		$ret = false;
	}

	return $ret;
}

/**
 * Save the options set in the metabox.
 */
function _scaip_meta_box_save( $post_id ) {
	global $post;

	// Bail if we're doing an auto save
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// if our current user can't edit this post, bail
	if ( ! current_user_can( 'edit_post' ) ) {
		return;
	}

	$value = $_POST['scaip_prevent_shortcode_addition'];

	if ( get_post_meta( $post->ID, 'scaip_prevent_shortcode_addition', false ) ) {
		update_post_meta( $post->ID, 'scaip_prevent_shortcode_addition', $value ); //if the custom field already has a value, update it
	} else {
		add_post_meta( $post->ID, 'scaip_prevent_shortcode_addition', $value );//if the custom field doesn't have a value, add the data
	}
}
add_action( 'save_post', '_scaip_meta_box_save' );
