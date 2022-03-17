=== ConvertKit ===
Contributors: nathanbarry, growdev, travisnorthcutt, ggwicz
Donate link: https://convertkit.com
Tags: email, marketing, embed form, convertkit, capture
Requires at least: 5.0
Tested up to: 5.9.2
Requires PHP: 5.6.20
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

ConvertKit is an email marketing platform for capturing leads from your WordPress blog.

== Description ==

[ConvertKit](https://convertkit.com) makes it easy to capture more leads and sell more products by easily embedding email capture forms anywhere.

This plugin makes it even easier for those of us using WordPress by automatically appending a lead capture form to any post or page.

If you choose a default form on the settings page, that form will be embedded at the bottom of every post or page (in single view only) across your site.

If you wish to turn off form embedding or select a different form for an individual post or page, you can do so within the ConvertKit meta box on the editing form.

Finally, you can insert the default form into the middle of post or page content by using the `[convertkit]` shortcode.

Full plugin documentation is located [here](https://help.convertkit.com/en/articles/2502591-the-convertkit-wordpress-plugin).

== Installation ==

1. Upload the `convertkit` folder to the `/wp-content/plugins/` directory
2. Active the ConvertKit plugin through the 'Plugins' menu in WordPress

== Configuration ==

1. Configure the plugin by navigating to Settings > ConvertKit in the WordPress Administration Menu, entering your [API Key](https://app.convertkit.com/account_settings/advanced_settings) and defining the default forms to display on Pages, Posts and/or Custom Post Types
2. (Optional) choose a specific Form to display when editing a Page, Post or Custom Post Type in the Page/Post/Custom Post Type's ConvertKit settings
3. (Optional) use the ConvertKit Form Shortcode or Block to insert Forms into your Page, Post or Custom Post Type content

== Frequently asked questions ==

= Does this plugin require a paid service? =

No. You must first have an account on ConvertKit.com, but you do not have to use a paid plan!

= How do I refresh my available Forms, Landing Pages and Tags? =

Navigate to the Plugin's Settings at Settings > ConvertKit.

== Screenshots ==

1. Create and customize stunning landing pages in ConvertKit
2. Set WordPress Pages to use ConvertKit landing page content from a simple metabox in the WordPress admin editor
3. Set up form and landing page automations in ConvertKit
4. Manage the WordPress plugin from a simple settings page in the WordPress admin
5. Track subscriber growth
6. An example of a ConvertKit landing page
7. Another ConvertKit landing page example

== Changelog ==

### 1.9.7.0 2022-03-17
* Fix: ConvertKit Form Block: Order Form names alphabetically
* Fix: Prevent Select2 styling from applying to non-Plugin elements

### 1.9.6.9 2022-03-07
* Added: ConvertKit Form Block: When editing, display the Form Name if a non-inline form has been selected, as non-inline forms cannot be previewed in the editor
* Fix: Include email address in API request when attempting to fetch subscriber ID by email when ConvertKit Form is submitted with no email address

### 1.9.6.8 2022-02-18
* Fix: Performance: Don't query API to fetch subscriber ID by email when ConvertKit Form is submitted with no email address

### 1.9.6.7 2022-02-14
* Fix: Localization: Corrected path to load language files

### 1.9.6.6 2022-01-27
* Fix: Plugin Activation: Parse error when using PHP 7.2 or below due to trailing comma in sprintf() call

### 1.9.6.5 2022-01-26
* Added: ConvertKit Form Block for Gutenberg
* Added: Select2 dropdown for Forms, Landing Pages and Tags with search functionality for improved UX.
* Fix: Legacy Forms: Removed erronous <html>, <head> and <body> tags from markup 

### 1.9.6.4 2022-01-11
* Fix: Render Legacy Form when shortcode is copied from app.convertkit.com for a Legacy Form
* Fix: Don't check for Landing Page when viewing any non-Page public Post Type which doesn't support Landing Pages
* Fix: PHP Notice: Undefined index landing_page when upgrading from 1.4.6 or earlier
* Fix: PHP Notice: Undefined index tag when upgrading from 1.4.6 or earlier

### 1.9.6.3 2021-12-23
* Fix: Render Legacy Landing Pages

### 1.9.6.2 2021-12-22
* Fix: Render Form Shortcode when a new ConvertKit Form ID specified that does not yet exist in Plugin's cached Form Resources

### 1.9.6.1 2021-12-16
* Fix: Character encoding issue on Landing Pages
* Fix: Removed unused .scripts directory and .MD files

### 1.9.6 2021-12-15
* Added: ConvertKit Form Shortcode Button for Classic Editor
* Added: Text Editor: Quicktag Buttons for inserting ConvertKit Forms and Custom Content
* Added: Settings: ConvertKit: Logo and branding header
* Added: Option to specify ConvertKit API Key and Secret as constants `CONVERTKIT_API_KEY` and `CONVERTKIT_API_SECRET` in wp-config.php
* Added: Settings: ConvertKit: General: Different Default Forms can be specified for Pages and Posts
* Added: Settings: ConvertKit: General: Enabling Debug option will also output data to browser console and inline HTML comments
* Added: Settings: ConvertKit: Tools: Option to Download Log to text file
* Added: Settings: ConvertKit: Tools: Option to Download System Info to text file
* Added: PHP 8.x compatibility
* Added: Developers: Action and filter hooks.  See https://github.com/ConvertKit/convertkit-wordpress/blob/1.9.6/ACTIONS-FILTERS.md
* Fix: PHP warnings on new installations when adding/editing Pages or Posts where the plugin was not yet configured
* Fix: PHP 8.x: PHP Deprecated warnings where required parameters wrongly followed optional parameters
* Fix: gzinflate() data error
* Fix: Deprecated edit_category_form_fields warning
* Fix: Integration: WishList Member: Unsubscribe Action 'Unsubscribe from all' now honored when selected and saved
* Fix: Ensure code meets WordPress Coding Standards
* Fix: Use WP_Filesystem instead of PHP functions to read/write log file, per WordPress Coding Standards

### 1.9.5.2 2021-07-28
* Fix: Fixed an issue where the ConvertKit shortcode would not function properly.

### 1.9.5.1 2021-07-27
* Fix: Updated GitHub zip URL for build.

### 1.9.5 2021-07-21
* Fix: Changed how API responses are logged.
* Fix: Reduced Debug output to improve performance.

### 1.9.4 2021-02-22
* New: Increase minimum supported WordPress version to 4.9 and maximum supported WordPress version to 5.6.2
* Fix: Restore original plugin file name (`wp-convertkit.php`), which reverts the breaking file name change (`plugin.php`) released in 1.9.3
* Fix: Fixed inconsistent refreshing of forms in the ConvertKit forms dropdown
* Fix: Fixed occasional "undefined variable" notices regarding the API key and API secret
* Tweak: Add ConvertKit plugin information to useragent of plugin API requests

### 1.9.3 2020-11-11
* New: Increase minimum supported WordPress version to 4.8 and maximum supported WordPress version to 5.5.3
* Fix: Fix occasional "undefined variable" notice when viewing ConvertKit forms dropdown
* Fix: Only log transients from `get_form()` method when debugging is enabled
* Tweak: Remove `sslverify => false` argument from main ConvertKit API call

### 1.9.2 2019-12-20
* Fix issue that prevented Google fonts from loading on landing pages
* Fix issue that resulted in multiple failed API calls for non-existent subscribers passed in via query parameter

### 1.9.1 2019-12-20
* Fix bug that prevented "refresh forms" button on settings page from working

### 1.9.0 2019-12-17
* Allow appending forms to WooCommerce products
* Fix bug that prevented setting "none" for form on a post from overriding category setting
* Add account name to settings page

### 1.8.1 2019-05-30
* Fix bug that could result in fatal error with certain other plugins active
* Add additional server debug info for tech support

### 1.8.0 2019-05-30
* Displays tags/LPs/forms alphabetically
* Several edge-case bug fixes
* Removes confusing utf8 warning on settings page

### 1.7.5 2019-04-30
* Fix false positive detection of character set issues related to using emojis in forms & landing pages
* Don't show error on tools tab on first visit with logging turned on
* Fix code conflict with some other plugins that resulted in PHP warnings being displayed

### 1.7.4 2019-03-27
* Fixed problem with a library that was only compatible with PHP 7+

### 1.7.3 2019-03-26
* Adds checks and notices for outdated character set (utf8 vs. utf8mb4) use
* Add notice to metabox on blog archive page that our plugin does not do anything on this page
* Update library to make plugin work with PHP 7.3 (previously, landing pages did not work)
* Fix issue where global default form would never show for some categories
* Fix include path for system status box on tools tab

### 1.7.2 2019-02-18
* Fix bug that caused fatal error on upgrade

### 1.7.1 2019-02-18
* Fix bug that caused fatal error on upgrade

### 1.7.0 2019-02-18
* New: Significantly improve performance of plugin by only attempting to tag visitors if needed (not on every page)
* New: Add option to disable javascript entirely (prevents tagging and custom content features from working)
* Fix conflict with Yoast SEO plugin
* Fix bug that prevented plugin from working with PHP 7.3
* Fix bug that prevented changing a category default form back to None
* Clarify that both API key and secret are required
* Fix bug that stripped out URL query parameters unrelated to ConvertKit
* Better handle refreshing list of forms in the connected ConvertKit account

### 1.6.4 2019-01-18
* Added tools tab with debug log and system info boxes
* Fixed a bug that would show a PHP notice in some cases
* Fixed two bugs that would sometimes result in the custom content feature failing
* Fixed a bug that would sometimes cause WishList Member integration settings to not save


### 1.6.3 2019-01-07
* Fixes issues with Contact Form 7 integration not saving form settings.
* Adds button to refresh on settings page, to fetch new forms added to the connected ConvertKit account.

### 1.6.2 2018-07-12
* Fix for this message when Landing Page is set to None: PHP Notice:  Undefined offset: 0
* Fix for new form builder being used in shortcode with "form" attribute instead of "id"

### 1.6.1 2018-07-03
* Fix for landing pages not showing in the admin area drop down
* Fix for showing new landing pages on the front end of the site
* Added jquery to landing pages as new landing page builder does not include it

### 1.6.0 2018-06-30
* Add support for new form builder
* Remove unnecessary API calls
* Store form/landing page/tag data in WP Options
* Add update routine for refreshing local convertkit data

### 1.5.5 2018-06-01
* Fix for error in javascript added to landing pages.
* Fix for applying tags based on page views on initial visit. This adds an ajax call
to each visit which some customers expressed concern about. We will add a way to remove this in an upcoming version.

### 1.5.4 2018-05-19
* Fix for adding ck_subscriber_id to cookie

### 1.5.3 2018-03-08
* Added a default form setting for post categories
* Clean up logging
* Remove admin ajax calls from each page load
* Remove api calls from the dashboard list posts page
* Add dependency to javascript enqueue to fix javascript error on landing pages

### 1.5.2 2017-11-30
* Fixed plugin settings link
* Added javascript subscriber tagging to landing pages

### 1.5.1 2017-10-31
* Fixed encoding of javascript.
* Fixed localization of data.
* Removing console.log from js.

### 1.5.0 2017-10-13
* Added saving subscriber_id after visitor fills out ConvertKit form
* Added saving subscriber_id when visitor lands on site from ConvertKit email link
* Added "Add a tag" to Posts/Page metabox
* Added convertkit_content shortcode to show content to subscribers who have a tag

### 1.4.10 2017-10-04
* Removed transient for API calls.
* Changed widget to use option setting instead of making API call.
* Reduced number of API calls while the site is being browsed.
* Updated contributors

### 1.4.9 2017-07-24
* Fix _get_meta_defaults() because Posts and Pages set to Default were not showing forms.
* Updated ConvertKit_API::_get_api_response() to inflate response body if necessary.

### 1.4.8 2017-07-13
* Fixed API response not getting unzipped
* Added check for multibyte string PHP extension
* Fixed CF7 mapping not showing all forms
* Changed log file to write locally instead of using WP_Filesystem

### 1.4.7 2017-06-01
* Code refactor with WordPress Code Standards
* Added ability to tag a customer when WishList Member membership lapses
* Added WishList Member tag a customer
* Removed curl and replaced with wp_remote_request

### 1.4.6 2017-03-29
* Fix for landing pages not appearing.
* Added code to API to not return status_code 404 content

### 1.4.5 2017-03-28
* Uncommented logging around api calls.
* Do not show 404 page content when shortcode is used with form ID that does not exist
* If ConvertKit API is unavailable fail gracefully
* Fix for Contact Form 7 message sending when no ConvertKit forms are mapped
* Removed archived forms from the list of available forms in settings page

### 1.4.4
* Added i18n support
* Added Contact Form 7 integration. Site admins can now map CF7 name and email fields to CK subscription.
* Verified functionality with latest version of WishlistMember

### 1.4.3

* Add WP widget for form
* Updates form version to v6
* Add logger to help troubleshoot issues
* Add link to CK account if no forms available
* Add notice if can't connect to API

### 1.4.2

* Fixed issue with WishlistMember integration where members were not being subscribed.

### 1.4.1

* Add upgrade routine to change ID to form_id for API version 3.0

### 1.4.0

* Update ConvertKit API to version 3.0

### 1.3.9

* Fix WishList Member email sent to CK API when shopping cart used.

### 1.3.8

* Fix crash when API response is slow

### 1.3.7

* Avoid calling API endpoints when no forms need to be shown

### 1.3.6

* Fixes issue with illegal offset showing warning message

### 1.3.5

* Fix bug showing warning messages for some users

### 1.3.4

* Fix bug showing error messages for some users

### 1.3.3

* Updated for compatibility with WordPress 4.3

### 1.3.2

* Another fix for a pesky bug causing syntax errors

### 1.3.1

* Fixes a bug causing syntax error when getting options

### 1.3.0

* Added WishList Member integration
* Updated API methods

### 1.2.1

* Fixed a warning that appeared sometimes when no forms were loaded.

### 1.2.0

* Updated to use responsive forms

### 1.0.0

* Initial release

== Upgrade notice ==

