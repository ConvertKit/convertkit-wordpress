# Testing Guide

This document describes how to create and run tests for your development work.

If you're new to creating and running tests, this guide will walk you through how to do this.

For those more experienced with creating and running tests, our tests are written in PHP using [wp-browser](https://wpbrowser.wptestkit.dev/) 
and [Codeception](https://codeception.com/docs/01-Introduction).

## Prerequisites

If you haven't yet set up your local development environment with the ConvertKit Plugin repository installed, refer to the [Setup Guide](SETUP.md).

If you haven't yet created a branch and made any code changes to the Plugin, refer to the [Development Guide](DEVELOPMENT.md)

## Write (or modify) a test

If your work creates new functionality, write a test.

If your work fixes existing functionality, check if a test exists. Either update that test, or create a new test if one doesn't exist.

Tests are written in PHP using [wp-browser](https://wpbrowser.wptestkit.dev/) and [Codeception](https://codeception.com/docs/01-Introduction).

Codeception provides an expressive test syntax.  For example:
```php
$I->click('Login');
$I->fillField('#input-username', 'John Dough');
$I->pressKey('#input-remarks', 'foo');
```

wp-browser further extends Codeception's test syntax, with functions and assertions that are *specific for WordPress*.  For example,
```php
$I->activatePlugin('convertkit');
```

## Types of Test

There are different types of tests that can be written:
- Acceptance Tests: Test as a non-technical user in the web browser.
- Functional Tests: Test the framework (WordPress).
- Integration Tests: Test code modules in the context of a WordPress web site.
- Unit Tests: Test single PHP classes or functions in isolation.

There is no definitive / hard guide, as a test can typically overlap into different types (such as Acceptance and Functional).

The most important thing is that you have a test for *something*.  If in doubt, an Acceptance Test will suffice.

## Writing an Acceptance Test

To create a new Acceptance Test, at the command line in the Plugin's folder, enter the following command, replacing `ActivatePlugin` with a 
meaningful name of what the test will perform:

```bash
php vendor/bin/codecept generate:cest acceptance ActivatePlugin
```
This will create a PHP test file in the `tests/acceptance` directory called `ActivatePluginCest.php`

```php
class ActivatePluginCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function tryToTest(AcceptanceTester $I)
    {
    }
}
```

For common WordPress actions that do not relate to the Plugin (such as logging into the WordPress Administration interface), which need to be 
performed for every test that you write in this Acceptance Test, it's recommended to use the `_before()` function:

```php
class ActivatePluginCest
{
    public function _before(AcceptanceTester $I)
    {
        // Login as a WordPress Administrator before performing each test.
        $I->loginAsAdmin();
    }

    public function tryToTest(AcceptanceTester $I)
    {
    }
}
```

Above, the call to `loginAsAdmin()` is a [wp-browser specific testing function](https://wpbrowser.wptestkit.dev/modules/wpbrowser#loginasadmin) 
that is available to us.

Next, rename the `tryToTest` function to a descriptive function name that best describes what you are testing in a human readable format:

```php
class ActivatePluginCest
{
    public function _before(AcceptanceTester $I)
    {
        // Login as a WordPress Administrator before performing each test.
        $I->loginAsAdmin();
    }

    public function testPluginActivation(AcceptanceTester $I)
    {
    }
}
```

Within your test function, write the test:
```php
class ActivatePluginCest
{
    public function _before(AcceptanceTester $I)
    {
        // Login as a WordPress Administrator before performing each test.
        $I->loginAsAdmin();
    }

    public function testPluginActivation(AcceptanceTester $I)
    {
        // Go to the Plugins screen in the WordPress Administration interface.
        $I->amOnPluginsPage();

        // Activate the Plugin.
        $I->activatePlugin('convertkit');

        // Check that the Plugin activated successfully.
        $I->seePluginActivated('convertkit');

        // Check that the <body> class does not have a php-error class, which indicates an error in activation.
        $I->dontSeeElement('body.php-error');
    }
}
```

In a Terminal window, run the ChromeDriver.  This is used by our test to mimic user behaviour, and will execute JavaScript
and other elements just as a user would see them:

```bash
chromedriver --url-base=/wd/hub
```

In a second Terminal window, run the test to confirm it works:
```bash
vendor/bin/codecept build
vendor/bin/codecept run acceptance
```

The console will show the successful result:

![Codeception Test Results](/.github/docs/codeception.png?raw=true)

For a full list of available wp-browser and Codeception functions that can be used for testing, see:
- [wp-browser](https://wpbrowser.wptestkit.dev/modules)
- [Codeception](https://codeception.com/docs/03-AcceptanceTests)

## Using Helpers

Helpers extend testing by registering functions that we might want to use across multiple tests, which are not provided by wp-browser, 
Codeception or PHPUnit.  This helps achieve the principle of DRY code (Don't Repeat Yourself).

For example, in the `tests/_support/Helper` directory, our `Acceptance.php` helper contains the `checkNoWarningsAndNoticesOnScreen()` function,
which checks that
- the <body> class does not contain the `php-error` class, which WordPress adds if a PHP error is detected
- no Xdebug errors were output
- no PHP Warnings or Notices were output

Our Acceptance Tests can now call `$I->checkNoWarningsAndNoticesOnScreen($I)`, instead of having to write several lines of code to perform each 
error check for every test.

Further Acceptance Test Helpers that are provided include:
- `activateConvertKitPlugin()`: Logs in to WordPress as the `admin` user, and activates the ConvertKit Plugin.
- `setupConvertKitPlugin()`: Enters the ConvertKit API Key and Secret in the Plugin's Settings screen, saving it.
- `loadConvertKitSettingsGeneralScreen()`: Loads the Plugin's Settings screen at Settings > ConvertKit > General.
- `loadConvertKitSettingsToolsScreen()`: Loads the Plugin's Tools screen at Settings > ConvertKit > Tools.

The above helpers automatically check for PHP and Xdebug errors.

## Writing Helpers

With this methodology, if two or more of your tests perform the same checks, you should:
- add a function to the applicable file in the `tests/_support/Helper` directory (e.g. `tests/_support/Helper/Acceptance.php`),
usually in the format of
```php
/**
 * Description of what this function does
 * 
 * @since   1.0.0
 */
public function yourCustomFunctionNameInHelper($I)
{
    // Your checks here
    $I->...
}
```
- in your test, call your function by using `$I->yourCustomFunctionNameInHelper($I);`
- at the command line, tell Codeception to build your custom function helpers by using `vendor/bin/codecept build`

Need to change how Codeception runs?  Edit the [codeception.dist.xml](codeception.dist.xml) file.

## Run Tests

Once you have written your code and test(s), run the tests to make sure there are no errors.

If ChromeDriver isn't running, open a new Terminal window and enter the following command:

```bash
chromedriver --url-base=/wd/hub
```

To run the tests, enter the following commands in a separate Terminal window:

```bash
vendor/bin/codecept build
vendor/bin/codecept run acceptance
vendor/bin/codecept run functional
vendor/bin/codecept run wpunit
vendor/bin/codecept run unit
```

If a test fails, you can inspect the output and screenshot at `tests/_output`.

Any errors should be corrected by making applicable code or test changes.

## Run PHP CodeSniffer

[PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) checks that all Plugin code meets the 
[WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/).

In the Plugin's directory, run the following command to run PHP_CodeSniffer, which will check the code meets WordPress' Coding Standards:

```bash
vendor/bin/phpcs ./ -v
```

Any errors should be corrected by either:
- making applicable code changes
- (Experimental) running `vendor/bin/phpcbf -h` to automatically fix coding standards

Need to change the PHP or WordPress coding standard rules applied?  Edit the [phpcs.xml](phpcs.xml) file.

## Next Steps

Once your test(s) are written and successfully run, submit your branch via a new [Pull Request](https://github.com/ConvertKit/convertkit-wordpress/compare).

This will trigger a GitHub Action, which will run the above tests.