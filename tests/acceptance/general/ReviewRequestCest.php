<?php
/**
 * Tests the ConvertKit Review Notification.
 *
 * @since   1.9.6
 */
class ReviewRequestCest
{
	/**
	 * Run common actions before running the test functions in this class.
	 *
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _before(AcceptanceTester $I)
	{
		$I->activateConvertKitPlugin($I);
	}

	/**
	 * Test that the review request is set in the options table when the Plugin's
	 * Settings are saved with a Default Page Form specified in the Settings.
	 *
	 * @since   1.9.6.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testReviewRequestOnSaveSettings(AcceptanceTester $I)
	{
		// Setup Plugin, without defining default Forms.
		$I->setupConvertKitPluginNoDefaultForms($I);

		// Go to the Plugin's Settings Screen.
		$I->loadConvertKitSettingsGeneralScreen($I);

		// Select Default Form for Pages and Posts.
		$I->fillSelect2Field($I, '#select2-_wp_convertkit_settings_page_form-container', $_ENV['CONVERTKIT_API_FORM_NAME']);
		$I->fillSelect2Field($I, '#select2-_wp_convertkit_settings_post_form-container', $_ENV['CONVERTKIT_API_FORM_NAME']);

		// Click the Save Changes button.
		$I->click('Save Changes');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check that the options table does have a review request set.
		$I->seeOptionInDatabase('convertkit-review-request');

		// Check that the option table does not yet have a review dismissed set.
		$I->dontSeeOptionInDatabase('convertkit-review-dismissed');
	}

	/**
	 * Test that no review request is set in the options table when the Plugin's
	 * Settings are saved with no Forms specified in the Settings.
	 *
	 * @since   1.9.6.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testReviewRequestOnSaveBlankSettings(AcceptanceTester $I)
	{
		// Setup Plugin, without defining default Forms.
		$I->setupConvertKitPluginNoDefaultForms($I);

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
	 * @since   1.9.6.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testReviewRequestOnSavePageWithFormSpecified(AcceptanceTester $I)
	{
		// Setup ConvertKit Plugin.
		$I->setupConvertKitPlugin($I);
		$I->setupConvertKitPluginResources($I);

		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'Kit: Page: Test Review Request on Save with Form Specified');

		// Configure metabox's Form setting = Default.
		$I->configureMetaboxSettings(
			$I,
			'wp-convertkit-meta-box',
			[
				'form' => [ 'select2', $_ENV['CONVERTKIT_API_FORM_NAME'] ],
			]
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Navigate to a screen in the WordPress Administration.
		$I->amOnAdminPage('index.php');

		// Check that the options table does have a review request set.
		$I->seeOptionInDatabase('convertkit-review-request');

		// Check that the option table does not yet have a review dismissed set.
		$I->dontSeeOptionInDatabase('convertkit-review-dismissed');
	}

	/**
	 * Test that the review request is set in the options table when a
	 * WordPress Page is created and saved with a Landing Page specified in
	 * the ConvertKit Meta Box.
	 *
	 * @since   1.9.6.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testReviewRequestOnSavePageWithLandingPageSpecified(AcceptanceTester $I)
	{
		// Setup ConvertKit Plugin.
		$I->setupConvertKitPlugin($I);
		$I->setupConvertKitPluginResources($I);

		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'Kit: Page: Test Review Request on Save with Form Specified');

		// Configure metabox's Form setting = Default.
		$I->configureMetaboxSettings(
			$I,
			'wp-convertkit-meta-box',
			[
				'landing_page' => [ 'select2', $_ENV['CONVERTKIT_API_LANDING_PAGE_NAME'] ],
			]
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Navigate to a screen in the WordPress Administration.
		$I->amOnAdminPage('index.php');

		// Check that the options table does have a review request set.
		$I->seeOptionInDatabase('convertkit-review-request');

		// Check that the option table does not yet have a review dismissed set.
		$I->dontSeeOptionInDatabase('convertkit-review-dismissed');
	}

	/**
	 * Test that the review request is displayed when the options table entries
	 * have the required values to display the review request notification.
	 *
	 * @since   1.9.6.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testReviewRequestNotificationDisplayed(AcceptanceTester $I)
	{
		// Set review request option with a timestamp in the past, to emulate
		// the Plugin having set this a few days ago.
		$I->haveOptionInDatabase('convertkit-review-request', time() - 3600 );

		// Navigate to a screen in the WordPress Administration.
		$I->amOnAdminPage('index.php');

		// Confirm the review displays.
		$I->seeElementInDOM('div.review-convertkit');

		// Confirm links are correct.
		$I->seeInSource('<a href="https://wordpress.org/support/plugin/convertkit/reviews/?filter=5#new-post" class="button button-primary" rel="noopener" target="_blank">');
		$I->seeInSource('<a href="https://kit.com/support" class="button" rel="noopener" target="_blank">');
	}

	/**
	 * Test that the review request is dismissed and does not reappear
	 * on a subsequent page load.
	 *
	 * @since   1.9.6.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testReviewRequestNotificationDismissed(AcceptanceTester $I)
	{
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

	/**
	 * Deactivate and reset Plugin(s) after each test, if the test passes.
	 * We don't use _after, as this would provide a screenshot of the Plugin
	 * deactivation and not the true test error.
	 *
	 * @since   1.9.6.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _passed(AcceptanceTester $I)
	{
		$I->deactivateConvertKitPlugin($I);
		$I->resetConvertKitPlugin($I);
	}
}
