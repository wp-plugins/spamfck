=== Plugin Name ===
Contributors: zedna
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=3ZVGZTC7ZPCH2&lc=CZ&item_name=Zedna%20Brickick%20Website&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted
Tags: comments, spam, protection
Requires at least: 3.0.4
Tested up to: 4.2.2
Stable tag: 1.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Simple antispam protection for registration and comment forms.

== Description ==

This plugin:
1. Add 3 hidden fields to Registration form and to Comments form, that only robots can see. Plus you can choose to add validation checkbox to forms.
2. Add to .htaccess file direct access protection for wp-comments-post.php file.
3. Check time user spent on page and time when user send a form, if it´s under time limit (5s on registration form, 10s on comment form).  

Field names: web, message, description are part of most common spam triggers.

Additionally you can choose to add validation checkbox to forms.

== Installation ==

1. Upload `spamfck` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

Does it protect my website from all spam?

Spam is everywhere, but this plugin can reduce about 99% of incoming spam.

How does it work?

There is a 3-way protection, hidden fields, comments file protection, time check protection. 

 == Screenshots ==
1. Validation checkbox
2. Settings
3. Error message

== Upgrade Notice ==
= 1.2 =
Built on WP 4.2.2 but can work on older versions

= 1.1 =
Built on WP 4.2.2 but can work on older versions

= 1.0 =
Built on WP 4.2.2 but can work on older versions

== Changelog ==
= 1.1 =
* Added spam protection statistics
 
= 1.1 =
* Added time protection and .htaccess file protection

= 1.0 =
* First version