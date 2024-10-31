=== Require & Limit Categories, Tags, Featured Image and taxonomies ===

Tags			 : require,post,tag,taxonomy,category,term,thumbnail
Stable tag		 : 1.30
WordPress URI	 : https://wordpress.org/plugins/require-taxonomy-image-category-tag/
Plugin URI		 : https://puvox.software/software/wordpress-plugins/?plugin=require-taxonomy-image-category-tag
Contributors	 : puvoxsoftware,ttodua
Author			 : Puvox.software
Author URI		 : https://puvox.software/
Donate link		 : https://paypal.me/puvox
License			 : GPL-3.0
License URI		 : https://www.gnu.org/licenses/gpl-3.0.html
Requires at least: 6.0
Tested up to	 : 6.5.3

[ ✅ 𝐒𝐄𝐂𝐔𝐑𝐄 𝐏𝐋𝐔𝐆𝐈𝐍𝐒 b𝓎 𝒫𝓊𝓋𝑜𝓍] 
Force dashboard users to select chosen fields during publishing

== Description ==
= [ ✅ 𝐒𝐄𝐂𝐔𝐑𝐄 𝐏𝐋𝐔𝐆𝐈𝐍𝐒 b𝓎 𝒫𝓊𝓋𝑜𝓍] : =
> • Revised for security to be reliable and free of vulnerability holes.
> • Efficient, not to add any extra load/slowness to site.
> • Don't collect private data.
= Plugin Description =
Force dashboard users to select chosen fields during publishing. You can force users to insert/choose category, tag (or specific taxonomy) or Featured Image (a.k.a. thumbnails) when they publish any post. 
You can also limit maximum allowed tags/categories to be chosen.

**Programatical hooks**: 

- to change the `$args` passed to javascript handler:
<code>
add_filter("rtict_javascript_object", "your_func");
function your_func($args) {
	...
}
</code>
- to change the javascript handler's error-output callback, define this in global JS scope:

<code>
function rtict_error_handler(args) {
	// ... console.log(args);
}
</code>

= Available Options =
See all available options and their description on plugin's settings page.


== Screenshots ==
1. screenshot


== Installation ==
A) Enter your website "Admin Dashboard > Plugins > Add New" and enter the plugin name
or
B) Download plugin from WordPress.org , Extract the zip file and upload the container folder to "wp-content/plugins/"


== Frequently Asked Questions ==
- More at <a href="https://puvox.software/software/wordpress-plugins/">our WP plugins</a> page.


== Changelog ==
= 1.0 =
* First release.