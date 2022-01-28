<?php
/**
 * Tests the ConvertKit Review Notification.
 * 
 * @since 	1.9.6
 */
class ReviewRequestCest
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
	}

	/**
	 * Test that the review request is set in the options table when the Plugin's
	 * Settings are saved with a Default Page Form specified in the Settings.
	 * 
	 * @since 	1.9.6.7
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testReviewRequestOnSaveSettings(AcceptanceTester $I)
	{
		// Clear options table settings for review request.
		$I->deleteConvertKitReviewRequestOptions($I);

		// Setup ConvertKit Plugin.
		$I->setupConvertKitPlugin($I);

		// Define Default Form.
		$I->setupConvertKitPluginDefaultForm($I);

		// Check that the options table does have a review request set.
		$I->seeOptionInDatabase('convertkit-review-request');

		// Check that the option table does not yet have a review dismissed set.
		$I->dontSeeOptionInDatabase('convertkit-review-dismissed');
	}

	/**
	 * Test that no review request is set in the options table when the Plugin's
	 * Settings are saved with no Forms specified in the Settings.
	 * 
	 * @since 	1.9.6.7
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testReviewRequestOnSaveBlankSettings(AcceptanceTester $I)
	{
		// Clear options table settings for review request.
		$I->deleteConvertKitReviewRequestOptions($I);

		// Go to the Plugin's Settings Screen.
		$I->loadConvertKitSettingsGeneralScreen($I);

		// Click the Save Changes button.
		$I->click('Save Changes');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check that the options table doesn't have a review request set.
		$I->dontSeeOptionInDatabase('convertkit-review-request');
		$I->dontSeeOptionInDatabase('convertkit-review-dismissed');
	}

	/**
	 * Test that the review request is set in the options table when a
	 * WordPress Page is created and saved with a Form specified in
	 * the ConvertKit Meta Box.
	 * 
	 * @since 	1.9.6.7
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testReviewRequestOnSavePageWithFormSpecified(AcceptanceTester $I)
	{

		// Clear options table settings for review request.
		$I->deleteConvertKitReviewRequestOptions($I);

		// Flush Permalinks.
		$I->amOnAdminPage('options-permalink.php');

		// Setup ConvertKit Plugin.
		$I->setupConvertKitPlugin($I);

		// Define Default Form.
		$I->setupConvertKitPluginDefaultForm($I);

		// Navigate to Pages > Add New
		$I->amOnAdminPage('post-new.php?post_type=page');

		// Close the Gutenberg "Welcome to the block editor" dialog if it's displayed.
		$I->maybeCloseGutenbergWelcomeModal($I);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check that the metabox is displayed.
		$I->seeElementInDOM('#wp-convertkit-meta-box');

		// Check that the Form option is displayed.
		$I->seeElementInDOM('#wp-convertkit-form');

		// Change Form to value specified in the .env file.
		$I->fillSelect2Field($I, '#select2-wp-convertkit-form-container', $_ENV['CONVERTKIT_API_FORM_NAME']);

		// Define a Page Title.
		$I->fillField('.editor-post-title__input', 'abc123');

		// Click the Publish button.
		$I->click('.editor-post-publish-button__button');
		
		// When the pre-publish panel displays, click Publish again.
		$I->performOn('.editor-post-publish-panel__prepublish', function($I) {
			$I->click('.editor-post-publish-panel__header-publish-button .editor-post-publish-button__button');	
		});

		$I->wait(3);

		// Navigate to a screen in the WordPress Administration.
		$I->amOnAdminPage('index.php');

		// Check that the options table does have a review request set.
		$I->seeOptionInDatabase('convertkit-review-request');

		// Check that the option table does not yet have a review dismissed set.
		$I->dontSeeOptionInDatabase('convertkit-review-dismissed');

		// Confirm the review displays.
		$I->seeElementInDOM('div.review-convertkit');

		// Confirm links are correct.
		$I->seeInSource('<a href="https://wordpress.org/support/plugin/convertkit/reviews/?filter=5#new-post" class="button button-primary" rel="noopener" target="_blank">');
		$I->seeInSource('<a href="https://convertkit.com/support" class="button" rel="noopener" target="_blank">');
	}

	/**
	 * Test that the review request is set in the options table when a
	 * WordPress Page is created and saved with a Landing Page specified in
	 * the ConvertKit Meta Box.
	 * 
	 * @since 	1.9.6.7
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testReviewRequestOnSavePageWithLandingPageSpecified(AcceptanceTester $I)
	{
		// Clear options table settings for review request.
		$I->deleteConvertKitReviewRequestOptions($I);

		// Setup ConvertKit Plugin.
		$I->setupConvertKitPlugin($I);

		// Define Default Form.
		$I->setupConvertKitPluginDefaultForm($I);

		// Navigate to Pages > Add New
		$I->amOnAdminPage('post-new.php?post_type=page');

		// Close the Gutenberg "Welcome to the block editor" dialog if it's displayed.
		$I->maybeCloseGutenbergWelcomeModal($I);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check that the metabox is displayed.
		$I->seeElementInDOM('#wp-convertkit-meta-box');

		// Check that the Form option is displayed.
		$I->seeElementInDOM('#wp-convertkit-landing_page');

		// Change Landing Page to value specified in the .env file.
		$I->fillSelect2Field($I, '#select2-wp-convertkit-landing_page-container', $_ENV['CONVERTKIT_API_LANDING_PAGE_NAME']);

		// Define a Page Title.
		$I->fillField('.editor-post-title__input', 'ConvertKit: Review Request: Landing Page');

		// Click the Publish button.
		$I->click('.editor-post-publish-button__button');
		
		// When the pre-publish panel displays, click Publish again.
		$I->performOn('.editor-post-publish-panel__prepublish', function($I) {
			$I->click('.editor-post-publish-panel__header-publish-button .editor-post-publish-button__button');	
		});

		$I->wait(3);

		// Navigate to a screen in the WordPress Administration.
		$I->amOnAdminPage('index.php');

		// Check that the options table does have a review request set.
		$I->seeOptionInDatabase('convertkit-review-request');

		// Check that the option table does not yet have a review dismissed set.
		$I->dontSeeOptionInDatabase('convertkit-review-dismissed');

		// Confirm the review displays.
		$I->seeElementInDOM('div.review-convertkit');

		// Confirm links are correct.
		$I->seeInSource('<a href="https://wordpress.org/support/plugin/convertkit/reviews/?filter=5#new-post" class="button button-primary" rel="noopener" target="_blank">');
		$I->seeInSource('<a href="https://convertkit.com/support" class="button" rel="noopener" target="_blank">');
	}

	/**
	 * Test that the review request is displayed when the options table entries
	 * have the required values to display the review request notification.
	 * 
	 * @since 	1.9.6.7
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testReviewRequestNotificationDisplayed(AcceptanceTester $I)
	{
		// Clear options table settings for review request.
		$I->deleteConvertKitReviewRequestOptions($I);

		// Set review request option with a timestamp in the past, to emulate
		// the Plugin having set this a few days ago.
		$I->haveOptionInDatabase('convertkit-review-request', time() - 3600 );

		// Navigate to a screen in the WordPress Administration.
		$I->amOnAdminPage('index.php');

		// Confirm the review displays.
		$I->seeElementInDOM('div.review-convertkit');

		// Confirm links are correct.
		$I->seeInSource('<a href="https://wordpress.org/support/plugin/convertkit/reviews/?filter=5#new-post" class="button button-primary" rel="noopener" target="_blank">');
		$I->seeInSource('<a href="https://convertkit.com/support" class="button" rel="noopener" target="_blank">');
	}

	/**
	 * Test that the review request is dismissed and does not reappear
	 * on a subsequent page load.
	 * 
	 * @since 	1.9.6.7
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testReviewRequestNotificationDismissed(AcceptanceTester $I)
	{
		// Clear options table settings for review request.
		$I->deleteConvertKitReviewRequestOptions($I);

		// Set review request option with a timestamp in the past, to emulate
		// the Plugin having set this a few days ago.
		$I->haveOptionInDatabase('convertkit-review-request', time() - 3600 );

		// Navigate to a screen in the WordPress Administration.
		$I->amOnAdminPage('index.php');

		// Confirm the review displays.
		$I->seeElementInDOM('div.review-convertkit');

		// Dismiss the review request.
		$I->click('div.review-convertkit button.notice-dismiss');

		// Navigate to a screen in the WordPress Administration.
		$I->amOnAdminPage('index.php');

		// Confirm the review notification no longer displays.
		$I->dontSeeElementInDOM('div.review-convertkit');
	}
}