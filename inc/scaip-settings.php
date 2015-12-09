<?php
/**
 * The settings page for the Super Cool Ad Inserter Plugin.
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
 * Here being callbacks for settings sections and settings fields.
 */

function scaip_settings_section_header($args) {
	echo '<p>This is a test paragraph.</p>';
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
	echo '<p>If a post has fewer than this number of paragraphs, ads will not be inserted.</p>';
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
		<hr/>
		<p>Insert explanation of shortcodes here, possibly as a function that also gets inserted in the post editor as a metabox.</p>
		<p>Insert link to docs on creating callbacks here.</p>
	</div>
	<?php
}
