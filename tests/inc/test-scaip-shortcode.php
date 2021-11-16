<?php

class ScaipShortcodeTestFunctions extends WP_UnitTestCase {

	function test_scaip_shortcode() {
		$scaip_index = 1;
		$text        = 'Ad ' . $scaip_index;
		add_action(
			'scaip_shortcode',
			function ( $args ) {
				echo 'Ad ' . esc_attr( $args['number'] );
			}
		);
		$ret = scaip_shortcode( array( 'number' => $scaip_index ), '', '' );
		$this->assertRegExp( "/{$text}/", $ret );
	}
}
