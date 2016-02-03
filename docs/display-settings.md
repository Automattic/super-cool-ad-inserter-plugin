# Ad Display Settings

The Super Cool Ad Inserter provides lots of flexibility in how and where ads are placed within posts. By default, 6 ads will be inserted automatically in a post, beginning 3 paragraphs after the beginning and every 3 paragraphs after that. They will not appear if the post is shorter than 6 paragraphs. You can easily change the default settings for automatic insertion of ads in the [Ad Inserter Plugin Options panel](configuration.md).

## Ad Spacing using empty Inserted Ad Positions

You can add Text Widgets to each and every numbered Inserted Ad Position, and they will display every nth paragraph based on the number of paragraphs you defined in **Plugins** > **Ad Inserter**  settings. 

But sometimes you might want to protect larger blocks of paragraphs from getting interrupted by an ad. Let's say the ads are set to display every 3 paragraphs, which means the first ad will appear 3 paragraphs from the beginning of the post. As mentioned above, you can use an empty Inserted Ad Position to reserve that position without displaying an ad. In this case, we'd leave Inserted Ad Position 1 blank:

![empty Inserted Ad position](./img/scaip-widget-area-empty.png)

Note that we haven't even added an empty Text Widget to this Ad Position, but simply left it empty. On the post page, the first ad won't display until after 6 paragraphs:

![post with six paragraphs before the first ad](./img/scaip-ad-after-six-paras.png)