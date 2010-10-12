=== Gravity Forms Directory & Addons ===
Tags: gravity forms, forms, gravity, form, crm, gravity form, directory, business, business directory, list, listings, sort, submissions, table, tables, member, contact, contacts, directorypress, business directory, directory plugin, wordpress directory, classifieds, captcha, cforms, contact, contact form, contact form 7, contact forms, CRM, email, enhanced wp contact form, feedback, form, forms, gravity, gravity form, gravity forms, secure form, simplemodal contact form, wp contact form, widget
Requires at least: 2.8
Tested up to: 3.0.1
Stable tag: trunk
Contributors: katzwebdesign
Donate link:https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=zackkatz%40gmail%2ecom&item_name=Gravity%20Forms%20Addons&no_shipping=0&no_note=1&tax=0&currency_code=USD&lc=US&bn=PP%2dDonationsBF&charset=UTF%2d8

Add directory capabilities and other functionality to the great <a href="http://sn.im/gravityforms" rel="nofollow">Gravity Forms</a> plugin.

== Description ==

> This plugin requires the <a href="http://sn.im/gravityforms" rel="nofollow">Gravity Forms plugin</a>. <strong>Don't use Gravity Forms? <a href="http://sn.im/gravityforms" rel="nofollow">Buy the plugin</a></strong> and start using this revolutionary plugin!

__How easy is <a href="http://sn.im/gravityforms" rel="nofollow">Gravity Forms</a>? Check out the video below:__
[youtube http://www.youtube.com/watch?v=t2gFT3K_Klc]

### Turn Gravity Forms into a Directory plugin
Gravity Forms is already the easiest form plugin - its functionality makes WordPress <em>close</em> to having user-submitted directory capabilities. Finally, the Gravity Forms Directory & Addons plugin does just that.

* Completely shortcode based, using the `[directory]` shortcode
* Includes built-in __searching__ 
* Sort by each column
* Easily re-organize the columns inside Gravity Forms
* Has an option to <strong>show only approved listings</strong>
* Show or hide any column
* Directory features pagination
* Define custom styles inside the shortcode
* Includes lightbox support for uploaded images

####Insert a totally configurable table using the editor
There are over 30 configurable options for how you want the directory to display. 

###Improve Gravity Forms Functionality and Usability

* Expand the Add Fields boxes to view all the boxes at once.
* Edit form entries directly from the Entries page (saving two clicks)
* Easily access form data to use in your website with PHP functions - [Learn more on the plugin's website](http://www.seodenver.com/gravity-forms-addons/)

### Google Analytics Integration & Gravity Forms Widget

This plugin integrates Joost de Valk's <a href="http://yoast.com/gravity-forms-widget-extras/" target="_blank" rel="nofollow">Gravity Forms Widget + Extras</a>, which includes:

* Track the referrer for the form submission using Google Analytics (adds referrer data and the search keyword[s] used to the notification emails.)
* A Gravity Forms widget (see <a href="http://wordpress.org/extend/plugins/gravity-forms-addons/screenshots/" rel="nofollow">plugin Screenshots</a>)

### Check out other Gravity Forms integrations:

* <a href="http://wordpress.org/extend/plugins/gravity-forms-constant-contact/">Gravity Forms + Constant Contact</a> - If you use Constant Contact and Gravity Forms, this plugin is for you.

#### Have an idea or issue with this Gravity Forms add-on plugin?

* [Leave suggestions, comments, and/or bug reports](http://www.seodenver.com/gravity-forms-addons/)

== Screenshots ==

1. How the Gravity Forms 'Add Fields' boxes look after plugin is activated
2. This plugin adds an Edit link to Gravity Form entries
3. Insert a directory
4. How the Gravity Forms widget appears on the widgets page

== Frequently Asked Questions == 

= Does this plugin require Gravity Forms? =
This plugin requires the [Gravity Forms plugin](http://sn.im/gravityforms). __Don't use Gravity Forms? [Buy the plugin](http://sn.im/gravityforms)__ and start using this add-on plugin!

= How do I find a field ID? =
On the Gravity Forms "Edit Forms" page, hover over the form and click the link called "IDs" that appears.

= What's the license? =
This plugin is released under a GPL license.

= Form submissions are showing as duplicates. =
This is a known issue. If the submission page has both a form in the content and the same form on the sidebar widget, the entry will be submitted twice. We're working on a fix.

= How do I remove referrer information from emails? =
Add the following to your theme's `functions.php` file:

<code>remove_filter('gform_pre_submission_filter','gf_yst_store_referrer');</code>

== Changelog ==

= 2.1.2 =
* Fixed <a href="http://wordpress.org/support/topic/plugin-gravity-forms-directory-addons-widget-admin-bug" rel="nofollow">reported bug</a> with the widget where checkboxes weren't staying checked. No other changes.

= 2.1.1 =
* Fixed "Insert Directory" modal ("lightbox") functionality (<a href="http://www.seodenver.com/gravity-forms-addons/comment-page-1/#comment-3152">as reported on the plugin page</a>)

= 2.1.0 =
* Incorporated Joost de Valk's <a href="http://yoast.com/gravity-forms-widget-extras/" target="_blank">Gravity Forms Widget + Extras</a> plugin.
* Removed some code that may have been negatively affecting the display of the form fields on the Form Editor page

= 2.0.2 =
* Fixed  `Warning: in_array() [function.in-array]: Wrong datatype for second argument in /gravity-forms-addons/directory.php on line 522` and 
`Warning: in_array() [function.in-array]: Wrong datatype for second argument in /gravity-forms-addons/directory.php on line 528` PHP Warnings. Please note: you should turn off PHP warnings on your production website. To do this, add the following to your `wp-config.php` file:
<pre>
error_reporting(0);
@ini_set(‘display_errors’, 0);
</pre>

= 2.0.1 = 
* Fixed Admin-only columns being shown if in Select Columns view
* Turned off Admin-only columns by default, and added option to force showing of Admin-only options the Approved column will always be able to be shown.

= 2.0 =
* This upgrade deserves a new version number. Added directory capabilities. <em>Killer</em> directory capabilities.
* Added a form field identifier to more easily find out the form ID. Check out the FAQ "How do I find a field ID?"

= 1.2.1.1 = 
* Updated with GPL information. Did you know Gravity Forms is also GPL? Any WordPress plugin is.

= 1.2.1 = 
* Fixed whitespace issue if site is gzip'ed. No need to upgrade if you aren't getting the `Warning: Cannot modify header information - headers already sent by...` PHP error.

= 1.2 = 
* Compatibility with Gravity Forms 1.3

= 1.1 =
* Added Edit link to Entries page to directly edit an entry
* Added a bunch of functions to use in directly accessing form and entry data from outside the plugin

= 1.0 =
* Launched plugin

== Upgrade Notice ==

* Fixed <a href="http://wordpress.org/support/topic/plugin-gravity-forms-directory-addons-widget-admin-bug" rel="nofollow">reported bug</a> with the widget where checkboxes weren't staying checked. No other changes.

= 2.1.1 =
* Fixed "Insert Directory" modal ("lightbox") functionality (<a href="http://www.seodenver.com/gravity-forms-addons/comment-page-1/#comment-3152">as reported on the plugin page</a>)

= 2.1.0 =
* Incorporated Joost de Valk's <a href="http://yoast.com/gravity-forms-widget-extras/" target="_blank">Gravity Forms Widget + Extras</a> plugin.
* Removed some code that may have been negatively affecting the display of the form fields on the Form Editor page.

= 2.0.2 = 
* Fixed  `Warning: in_array() [function.in-array]: Wrong datatype for second argument in /gravity-forms-addons/directory.php on line 522` and 
`Warning: in_array() [function.in-array]: Wrong datatype for second argument in /gravity-forms-addons/directory.php on line 528` PHP Warnings. Please note: you should turn off PHP warnings on your production website. To do this, add the following to your `wp-config.php` file:
<pre>
error_reporting(0);
@ini_set(‘display_errors’, 0);
</pre>

= 2.0.1 = 
* Fixed Admin-only columns being shown if in Select Columns view
* Turned off Admin-only columns by default, and added option to force showing of Admin-only options the Approved column will always be able to be shown.

= 2.0 =
* This upgrade deserves a new version number. Added directory capabilities. Killer directory capabilities.


== Installation == 

1. Upload this plugin to your blog and Activate it
2. Using Gravity Forms, reorder the columns on the form you'd like to turn into a directory
	* In the "Entries: [Your Form]") screen, find the "Edit" link with the pencil icon in the table header. Click it.
	* Drag the columns you want in your directory onto the "Active Columns" box
	* Drag the columns you <strong>don't</strong> want in your directory onto the "Inactive Columns" box
	* Click the Save button
3. Go to the post or page where you want the directory
4. Click "Add a Gravity Forms Directory" button (likely just to the right of the Gravity Forms button)
5. Choose a form from the drop down, (you may click advanced options for lots of additional options)

