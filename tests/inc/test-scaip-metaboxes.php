<?php
/**
 * Tests for the SCAIP metaboxes / sidebar panel module.
 *
 * @package super-cool-ad-inserter-plugin
 */

/**
 * Test the auth callback used by register_post_meta for
 * scaip_prevent_shortcode_addition.
 *
 * The function under test, scaip_prevent_shortcode_addition_auth_callback(),
 * is defined in inc/scaip-metaboxes.php.
 */
class ScaipMetaboxesTestFunctions extends WP_UnitTestCase {

	/**
	 * An Author can edit their own posts, so the meta auth callback must
	 * allow REST writes for a post they authored.
	 */
	public function test_auth_callback_allows_author_on_own_post() {
		$author_id = $this->factory->user->create( array( 'role' => 'author' ) );
		$post_id   = $this->factory->post->create( array( 'post_author' => $author_id ) );

		$result = scaip_prevent_shortcode_addition_auth_callback(
			false,
			'scaip_prevent_shortcode_addition',
			$post_id,
			$author_id
		);

		$this->assertTrue( $result );
	}

	/**
	 * Subscribers cannot edit any post, so the meta auth callback must
	 * deny their REST writes.
	 */
	public function test_auth_callback_denies_subscriber() {
		$subscriber_id = $this->factory->user->create( array( 'role' => 'subscriber' ) );
		$post_id       = $this->factory->post->create();

		$result = scaip_prevent_shortcode_addition_auth_callback(
			false,
			'scaip_prevent_shortcode_addition',
			$post_id,
			$subscriber_id
		);

		$this->assertFalse( $result );
	}
}
