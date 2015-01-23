# WP ConvertKit

Contributors: nickohrn  
Tags: admin, api, forms, web service  
Requires at least: 3.6  
Tested up to: 3.6  
Stable tag: 1.0.0  
License: GPLv2 or later  
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Quickly and easily integrate [ConvertKit](https://convertkit.com) into your WordPress site.

## Description

[ConvertKit](https://convertkit.com) makes it easy to capture more leads and sell more products by easily
embedding email capture forms anywhere. This plugin makes it a little bit easier for those of us using WordPress
blogs, by automatically appending a lead capture form to any post or page.

If you choose a default form on the settings page, that form will be embedded at the bottom of every post or page
(in single view only) across your site. If you wish to turn off form embedding or select a different form for
an individual post or page, you can do so within the ConvertKit meta box on the editing form.

Finally, you can insert the default form into the middle of post or page content by using the `[convertkit]` shortcode.

## Installation

1. Upload `wp-convertkit` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Visit the settings page by clicking on the link under the plugin's name
1. Enter your ConvertKit API key, which you can find [here](https://convertkit.com/app/account/edit), and save the settings
1. Select your default form and save the settings
1. If you wish, choose particular forms for each post or page by visiting the edit screen and choosing the correct form

## Changelog

### 1.2.1

* Fixed a warning that appeared sometimes when no forms were loaded.

### 1.2.0

* Updated to use responsive forms

### 1.0.0

* Initial release
