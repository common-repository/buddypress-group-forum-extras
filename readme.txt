=== Plugin Name ===
Contributors: nuprn1
Donate link: http://buddypress.org/community/groups/buddypress-group-forum-extras/donate/
Tags: buddypress, signature, signatures, _ck_, bbpress, bbcode, quote, ajax, group forums, forum index, rss, feed, activity, widget, topic preview
Requires at least: PHP 5.2, WordPress 2.9.2, BuddyPress 1.2.5
Tested up to: PHP 5.2.x, WordPress 3.0, BuddyPress 1.2.5.x
Stable tag: 0.3.0

This plugin is a collection of sub-plugins for group forums. Signatures, bbCode lite, ShortCodes, Ajaxed Quote, RSS Feeds, Forum Index (and Widget), Activity Comments on Forum Posts, Topic Preview

== Description ==

** IMPORTANT **

This plugin will not be updated for future versions of BuddyPress (1.3) - if you would like to take over this plugin, please contact me.
http://twitter.com/#!/etiviti/statuses/29550143485247489

Group Forum Extras v0.3.0 - requires BuddyPress v1.2.5

This plugin is a collection of sub-plugins for group forums.

= This plugin package contains the following plugins: =
* Signatures (restrict what html tags can be used)
* BBCode Lite (or option for Shortcode)
* Ajaxed Quote
* Group Forum and Topic RSS Feeds
* Forum Index (and Widget)
* Tag Index (and Widget)
* Latest Topics Widget
* Activity Stream Comments on Forum Posts
* Topic Preview
* Topic & Post CSS for user levels (group admin, group mod, banned, friend, follow, mine)

= Related Links: = 

* <a href="http://blog.etiviti.com/2010/03/buddypress-group-forum-extras/" title="BuddyPress Group Forum Extras - Blog About Page">About Page</a>
* <a href="http://etivite.com" title="Plugin Demo Site">Author's BuddyPress Demo Site</a>

= Conflict Warning = 
If you experience a WSOD or fatal error using this plugin, please report the bug.

== Installation ==

1.Upload the full directory into your wp-content/plugins directory
2.Activate the `BuddyPress Forums Extras` on the plugin administration page
3.Activate the desired sub-plugins for your best configuration
4.Some plugins require a theme edit in order to work (Ajaxed Quote, Activity comments on forum posts) - see the FAQ or settings page within wp-admin

== Frequently Asked Questions ==

= What theme edit is required? =

Please see Other Notes/Extra Configuration section

= I use an external bbPress install along with Group Forums =

This has been taken into consideration
Signatures will use the same meta table as _ck_'s signature plugin
BBCode lite is recommended for external installs (please use Shortcodes if you run a normal buddypress internal forums - then install <a href="http://wordpress.org/extend/plugins/bbcode/">BBCode Shortcodes</a> by Viper007Bond)

= How does a member update their Signature? =

xprofile is required and a member may edit their signature under Profile -> Change Signature

= Should we be activating both BBCode and Shortcode sub-plugins? =

No

Activate the "Forum Extras - BBCode" if you want to parse the bbcode into html prior to database update. (useful if you have external but also ok for internal). I'm a fan of this method as the process happens pre-save but when a user goes back to edit a post - they'll see html

Or

Activate the "Forum Extras - Shortcode" and then Viper's Shortcode BBCode wordpress plugin - this will retain the shortcode bbcode markup in the database. All this does does is enable the shortcode filter hook on the bbpress post content. (buddypress already hooks shortcode filter on activity updates) - Downside to this approach if you ever deactivate the shortcode - you'll see the bbcode markup instead of filtering to the html equivalent but when a user edits a post - they see bbcode instead of html.

= I'm using BuddyPress 1.2.5 and Ajax Quote link does not appear =

Make sure your theme is up-to-date as well. The action hook `bp_group_forum_post_meta` was added in 1.2.5

= My question isn't answered here =

Please contact me at

* <a href="http://blog.etiviti.com/2010/03/buddypress-group-forum-extras/" title="BuddyPress Group Forum Extras - Blog About Page">About Page</a>
* <a href="http://twitter.com/etiviti" title="Twitter">Twitter</a>


== Changelog ==

= 0.3.1 =
* Fixed: RSS forum topic link was incorrect

= 0.3.0 =
* Feature: added highlight topics and posts by user level css subplugin
* Fixed: Fatal error on Forum Index sub-plugin with "Last Posted" column
* Fixed: updated ajaxquote for new bp1.2.5 bp_group_forum_post_meta new hook (one less theme edit)
* Fixed: activity stream on forum posts expanded by default
* Fixed: forum index table - added thead/tbody/alt

= 0.2.3 =
* Feature: Tag index and widget
* Feature: Forum Index and Latest Topic widget
* Feature: Forum Index includes Last Post col (requires some css work)
* Fixed: Topic RSS feed no longer needs theme edit - bp1.2.4 action added
* Fixed: Preview no longer esc_attr the the post text - bp1.2.4 new filter fix

= 0.2.1 =
* Fixed: If activity stream was enabled - quoting did not work

= 0.2.0 =
* Feature: Activity Comments on Forum Posts
* Feature: Topic First Post Preview
* Feature: Forum Index Widget (max, avatar, desc)
* Fixed: BBCode List Buttons for expanded Shortcodes Plugin

= 0.1.8 =
* Feature: Topic and Forum RSS Feeds
* Feature: Forum Index
* Shortcode: Hook for bbcode buttons js
* Extra Functions: anchor link freshness topic_time to last post id, add pagination next to topic title on forums-loop (see admin page), topic voices

= 0.1.5 =
* wpmu forum extras admin had an error due bp_is_blog_page returning true
* sig: some html tag sets not working correct, another conflict with bb_allowed_tags and bp_forums_allowed_tags
* Sig: bbcode to html support
* Sig: define restricted html tag set
* Fixed fatal error bug on wpmu pages (new blog, activation) due to naming conflicts from internal bbPress
* Quote: now links 'said' to permalink of quoted post.

= 0.1.3 =
* Fixed Signature admin bug on changing settings - not saving to db
* Fixed Signature bug on saving - if admin did not save the signature settings at least once

= 0.1.1 =
* First [BETA] version - forked bbPress plugins


== Upgrade Notice ==


== Extra Configuration ==

= Ajaxed Quote Link = 

Include a reply box on all pages for ajaxed quote, remove this if statement in the same groups/single/forum/topic.php file

`<?php if ( bp_get_the_topic_is_last_page() ) : ?>`

(don't forget about the corresponding `<?php endif; ?>` )

If your theme was not updated for 1.2.5 - then an action hook `bp_group_forum_post_meta` is missing from the topic template file. Please update your theme.


= Activity on Topic Posts =

edit the bp-default theme file: groups/single/forum/topic.php

Replace:

`</li>

<?php endwhile; ?>
</ul><!-- !-- #topic-post-list -->
`

With:

`</li>
	
<?php do_action( 'bp_forum_extras_add_after_post_content_li' ) ?>
	
<?php endwhile; ?>
</ul><!-- !-- #topic-post-list -->
`

If your theme was not updated for 1.2.5 - then an action hook `bp_group_forum_post_meta` is missing from the topic template file. Please update your theme.


= Link the freshness time_since to the last post = 

edit /bp-default/forums/forums-loop.php

Change:

`<td>
	<?php bp_the_topic_time_since_last_post() ?>
</td>`

To:

`<td>
	<a href="<?php echo bp_forum_extras_topic_last_post_link( 15 ); ?>"><?php bp_the_topic_time_since_last_post() ?></a>
</td>`


Note: 15 per_page is default for bp_has_forum_topic_posts - you may need to change this if you use a different per_page in the loop. 


= Add CSS highlights subplugin =

You will need to add css definitions to your child/theme.

`
.highlightpost-admin .highlighttopic-admin .highlightpost-mod .highlighttopic-mod .highlightpost-banned .highlighttopic-banned .highlightpost-friend .highlighttopic-friend .highlightpost-follow .highlighttopic-follow .highlightpost-mine .highlighttopic-mine
`