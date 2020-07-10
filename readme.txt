=== Super Cool Ad Inserter Plugin ===
Contributors: innlabs
Donate link: https://inn.org/donate
Tags: ads, advertising,  widget, shortcode, google, post, page
Requires at least: 4.0.0
Tested up to: 5.4
Requires PHP: 5.3
Stable tag: 0.2.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin enables the insertion of widget areas in your post's content via programmatic insertion at display time, via a shortcode, or via blocks. Use these widget areas for ads, newsletter signups, calls to action, or anything else!

== Description ==

This WordPress plugin gives site administrators a way to insert widgets such as ads, newsletter signups, or calls to action into posts at set intervals. This setting can be overridden on a per-post basis.

== Installation ==

1. Upload the plugin directory to your `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

For more advanced documentation for developers and advanced users see [the official plugin docs](https://github.com/INN/super-cool-ad-inserter-plugin/tree/master/docs).

For support, contact [mailto:support@inn.org](support@inn.org).

== Changelog ==

= 0.2.1 =

* Updates the stable "Tested up to" number.

= 0.2 =

* Adds Gutenberg block to enable custom placement of Inserted Ad Position widget areas within posts. This block supports Gutenberg's alignment and custom class tools.
* Partial WordPress VIP code approval, via [Adam Schweigert](https://github.com/aschweigert)'s work in [pull request #32](https://github.com/INN/super-cool-ad-inserter-plugin/pull/32).
* Adds filter to programmatically disable ad insertion. Pull request [#39](https://github.com/INN/super-cool-ad-inserter-plugin/pull/39) for [issue #25](https://github.com/INN/super-cool-ad-inserter-plugin/issues/25).
* Adds filter to allow changing the `before_widget`, `after_widget`, `before_title`, and `after_title` attributes used to register SCAIP's Inserted Ad Position sidebars. Pull request [#40](https://github.com/INN/super-cool-ad-inserter-plugin/pull/40), continuing [#39](https://github.com/INN/super-cool-ad-inserter-plugin/pull/39)'s work to make the plugin more configurable for advanced use cases.
* Removes some unneeded development files
* Documentation improvements

= 0.1.2 =
* Removes the .visuallyhidden class from the widget title of inserted widgets
* Improves compliance with WordPress PHP code standards
* Removes some unneeded development files

= 0.1 =
* Initial beta release.

