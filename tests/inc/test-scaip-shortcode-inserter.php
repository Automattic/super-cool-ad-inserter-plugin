<?php

class ScaipShortcodeInserterTestFunctions extends WP_UnitTestCase {

	function test_scaip_insert_shortcode() {
		// Create a long test post thing, please.
		// Check that it does nothing on posts outside The Loop

		$ret = scaip_insert_shortcode( '' );
		$this->assertEquals( $ret, '' );

		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

	function test_scaip__maybe_insert_shortcode() {
		// Check that it does nothing on posts outside The Loop
		// check that the filter works
		// check that it doesn't work on posts of other post tyles

		$ret = scaip_maybe_insert_shortcode( '' );
		$this->assertEquals( $ret, '' );

		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

}
