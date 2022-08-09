# Testing Guide

This document describes how to:
- create and run tests for your development work,
- ensure code meets PHP and WordPress Coding Standards, for best practices and security,
- ensure code passes static analysis, to catch potential errors that tests might miss

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
- WordPress Unit Tests: Test single PHP classes or functions in isolation, with WordPress functions and classes loaded.

There is no definitive / hard guide, as a test can typically overlap into different types (such as Acceptance and Functional).

The most important thing is that you have a test for *something*.  If in doubt, an Acceptance Test will suffice.

## Writing an Acceptance Test

To create a new Acceptance Test, at the command line in the Plugin's folder, enter the following command, replacing:
- `general` with the subfolder name to place the test within at `tests/acceptance`,
- `ActivatePlugin` with a meaningful name of what the test will perform.

Acceptance tests are placed in groups within subfolders at `tests/acceptance` so that they can be run in isolation, and the GitHub Action can run each folder's acceptance tests in parallel for speed.

For example, to generate an `ActivatePlugin` acceptance test in the `tests/acceptance/general` folder:

```bash
php vendor/bin/codecept generate:cest acceptance general/ActivatePlugin
```
This will create a PHP test file in the `tests/acceptance/general` directory called `ActivatePluginCest.php`

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

Additional tests can also be added that relate to this suite of tests.  For example, we might want to test that Plugin deactivation
also works:
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

    public function testPluginDeactivation(AcceptanceTester $I)
    {
        // Go to the Plugins screen in the WordPress Administration interface.
        $I->amOnPluginsPage();

        // Activate the Plugin.
        $I->deactivatePlugin('convertkit');

        // Check that the Plugin activated successfully.
        $I->seePluginDeactivated('convertkit');

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
vendor/bin/codecept run acceptance general/ActivatePluginCest
```

The console will show the successful result:

![Codeception Test Results](/.github/docs/codeception.png?raw=true)

To run all acceptance tests, use:
```bash
vendor/bin/codecept run acceptance
```

To run acceptance tests in a specific folder (for example, `general`), use:
```bash
vendor/bin/codecept run acceptance general
```

To run a specific acceptance test in a specific folder (for example, `ActivateDeactivatePluginCest` in the `general` folder), use:
```bash
vendor/bin/codecept run acceptance general/ActivateDeactivatePluginCest
```

For a full list of available wp-browser and Codeception functions that can be used for testing, see:
- [wp-browser](https://wpbrowser.wptestkit.dev/modules)
- [Codeception](https://codeception.com/docs/03-AcceptanceTests)

## Required Test Format

Tests can be run in isolation, as part of a suite of tests, sequentially and/or in parralel across different environments.
It's therefore required that every Cest contain both `_before()` and `_passed()` functions, which handle:
- `_before()`: Performing prerequisite steps (such as Plugin activation, third party Plugin activation and setup) prior to each test,
- `_passed()`: Performing cleanup steps (such as Plugin deactivation, removal of Plugin data from the database) after each passing test.

The following test format should be used:

```php
class ExampleCest
{
    /**
     * Run common actions before running the test functions in this class.
     * 
     * @since   X.X.X
     * 
     * @param   AcceptanceTester    $I  Tester
     */
    public function _before(AcceptanceTester $I)
    {
        $I->activateConvertKitPlugin($I);
        $I->activateThirdPartyPlugin($I, 'third-party-plugin-slug');
        $I->setupConvertKitPlugin($I);
        $I->enableDebugLog($I);
    }

    public function testSpecificSteps(AcceptanceTester $I)
    {
        // ... write a test here.
    }

    public function testAnotherSpecificSteps(AcceptanceTester $I)
    {
        // ... write a test here.
    }

    // .. write further functions for tests as necessary.

    /**
     * Deactivate and reset Plugin(s) after each test, if the test passes.
     * We don't use _after, as this would provide a screenshot of the Plugin
     * deactivation and not the true test error.
     * 
     * @since   X.X.X
     * 
     * @param   AcceptanceTester    $I  Tester
     */
    public function _passed(AcceptanceTester $I)
    {
        $I->deactivateConvertKitPlugin($I);
        $I->deactivateThirdPartyPlugin($I, 'third-party-plugin-slug');
        $I->resetConvertKitPlugin($I);
    }
}
```

## Using Helpers

Helpers extend testing by registering functions that we might want to use across multiple tests, which are not provided by wp-browser, 
Codeception or PHPUnit.  This helps achieve the principle of DRY code (Don't Repeat Yourself).

For example, in the `tests/_support/Helper/Acceptance` directory, our `Xdebug.php` helper contains the `checkNoWarningsAndNoticesOnScreen()` function,
which checks that
- the <body> class does not contain the `php-error` class, which WordPress adds if a PHP error is detected
- no Xdebug errors were output
- no PHP Warnings or Notices were output

Our Acceptance Tests can now call `$I->checkNoWarningsAndNoticesOnScreen($I)`, instead of having to write several lines of code to perform each 
error check for every test.

Further Acceptance Test Helpers that are provided include:
- `maybeCloseGutenbergWelcomeModal($I)`: Closes the Gutenberg welcome modal when adding a Page or Post.
- `activateConvertKitPlugin($I)`: Logs in to WordPress as the `admin` user, and activates the ConvertKit Plugin.
- `deactivateConvertKitPlugin($I)`: Logs in to WordPress as the `admin` user, and deactivates the ConvertKit Plugin.
- `activateThirdPartyPlugin($I, $name)`: Logs in to WordPress as the `admin` user, and activates the given third party Plugin by its slug.
- `deactivateThirdPartyPlugin($I, $name)`: Logs in to WordPress as the `admin` user, and deactivates the given third party Plugin by its slug.
- `setupConvertKitPlugin($I)`: Enters the ConvertKit API Key and Secret in the Plugin's Settings screen, saving it.

Other helpers most likely exist; refer to the [Acceptance.php](https://github.com/ConvertKit/convertkit-wordpress/blob/main/tests/_support/Helper/Acceptance/)
folder of helper files for all available functions.

## Writing Helpers

With this methodology, if two or more of your tests perform the same checks, you should:
- add a function to the applicable file in the `tests/_support/Helper/Acceptance` directory (e.g. `tests/_support/Helper/Acceptance/Plugin.php`),
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

If the function doesn't fit into any existing helper file:
- create a new file in the `tests/_support/Helper/Acceptance` directory
- edit the [acceptance.suite.yml](https://github.com/ConvertKit/convertkit-wordpress/blob/main/tests/acceptance.suite.yml) file, adding
the Helper's namespace and class under the `enabled` section.

Need to change how Codeception runs?  Edit the [codeception.dist.xml](codeception.dist.xml) file.

## Writing a WordPress Unit Test

WordPress Unit tests provide testing of Plugin specific functions and/or classes, typically to assert that they perform as expected
by a developer.  This is primarily useful for testing our API class, and confirming that any Plugin registered filters return
the correct data.

To create a new WordPress Unit Test, at the command line in the Plugin's folder, enter the following command, replacing `APITest`
with a meaningful name of what the test will perform:

```bash
php vendor/bin/codecept generate:wpunit wpunit APITest
```

This will create a PHP test file in the `tests/wpunit` directory called `APITest.php`

```php
<?php

class APITest extends \Codeception\TestCase\WPTestCase
{
    /**
     * @var \WpunitTester
     */
    protected $tester;
    
    public function setUp(): void
    {
        // Before...
        parent::setUp();

        // Your set up methods here.
    }

    public function tearDown(): void
    {
        // Your tear down methods here.

        // Then...
        parent::tearDown();
    }

    // Tests
    public function test_it_works()
    {
        $post = static::factory()->post->create_and_get();
        
        $this->assertInstanceOf(\WP_Post::class, $post);
    }
}
```

Helpers can be used for WordPress Unit Tests, similar to how they can be used for acceptance tests.
To register your own helper function, add it to the `tests/_support/Helper/Wpunit.php` file.

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
vendor/bin/codecept run wpunit
```

If a test fails, you can inspect the output and screenshot at `tests/_output`.

Any errors should be corrected by making applicable code or test changes.

## Run PHP CodeSniffer

[PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) checks that all Plugin code meets the 
[WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/).

In the Plugin's directory, run the following command to run PHP_CodeSniffer, which will check the code meets WordPress' Coding Standards:

```bash
vendor/bin/phpcs ./ -v -s
```

`-v` produces verbose output, and `-s` specifies the precise rule that failed:
![Coding Standards Screenshot](/.github/docs/coding-standards-error.png?raw=true)

Any errors should be corrected by either:
- making applicable code changes
- (Experimental) running `vendor/bin/phpcbf ./ -v` to automatically fix coding standards

Need to change the PHP or WordPress coding standard rules applied?  Either:
- ignore a rule in the affected code, by adding `phpcs:ignore {rule}`, where {rule} is the given rule that failed in the above output.
- edit the [phpcs.xml](phpcs.xml) file.

**Rules should be ignored with caution**, particularly when sanitizing and escaping data.

## Run PHPStan

[PHPStan](https://phpstan.org) performs static analysis on the Plugin's PHP code.  This ensures:

- DocBlocks declarations are valid and uniform
- DocBlocks declarations for WordPress `do_action()` and `apply_filters()` calls are valid
- Typehinting variables and return types declared in DocBlocks are correctly cast
- Any unused functions are detected
- Unnecessary checks / code is highlighted for possible removal
- Conditions that do not evaluate can be fixed/removed as necessary

In the Plugin's directory, run the following command to run PHPStan:

```bash
vendor/bin/phpstan --memory-limit=1G
```

Any errors should be corrected by making applicable code changes.

False positives [can be excluded by configuring](https://phpstan.org/user-guide/ignoring-errors) the `phpstan.neon` file.

## Next Steps

Once your test(s) are written and successfully run locally, submit your branch via a new [Pull Request](https://github.com/ConvertKit/convertkit-wordpress/compare).

It's best to create a Pull Request in draft mode, as this will trigger all tests to run as a GitHub Action, allowing you to
double check all tests pass.

If the PR tests fail, you can make code changes as necessary, pushing to the same branch.  This will trigger the tests to run again.

If the PR tests pass, you can publish the PR, assigning some reviewers.