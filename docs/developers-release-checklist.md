# Release checklist

See also https://github.com/INN/docs/blob/master/projects/wordpress-plugins/release.sh.md

## Testing before release:

Plugin settings:

- [ ] Does the settings page work?

## Post meta boxes

- [ ] The metabox checkbox disabling automatic ad insertion should work.

## Shortcode tests

- [ ] The `[ad no]` shortcode should suppress automatic ad insertion.
- [ ] A `[ad]` shortcode should not display.
- [ ] An `[ad number=1]` or `[ad number=2]` shortcode should display the relevant sidebar.
- [ ] An `[ad number=2 align=left]` shortcode should have the class `alignleft`.

## Gutenberg tests

- [ ] the block, when inserted, inserts ad position 1
- [ ] the existence of the block in the post prevents the automatic inserter from running
- [ ] the block's ad position selector, custom classes, and alignment options are respected.
- [ ] on a site with Gutenberg not installed, the plugin functions
- [ ] on a 4.9 site with Gutenberg installed, the plugin functions
- [ ] on a 5.0 site, the plugin functions
