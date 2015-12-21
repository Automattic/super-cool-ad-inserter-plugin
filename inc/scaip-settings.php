<?php
/**
 * The settings page for the Super Cool Ad Inserter Plugin, and setting up the sidebars used by the shortcode to display ads.
 */

/**/

/**
 * Adds the SCAIP settings to the WordPress menu systen, under "Plugins."
 *
 * The capability 'manage_options' is limited to Super Admins on multisite WordPress, which might be a problem.
 */
function scaip_add_admin_menu() {
	add_submenu_page('plugins.php', 'Super Cool Ad Inserter Plugin', 'Ad Inserter', 'manage_options', 'scaip', 'scaip_admin_page');
}
add_action('admin_menu', 'scaip_add_admin_menu');

/**
 * Registers options for the plugin
 *
 * @since 0.2
 */
function scaip_register_settings() {
	add_settings_section('scaip-settings', 'Control how often the ads appear', 'scaip_settings_section_header', 'scaip');

	// id, title, callback, slug of the menu page it's displayed on, section of the settings page,
	add_settings_field('scaip_settings_period', 'Number of paragraphs before each insertion, and between insertions', 'scaip_settings_period', 'scaip', 'scaip-settings');
	add_settings_field('scaip_settings_repetitions', 'Number of times the ad should be inserted in a post', 'scaip_settings_repetitions', 'scaip', 'scaip-settings');
	add_settings_field('scaip_settings_min_paragraphs', 'Minimum number of paragraphs needed in a post to insert ads', 'scaip_settings_min_paragraphs', 'scaip', 'scaip-settings');

	// section, option name
	register_setting('scaip-settings', 'scaip_settings_period');
	register_setting('scaip-settings', 'scaip_settings_repetitions');
	register_setting('scaip-settings', 'scaip_settings_min_paragraphs');
}
add_action('admin_init', 'scaip_register_settings');


/*
 * Here begin callbacks for settings sections and settings fields.
 */

function scaip_settings_section_header($args) {
}

// This is the number of paragraphs after which SCAIP should insert a shortcode, counted in paragraphs since wither the beginning or the last time SCAIP inserted a shortcode
function scaip_settings_period($args) {
	$period = get_option('scaip_settings_period', 3);
	echo '<input name="scaip_settings_period" id="scaip_settings_period" type="number" value="' . $period . '" />';
}

// This is the number of times that SCAIP should insert a shortcode in a post
function scaip_settings_repetitions($args) {
	$repetitions = get_option('scaip_settings_repetitions', 2);
	echo '<input name="scaip_settings_repetitions" id="scaip_settings_repetitions" type="number" value="' . $repetitions . '" />';
}

// This is the minimum number of paragraphs in a post required for SCAIP to insert a shortcode in a post.
function scaip_settings_min_paragraphs($args) {
	$min_paragraphs = get_option('scaip_settings_min_paragraphs', 6);
	echo '<input name="scaip_settings_min_paragraphs" id="scaip_settings_min_paragraphs" type="number" value="' . $min_paragraphs . '" />';
	_e('<p>If a post has fewer than this number of paragraphs, ads will not be inserted.</p>','scaip');
}

function scaip_admin_page() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

	?>
	<div class="wrap scaip-admin">
		<h1>Super Cool Ad Inserter Plugin Options</h1>
		<form method="post" action="options.php">
		<?php
			settings_fields('scaip-settings');
			do_settings_sections('scaip');
			submit_button();
		?>
		</form>
		<?php
		echo sprintf(
			__('<p>To place ads or change ad placements, visit the <a href="%1$s">Widgets Settings</a>. For example, widgets in the first position will be in the "Inserted Ad Position 1" sidebar.</p>', 'scaip'),
			admin_url("widgets.php")
		);
		scaip_how_to_shortcode_callback() ?>
	</div>
	<?php
}

/**
 * Create a number of sidebars equal to scaip_settings_min_repetitions
 *
 * To create no widgets, reduce the "Number of times the ad should be inserted in a post" to 0.
 *
 * @since 0.1
 *
 */
function scaip_register_sidebars() {
	$sidebars = get_option('scaip_settings_repetitions', 2);
	$i=1;

	while ($i <= $sidebars) {
		register_sidebar(array(
			'name' => "Inserted Ad Position " . $i,
			'description' => "Widgets in this sidebar will be automatically inserted into posts. Please do not put more than one widget here.",
			'id' => "scaip-".$i,
			'before_widget' => '<aside id="%1$s" class="%2$s clearfix">',
			'after_widget' => '</aside>',
			'before_title' => '<h5 class="adtitle visuallyhidden">',
			'after_title' => '</h5>',
		));
		$i++;
	}
}
add_action( 'widgets_init', 'scaip_register_sidebars', 11); // 11 so these are added at the very bottom of the list.
