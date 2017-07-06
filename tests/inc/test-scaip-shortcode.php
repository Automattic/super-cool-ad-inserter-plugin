<?php

class ScaipShortcodeTestFunctions extends WP_UnitTestCase {

	function test_scaip_shortcode() {
		$ret = scaip_shortcode( array( 'number' => 4 ), '', '' );
		$this->assertRegExp( '/4/', $ret );
	}
}
