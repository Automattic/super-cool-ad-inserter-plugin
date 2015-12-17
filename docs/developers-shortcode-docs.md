## How ads are inserted by default.

1. You activate the plugin.
2. You configure the configurations.
3. The plugin registers a number of sidebars equal to the number of times that ads should be inserted into the post.
4. Users add widgets to those sidebars, which are inserted on the shortcodes where `$args[] == 

## Customization!


### Define your own behavior.

If you need to do something that is more complex than a widget, you can write your own hooks on the SCAIP shortcode.

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

Write a hook that hooks the shortcode ahead of the default scaip function and unregisters the default scaip function.
