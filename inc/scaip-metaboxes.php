<?php

/**
 * Short explanation of what the SCAIP shortcode is and how to use it in posts.
 *
 * @todo post-meta option to disable the thing.
 */
function scaip_how_to_shortcode_callback($post = null) {
	$scaip_period = get_option('scaip_settings_period', 3);
	$scaip_repetitions = get_option('scaip_settings_repetitions', 2);
	$scaip_minimum_paragraphs = get_option('scaip_settings_min_paragraphs', 6);

	?>
	<p>By default, <?php echo $scaip_repetitions; ?> ads will be inserted in a post, beginning <?php echo $scaip_period; ?> paragraphs after the beginning and every <?php echo $scaip_period; ?> paragraphs after that. They will not appear if this post is shorter than <?php echo $scaip_minimum_paragraphs; ?> paragraphs long.</p>
	<p>If the automatic positioning is terrible, you can prevent automatic placement of ads by adding the <code>[scaip]</code> shortcode on its own line in the post. Ads will be placed there.</p>
	<p>If you want to disable ads, add <code>[scaip no]</code> at the start of the story.</p>
	<?php
}
add_action('add_meta_boxes', function() {
	add_meta_box( 'scaip_docs_and_options', __('Super Cool Ad Inserter', 'scaip'), 'scaip_how_to_shortcode_callback', 'post', 'normal', 'low');
});
