<?php
/*
Plugin Name: Super Cool Ad Inserter Plugin
Description: A simple way to insert widgets after the nth paragraph
Version: 0.1
Author: The INN Nerds
Author URI: http://github.com/inn
*/

function scaip_deactivation() {
	// do nothing right now.
}
register_deactivation_hook(__FILE__, 'scaip_deactivation');
