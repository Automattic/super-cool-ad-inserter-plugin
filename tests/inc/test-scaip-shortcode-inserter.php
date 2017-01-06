<?php

class scaip_shortcode_inserter_test_functions extends WP_UnitTestCase {

	function test_scaip_insert_shortcode() {
		// Create a long test post thing, please.
		// Check that it does nothing on posts outside The Loop

		$ret = scaip_insert_shortcode('');
		$this->assertEquals( $ret, '' );

		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

}
