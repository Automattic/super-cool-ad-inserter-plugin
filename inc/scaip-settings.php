<?php
/**
 * The settings page for the Super Cool Ad Inserter Plugin, and setting up the sidebars used by the shortcode to display ads.
 *
 * @package WordPress
 * @subpackage SCAIP
 * @since 0.1
 */

/**
 * Adds the SCAIP settings to the WordPress menu systen, under "Settings."
 *
 * The capability 'manage_options' is limited to Super Admins on multisite WordPress, which might be a problem.
 */
function scaip_add_admin_menu() {
	add_submenu_page( 'options-general.php', 'Super Cool Ad Inserter Plugin', 'Ad Inserter', 'manage_options', 'scaip', 'scaip_admin_page' );
}
add_action( 'admin_menu', 'scaip_add_admin_menu' );

/**
 * Registers options for the plugin
 */
function scaip_register_settings() {
	add_settings_section( 'scaip-settings', 'Control how often the ads appear', 'scaip_settings_section_header', 'scaip' );

	add_settings_field( 'scaip_settings_period', 'Number of paragraphs before each insertion, and between insertions', 'scaip_settings_period', 'scaip', 'scaip-settings' );
	add_settings_field( 'scaip_settings_repetitions', 'Number of times the ad should be inserted in a post', 'scaip_settings_repetitions', 'scaip', 'scaip-settings' );
	add_settings_field( 'scaip_settings_min_paragraphs', 'Minimum number of paragraphs needed in a post to insert ads', 'scaip_settings_min_paragraphs', 'scaip', 'scaip-settings' );

	register_setting( 'scaip-settings', 'scaip_settings_period' );
	register_setting( 'scaip-settings', 'scaip_settings_repetitions' );
	register_setting( 'scaip-settings', 'scaip_settings_min_paragraphs' );
}
add_action( 'admin_init', 'scaip_register_settings' );

/*
 * Here begin callbacks for settings sections and settings fields.
 */

/**
 * The section header has no additional explanatory text.
 *
 * @param array $args the callback args.
 */
function scaip_settings_section_header( $args ) {
	return false;
}

/**
 * The number of paragraphs after which SCAIP should insert a shortcode,
 * counted in paragraphs since either the beginning or the last time SCAIP inserted a shortcode.
 *
 * @param array $args the callback args.
 */
function scaip_settings_period( $args ) {
	$period = get_option( 'scaip_settings_period', 3 );
	echo '<input name="scaip_settings_period" id="scaip_settings_period" type="number" value="' . esc_attr( $period ) . '" />';
}

/**
 * The number of times that SCAIP should insert a shortcode in a post.
 *
 * @param array $args the callback args.
 */
function scaip_settings_repetitions( $args ) {
	$repetitions = get_option( 'scaip_settings_repetitions', 2 );
	echo '<input name="scaip_settings_repetitions" id="scaip_settings_repetitions" type="number" value="' . esc_attr( $repetitions ) . '" />';
}

/**
 * The minimum number of paragraphs in a post required for SCAIP to insert a shortcode in a post.
 *
 * @param array $args the callback args.
 */
function scaip_settings_min_paragraphs( $args ) {
	$min_paragraphs = get_option( 'scaip_settings_min_paragraphs', 6 );
	echo '<input name="scaip_settings_min_paragraphs" id="scaip_settings_min_paragraphs" type="number" value="' . esc_attr( $min_paragraphs ) . '" />';
	?>
	<p>
		<?php esc_html_e( 'If a post has fewer than this number of paragraphs, ads will not be inserted.','scaip' ); ?>
	</p>
	<?php
}

/**
 * Generate the html for the admin page.
 */
function scaip_admin_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'scaip' ) );
	}

	?>
	<div class="wrap scaip-admin">
		<h1><?php esc_html_e( 'Super Cool Ad Inserter Plugin Options', 'scaip' ); ?></h1>
		<form method="post" action="options.php">
		<?php
			settings_fields( 'scaip-settings' );
			do_settings_sections( 'scaip' );
			submit_button();
		?>
		</form>
		<?php
		echo sprintf(
			wp_kses(
				__( '<p>To place ads or change ad placements, visit the <a href="%1$s">Widgets Settings</a>. For example, widgets in the first position will be in the "Inserted Ad Position 1" sidebar.</p>', 'scaip' ),
				array(
					'p' => array(),
					'a' => array(
						'href' => array(),
					),
				)
			),
			esc_url( admin_url( 'widgets.php' ) )
		);
		?>
	</div>
	<?php
}

/**
 * Create a number of sidebars equal to scaip_settings_min_repetitions
 *
 * To create no widgets, reduce the "Number of times the ad should be inserted in a post" to 0.
 */
function scaip_register_sidebars() {
	$sidebars = get_option( 'scaip_settings_repetitions', 2 );
	$i = 1;

	while ( $i <= $sidebars ) {
		register_sidebar(array(
			'name' => 'Inserted Ad Position ' . $i,
			'description' => __( 'Widgets in this sidebar will be automatically inserted into posts. Please do not put more than one widget here.', 'scaip' ),
			'id' => 'scaip-' . $i,
			'before_widget' => '<aside id="%1$s" class="%2$s clearfix">',
			'after_widget' => '</aside>',
			'before_title' => '<h5 class="adtitle">',
			'after_title' => '</h5>',
		));
		$i++;
	}
}
add_action( 'widgets_init', 'scaip_register_sidebars', 11 ); // 11 so these are added at the very bottom of the list.
