## How ads are inserted by default.

1. You activate the plugin.
2. You configure the plugin.
3. The plugin registers a number of sidebars equal to the number of times that ads should be inserted into the post.
4. Users add widgets to those sidebars, which are then automatically inserted in posts based on the configuration.

## Customization!

### Manually place shortcodes in a post

If you want to manually place SCAIP widget areas in a post, you can do so using the `[ad]` shortcode.

For example, to display the "Inserted Ad Position 2" in a custom location in your post, you would use the following shortcode:

    [ad number="2"]

Using this method, you can reorder inserted widget areas or manually limit the number of inserted ad positions on a per-post basis without having to define a callback to determine what gets placed in the widget area.

### Define your own behavior.

Of course, if you need to do something that is more complex than place a widget or SCAIP-inserted widget area, you can write your own hooks on the SCAIP shortcode.

1. You write a function that accepts a PHP array of arguments and `echo`s some HTML to the page.
2. You attach that function to the `scaip_shortcode` WordPress action:

```php
function scaip_test_function($args) {
	echo "<p>SCAIP was here, with these arguments: ";
	echo var_dump($args);
	echo "</p>";
}
add_action('scaip_shortcode', 'scaip_test_function');
```

### Disable the normal shortcodes

If you wish to disable the shortcode that is inserted normally, in order to replace it with your own work, write a hook that hooks the shortcode ahead of the default scaip function (so, with priority number 9 or lower) that performs your desired output and then uses [`remove_action( 'scaip_shortcode', 'scaip_shortcode_do_sidebar', 10 )`](https://codex.wordpress.org/Function_Reference/remove_action) to remove the default SCAIP shortcode handler.

If you wish to selectively disable programmatic shortcode insertion on a particular post, category of post, or other criteria, you can write a filter on `'scaip_whether_insert'`, accepting three parameters and returning `true` or `false`. The three parameters are:

1. Bool $whether Whether to insert ads programmatically in this instance.
2. String $content The post content.
3. Mixed $queried_object `$wp_query->queried_object` in the context in which the programmatic shortcode inserter is running.

An example filter would look like:

```php
function scaip_test_inserter_disabler( $whether, $content, $queried_object ) {
	if ( isset( $queried_object->ID ) && 120 = $queried_object->ID ) {
		return false;
	}

	return $whether;
}
add_filter( 'scaip_whether_insert', 'scaip_test_inserter_disabler', 10, 3 );
```

### Custom sidebar inserters

First, disable the normal inserter as described above.

Then, create a function that inserts the `[ad number=$n]` shortcode in your `post_content` where you want it to appears, where `n` is an integer between `1` and the value of `get_option( 'scaip_settings_repetitions' )`.

### Insert shortcodes in new and unusual places

A simple three-step process:

1. Identify the HTML content where you want the SCAIP widget areas to be inserted.
2. Via a filter or other function, pass that HTML as the first parameter of the function `scaip_insert_shortcode( $content );`.
3. That function will return the HTML string.

For more information on the `scaip_insert_shortcode()` function, see [`inc/scaip-shortcode-inserter.php`](/inc/scaip-shortcode-inserter.php).

As an example of a place where you might want to use the shortcode insertion function, consider that the default inserter does not insert the shortcode for the sidebar on the front page or on term archive pages.
