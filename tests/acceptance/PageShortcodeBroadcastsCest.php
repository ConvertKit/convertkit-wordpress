<?php
/**
 * Tests for the ConvertKit Form shortcode.
 * 
 * @since 	1.9.7.4
 */
class PageShortcodeBroadcastsCest
{
	/**
	 * Run common actions before running the test functions in this class.
	 * 
	 * @since 	1.9.7.4
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function _before(AcceptanceTester $I)
	{
		$I->activateConvertKitPlugin($I);
		$I->setupConvertKitPlugin($I);
		$I->enableDebugLog($I);
		$I->wait(2);
	}

	/**
	 * Test the [convertkit_broadcasts] shortcode works when using the default parameters.
	 * 
	 * @since 	1.9.7.4
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testBroadcastsShortcodeWithDefaultParameters(AcceptanceTester $I)
	{
		// Create Page with Shortcode.
		$I->havePageInDatabase([
			'post_name' 	=> 'convertkit-page-broadcasts-shortcode-default-param',
			'post_content' 	=> '[convertkit_broadcasts]',
		]);

		// Load the Page on the frontend site.
		$I->amOnPage('/convertkit-page-broadcasts-shortcode-default-param');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the shortcode output displays.
		$I->seeElementInDOM('ul.convertkit-broadcasts');
		$I->seeElementInDOM('ul.convertkit-broadcasts li.convertkit-broadcast');
		$I->seeElementInDOM('ul.convertkit-broadcasts li.convertkit-broadcast a');

		// Confirm that the default date format is as expected.
		$I->seeInSource('<time datetime="2022-04-08">April 8, 2022</time>');

		// Confirm that the default expected number of Broadcasts are displayed.
		$I->seeNumberOfElements('li.convertkit-broadcast', [1,10]);
	}

	/**
	 * Test the [convertkit_broadcasts] shortcode works when specifying a non-default date format parameter.
	 * 
	 * @since 	1.9.7.4
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testBroadcastsShortcodeWithDateFormatParameter(AcceptanceTester $I)
	{
		// Create Page with Shortcode.
		$I->havePageInDatabase([
			'post_name' 	=> 'convertkit-page-broadcasts-shortcode-date-format-param',
			'post_content' 	=> '[convertkit_broadcasts date_format="Y-m-d"]',
		]);

		// Load the Page on the frontend site.
		$I->amOnPage('/convertkit-page-broadcasts-shortcode-date-format-param');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the shortcode output displays.
		$I->seeElementInDOM('ul.convertkit-broadcasts');
		$I->seeElementInDOM('ul.convertkit-broadcasts li.convertkit-broadcast');
		$I->seeElementInDOM('ul.convertkit-broadcasts li.convertkit-broadcast a');

		// Confirm that the default date format is as expected.
		$I->seeInSource('<time datetime="2022-04-08">2022-04-08</time>');

		// Confirm that the default expected number of Broadcasts are displayed.
		$I->seeNumberOfElements('li.convertkit-broadcast', [1,10]);
	}

	/**
	 * Test the [convertkit_broadcasts] shortcode works when specifying a non-default limit parameter.
	 * 
	 * @since 	1.9.7.4
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testBroadcastsShortcodeWithLimitParameter(AcceptanceTester $I)
	{
		// Create Page with Shortcode.
		$I->havePageInDatabase([
			'post_name' 	=> 'convertkit-page-broadcasts-shortcode-limit-param',
			'post_content' 	=> '[convertkit_broadcasts limit="2"]',
		]);

		// Load the Page on the frontend site.
		$I->amOnPage('/convertkit-page-broadcasts-shortcode-limit-param');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the shortcode output displays.
		$I->seeElementInDOM('ul.convertkit-broadcasts');
		$I->seeElementInDOM('ul.convertkit-broadcasts li.convertkit-broadcast');
		$I->seeElementInDOM('ul.convertkit-broadcasts li.convertkit-broadcast a');

		// Confirm that the default date format is as expected.
		$I->seeInSource('<time datetime="2022-04-08">April 8, 2022</time>');

		// Confirm that the default expected number of Broadcasts are displayed.
		$I->seeNumberOfElements('li.convertkit-broadcast', 2);
	}

	/**
	 * Deactivate and reset Plugin(s) after each test, if the test passes.
	 * We don't use _after, as this would provide a screenshot of the Plugin
	 * deactivation and not the true test error.
	 * 
	 * @since 	1.9.7.4.7
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function _passed(AcceptanceTester $I)
	{
		$I->deactivateConvertKitPlugin($I);
		$I->resetConvertKitPlugin($I);
	}
}