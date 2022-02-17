<?php
/**
 * Tests for the ConvertKit Custom Content shortcode.
 * 
 * @since 	1.9.6
 */
class PageShortcodeCustomContentCest
{
	/**
	 * Run common actions before running the test functions in this class.
	 * 
	 * @since 	1.9.6
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function _before(AcceptanceTester $I)
	{
		$I->activateConvertKitPlugin($I);
		$I->setupConvertKitPlugin($I);
	}

	/**
	 * Test the [convertkit_content] shortcode works when a valid Tag ID is specified,
	 * and an invalid Subscriber ID is used.
	 * 
	 * @since 	1.9.6
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testFormShortcodeWithValidTagParameterAndInvalidSubscriberID(AcceptanceTester $I)
	{
		// Create Page with Shortcode.
		$I->havePageInDatabase([
			'post_name' 	=> 'convertkit-custom-content-shortcode-valid-tag-param-and-invalid-subscriber-id',
			'post_content'	=> '[convertkit_content tag="' . $_ENV['CONVERTKIT_API_TAG_ID'] . '"]ConvertKitCustomContent[/convertkit_content]',
		]);

		// Load the Page on the frontend site.
		$I->amOnPage('/convertkit-custom-content-shortcode-valid-tag-param-and-invalid-subscriber-id');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the Custom Content is not yet displayed.
		$I->dontSeeInSource('ConvertKitCustomContent');

		// Reload the page, this time with an invalid subscriber ID .
		$I->amOnPage('/convertkit-custom-content-shortcode-valid-tag-param-and-invalid-subscriber-id?ck_subscriber_id=1');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the Custom Content is not yet displayed.
		$I->dontSeeInSource('ConvertKitCustomContent');
	}

	/**
	 * Test the [convertkit_content] shortcode works when a valid Tag ID is specified,
	 * and a valid Subscriber ID is used who is subscribed to the tag.
	 * 
	 * @since 	1.9.6
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testFormShortcodeWithValidTagParameterAndValidSubscriberID(AcceptanceTester $I)
	{
		// Create Page with Shortcode.
		$I->havePageInDatabase([
			'post_name' 	=> 'convertkit-custom-content-shortcode-valid-tag-param-and-valid-subscriber-id',
			'post_content'	=> '[convertkit_content tag="' . $_ENV['CONVERTKIT_API_TAG_ID'] . '"]ConvertKitCustomContent[/convertkit_content]',
		]);

		// Load the Page on the frontend site.
		$I->amOnPage('/convertkit-custom-content-shortcode-valid-tag-param-and-valid-subscriber-id');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the Custom Content is not yet displayed.
		$I->dontSeeInSource('ConvertKitCustomContent');

		// Reload the page, this time with a subscriber ID who is already subscribed to the tag.
		$I->amOnPage('/convertkit-custom-content-shortcode-valid-tag-param-and-valid-subscriber-id?ck_subscriber_id=' . $_ENV['CONVERTKIT_API_SUBSCRIBER_ID']);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the Custom Content is now displayed.
		$I->seeInSource('ConvertKitCustomContent');
	}

	/**
	 * Run common actions before running the test functions in this class.
	 * 
	 * @since 	1.9.6.7
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function _after(AcceptanceTester $I)
	{
		$I->deactivateConvertKitPlugin($I);
		$I->resetConvertKitPlugin($I);
	}
}