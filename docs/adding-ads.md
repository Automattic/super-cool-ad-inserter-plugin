# Adding Super Cool Ads for Display in Posts

After you [configure the plugin](configuration.md) to set the number of paragraphs before each ad insertion, and the number of available ad insertions, visit **Appearance** > **Widgets** to create the ads themselves. You'll see a number of Inserted Ad Positions as Widget areas where you can place widgets. You can set the number of available Inserted Ad Positions in the [plugin options panel](configuration.md). Each Inserted Ad Position widget area should have only one widget in it.

If you are using an ad plugin such as [DoubleClick for WordPress](https://wordpress.org/plugins/doubleclick-for-wp/), you can place that plugin's ad widgets in the Inserted Ad Positions.

If you would like to manually create an ad, begin by adding a Text Widget to one of the numbered Inserted Ad Positions:

![empty text widget in an Ad Position area](./img/empty-text-widget-in-ad-position.png)

You can then enter the content of the ad in the Text Widget. Note that you may include markup including iframed content from external ad sources. You can alternatively create your own ad content, and include inline styles for the content in the Text Widget:

![ad content in the text widget](./img/scaip-ad-markup.png)

In this case we've added our own markup, including inline styles defined in a div wrapping the ad:

```html
<div class="supercoolad" style="padding: 1em; border: 1px solid #ccc; background-color: #F6F7F8;">
<img src="http://borgborg.org/largoborg/wp-content/uploads/2016/02/bagpipes-e1454443678992.png" alt="man with bagpipes in front of scenic lake and mountains" style="float:left; margin: 0 1em 1em 0;">
<h2>Play the bagpipes</h2>
<p>Did you know that the term "bagpipe" is equally in the singular or plural, although in the English language, pipers most commonly talk of "the pipes", "a set of pipes" or "a stand of pipes". Either way, there's nothing more welcome at holiday parties than a set of bagpipes. <a href="#">Get yours today</a> and get a 10% discount on lessons. </p>
</div>
```

You can add CSS rules to your Largo child theme to apply styles to all inserted ads, which are contained in an aside with a class of `scaip`. Or in the Text Widget for each ad, you can wrap the ad in a div with whatever class defines your existing ad styles. The example above uses the class `supercoolad`.

With the above markup in the Text Widget in this Inserted Ad Position, the ad looks like this in the post:

![advertisement for bagpipes on the post page](./img/scaip-ad-on-post.png)

## Setting How Ads are Displayed in Posts

The SCAIP provides great flexibility in how and where ads are displayed in posts. For details see [Ad Display Settings](display-settings.md).
