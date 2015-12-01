<?php
/*
Plugin Name: Super Cool Ad Inserter Plugin
Description: A simple way to insert widgets after the nth paragraph
Version: 0.1
Author: The INN Nerds
Author URI: http://github.com/inn
*/

// Plugin directory
define( 'SCAIP_PLUGIN_FILE', __FILE__ );

/**
 * Shortcode addition
 */
require_once(__DIR__ . '/inc/scaip-shortcode-inserter.php')
require_once(__DIR__ . '/inc/scaip-shortcode.php')



function scaip_deactivation() {
	// do nothing right now.
}
register_deactivation_hook(__FILE__, 'scaip_deactivation');
