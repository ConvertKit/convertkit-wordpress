# Setup Guide

This document describes how to setup your development environment, so that it is ready to run, develop and test the ConvertKit WordPress Plugin.

Suggestions are provided for the LAMP/LEMP stack and Git client are for those who prefer the UI over a command line and/or are less familiar with 
WordPress, PHP, MySQL and Git - but you're free to use your preferred software.

## Setup

### LAMP/LEMP stack

Any Apache/nginx, PHP 7.x+ and MySQL 5.8+ stack running WordPress.  For example, but not limited to:
- Local by Flywheel (recommended)
- Docker
- MAMP
- WAMP
- VVV

### Composer

If [Composer](https://getcomposer.org) is not installed on your local environment, enter the following commands at the command line to install it:

```bash
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === '906a84df04cea2aa72f40b5f787e49f22d4c2f19492ac310e8cba5b96ac8b64115ac402c8cd292b8a03482574915d1a8') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"
sudo mv composer.phar /usr/local/bin/composer
```

Confirm that installation was successful by entering the `composer` command at the command line

### Clone Repository

Using your preferred Git client or command line, clone this repository into the `wp-content/plugins/` folder of your local WordPress installation.

If you prefer to clone the repository elsewhere, and them symlink it to your local WordPress installation, that will work as well.

If you're new to this, use [GitHub Desktop](https://desktop.github.com/) or [Tower](https://www.git-tower.com/mac)

### Create Test Database

Create a blank `test` database in MySQL, with a MySQL user who can read and write to it.

### Configure Testing Environment

Copy the `.env.example` file to `.env.testing` in the root of this repository, changing folder and database credentials as necessary:
```
TEST_SITE_DB_DSN=mysql:host=localhost;dbname=test  // Your local MySQL host and database name
TEST_SITE_DB_HOST=localhost // Your local MySQL host
TEST_SITE_DB_NAME=test // If you followed the instructions above, your test database should be called test :)
TEST_SITE_DB_USER=root // Your local MySQL user
TEST_SITE_DB_PASSWORD=root // Your local MySQL password
TEST_SITE_TABLE_PREFIX=wp_ // Dont' change; this refers to the WordPress database table prefix used  for testing that's stored in _tests/data/dump.sql
TEST_SITE_ADMIN_USERNAME=admin // Don't change; this refers to the WordPress admin login used for testing that's stored in _tests/data/dump.sql
TEST_SITE_ADMIN_PASSWORD=password // Don't change; this refers to the WordPress admin login used for testing that's stored in _tests/data/dump.sql
TEST_SITE_WP_ADMIN_PATH=/wp-admin // Don't change
WP_ROOT_FOLDER="/Users/tim/Local Sites/convertkit/app/public" // Location of your WordPress installation
TEST_DB_NAME=test // If you followed the instructions above, your test database should be called test :)
TEST_DB_HOST=localhost // Your local MySQL host
TEST_DB_USER=root // Your local MySQL user
TEST_DB_PASSWORD=root // Your local MySQL password
TEST_TABLE_PREFIX=wp_ // Dont' change; this refers to the WordPress database table prefix used  for testing that's stored in _tests/data/dump.sql
TEST_SITE_WP_URL=http://convertkit.local // Your local WordPress URL
TEST_SITE_WP_DOMAIN=convertkit.local // Your local WordPress domain
TEST_SITE_ADMIN_EMAIL=wordpress@convertkit.local // Don't change
CONVERTKIT_API_KEY=// A valid ConvertKit API Key
CONVERTKIT_API_SECRET=// A valid ConvertKit API Secret
CONVERTKIT_API_FORM_NAME="" // The name of a form that exists on the ConvertKit Account for the above API credentials
CONVERTKIT_API_FORM_ID="" // The ID of the CONVERTKIT_API_FORM_NAME
CONVERTKIT_API_LANDING_PAGE_NAME="" // The name of a landing page that exists on the ConvertKit Account for the above API credentials
CONVERTKIT_API_LANDING_PAGE_ID="" // The ID of the CONVERTKIT_API_LANDING_PAGE_NAME
CONVERTKIT_API_TAG_NAME="" // The name of a tag that exists on the ConvertKit Account for the above API credentials
CONVERTKIT_API_TAG_ID="" // The ID of the CONVERTKIT_API_TAG_NAME
CONVERTKIT_API_SUBSCRIBER_EMAIL="" // The email of the CONVERTKIT_API_SUBSCRIBER_ID
CONVERTKIT_API_SUBSCRIBER_ID="" // The ID of a valid subscriber on the ConvertKit Account for the above API credentials, who is also tagged with the above tag
```

### Install Testing Suite

In the Plugin's directory, at the command line, run `composer update`.

This will install the necessary libraries used for testing, including wp-browser, Codeception, PHPUnit and PHP_CodeSniffer, which we'll cover later on.

### Configure wp-config.php

In the root of your WordPress installation, find the `wp-config.php` file.

Change the following line from (your database name itself may vary):

```php
define( 'DB_NAME', 'local' );
```

to:

```php
if( isset( $_SERVER['HTTP_X_TEST_REQUEST'] ) && $_SERVER['HTTP_X_TEST_REQUEST'] ) {
    // WPBrowser request, performed when Codeception tests are run. Connect to test DB.
    define( 'DB_NAME', 'test' );
} elseif( isset( $_SERVER['HTTP_USER_AGENT'] ) && strpos( $_SERVER['HTTP_USER_AGENT'], 'HeadlessChrome' ) !== false ) {
    // WPWebDriver request, performed when Codeception tests are run. Connect to test DB.
    define( 'DB_NAME', 'test' );
} else {
    // Connect to local DB.
    define( 'DB_NAME', 'local' );
}
```

When Codeception tests are run, they will include either:
- The `HTTP_X_TEST_REQUEST` header for tests run using WPBrowser.
- The `HeadlessChrome` HTTP User Agent for tests run using WPWebDriver.

Our change above tells WordPress to use the test database for our test requests, whilst using the local/default database for any other requests.

### Install ChromeDriver

ChromeDriver is a headless (i.e. non-GUI) browser that our test suite uses to run Acceptance tests, interacting with the ConvertKit
Plugin just as a user would - including full JavaScript execution, user inputs etc.

Download chromedriver for your Google Chrome version and OS from https://sites.google.com/chromium.org/driver/downloads?authuser=0

For Mac users, copy the unzipped executable to `/usr/local/bin`.

### Running the Test Suite

First, run the ChromeDriver in a separate Terminal window:

```bash
chromedriver --url-base=/wd/hub
```

![ChromeDriver Screenshot](/.github/docs/chromedriver.png?raw=true)

In a second Terminal window, in the Plugin's directory, run the tests to make sure there are no errors and that you have correctly
setup your environment:

```bash
vendor/bin/codecept build
vendor/bin/codecept run acceptance
vendor/bin/codecept run functional
vendor/bin/codecept run wpunit
vendor/bin/codecept run unit
```

![Codeception Test Results](/.github/docs/codeception.png?raw=true)

Don't worry if you don't understand these commands; if your output looks similar to the above screenshot, with no errors, your environment
is setup successfully.

### Running CodeSniffer

In the Plugin's directory, run the following command to run PHP_CodeSniffer, which will check the code meets WordPress' Coding Standards:

```bash
vendor/bin/phpcs ./ -v
```

![Codeception Test Results](/.github/docs/codesniffer.png?raw=true)

Again, don't worry if you don't understand these commands; if your output looks similar to the above screenshot, with no errors, your environment
is setup successfully.

### Add your API Key to the Plugin

Refer to the [ConvertKit Help Article](https://help.convertkit.com/en/articles/2502591-getting-started-the-wordpress-plugin) to get started with
using the WordPress Plugin.

### Next Steps

With your development environment setup, you'll probably want to start development, which is covered in the [Development Guide](DEVELOPMENT.md)