=== Optimum Gravatar Cache ===
Contributors: jomisica, willstockstech
Author URI: https://www.ncdc.pt/members/admin
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=ETTJQCUA5Q6CE
Tags: gravatar, cache, optimization, avatar, Lazy Load
Requires PHP: 7
Requires MySQL at least: 5.0.95
Requires at least: 4.7
Tested up to: 6.4
Stable tag: 1.4.10
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

It stores optimized copies of gravatars locally, reducing the total number of requests. This will speed up site loading and consequently improve the user experience.

== Description ==

Optimum Gravatar Cache allows you to create a local cache with optimized copies of gravatars. The optimization is accomplished by resizing the avatars to the sizes used on your site, in addition to optimizing the images internally. Optimizations are performed to minimize the size of each avatar served by the plugin.

The plugin uses Wordpress CRON to perform heavier tasks. Avatars are resized in the background. Avatars are optimized internally in the background. Except in certain cases where it is necessary to resize the avatars in the page request.

The plugin handles all your site's gravatar as long as the wordpress get_avatar() function or buddypress bp_member_avatar() function is used. Works with plugins, themes, etc.

The plugin allows you to customize the avatar by default. This avatar is served whenever the user / visitor does not have a custom gravatar. This greatly reduces the number of requests made by your page, improving the user experience. Besides allowing to frame the avatar by default with the theme of your site.

The plugin, after being configured and activated, starts by serving gravatar for all. And it begins to create the cache dynamically in the background with the sizes being collected by the requests, as well as those configured on the plug-in page. When the avatar already exists in the cache, the plugin serves the avatar in cache. The plugin periodically checks to see if the user updated their gravatar on gravatar.com if yes is updated locally. This way, keeping an updated cache.


**The plugin intends the following:**
-------------------------------------
* Work with the gravatars locally, cache;
* Reduce the number of requests per page, thus reducing the total time required to load all files. This is achieved because most users do not have a custom gravatar, and for those, only one file needs to be downloaded;
* Optimize all avatars by reducing their size and transfer time again.

Please if you find any bugs of any kind please contact me so that I can solve it as soon as possible.

== Languages ==

* English en_GB (@willstockstech)
* Portuguese pt_PT (@jomisica)

Help translate this plugin into your language

==Dependencies==

This plugin depends on the following PHP modules:

*  php-gd or php-imagick (So that it can resize the avatars)
*  php-curl (So that it can communicate with the gravatar using the same connection to update several avatars)

The plugin also depends on WordPress CRON to be able to solve the heaviest tasks in the background.

== Installation ==

This section describes how to install the plugin and get it working.

**Install the plugin manually**
-------------------------------
1. Upload the `optimum-gravatar-cache` directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to "Settings -> Optimum Gravatar Cache" to configure the options to suit your needs.

**Install the plugin manually as a must-use plugin**
----------------------------------------------------
1. Upload the `optimum-gravatar-cache` directory to the `/wp-content/mu-plugins/` directory.
2. Go to "Settings -> Gravatar Optimum Cache" to configure the options to suit your needs.

== Screenshots ==

1. In this screenshot we can see the options to configure the cache.
2. In this screenshot, we can see the options that allow you to configure the default avatar.
3. In this screenshot we can see the optimization options.
4. In this screenshot we can see the options to configure Apache Server with the .htaccess file.
5. In this screenshot we can see the options to configure the NGinx Server.
6. In this screenshot we can see more options
7. In this screenshot we can see the options for Lazy Load
8. In this screenshot we can see the page where we can enter the account data to use Coudflare
9. In this screenshot we can see some data of the cache of the plugin
10. In this screenshot we can see a comparison of the files that are downloaded when the plugin is in use and when it is not.

== Frequently Asked Questions ==

= I need help =

Check out the WordPress [support forum](https://wordpress.org/support/plugin/optimum-gravatar-cache)

= I have a great idea for your plugin! =

That's fantastic! Feel free to open an issue or you can contact me through my [email](mailto:miguel@ncdc.pt) or my [Website](https://www.ncdc.pt/groups/wordpress-optimum-gravatar-cache/)

= Does the plugin work with  Page Cache plugins? =

Yes, the plugin works with the following list of page cache plugins:

* WP Super Cache
* Hummingbird

The plugin clears the cache of pages and posts that contain a particular Gravatar that has been updated.

Other plugins that offer a way to clear the cache for a given post / page as well as the entire cache will be added in the future.

= Does the plugin support Cloudflare? =

Yes, the plugin allows you to clear the Gravatars cache in Cloudflare.

= Does it work well for new commenters? =

Yes works well with new commenters. It is not necessary to be a registered user comment.

= Do you periodically check to update the gravatars in case they are changed? =

Yes, it is checked if the avatars have been updated on Gravatar.com and whenever they are updated there, they will be updated locally in the cache.

= How can I add extra classes to Gravatars =

**Through the plugin configuration pages.**

Go to the "Other Options" plugin configuration page and add the classes you need.

**Using the wordpress get_avatar() function through themes or plugins.**

    <?php echo get_avatar ($id_or_email, $size, $default, $alt, array("class"=>"class1 class2")); ?>

**Using the buddypress function bp_member_avatar() through themes or plugins**

    <?php bp_displayed_user_avatar (array ('type' => 'full', 'width' => 150, 'height' => 150, 'class' => 'class1 class2')); ?>

== Upgrade Notice ==

= 1.4.8 =

Better check for options when enabled, removes PHP Notices.

== Changelog ==

= 1.4.8 =

* Better check for options when enabled, removes PHP Notices.
