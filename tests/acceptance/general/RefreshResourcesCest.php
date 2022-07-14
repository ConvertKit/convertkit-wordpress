<?php
/**
 * Tests Refresh buttons for resource fields.
 * 
 * @since 	1.9.8.0
 */
class RefreshResourcesCest
{
	/**
	 * Run common actions before running the test functions in this class.
	 * 
	 * @since 	1.9.8.0
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function _before(AcceptanceTester $I)
	{
		// Activate and Setup ConvertKit plugin
		$I->activateConvertKitPlugin($I);

		// Use API keys that link a ConvertKit account with no forms, landing pages, tags etc.
		$I->setupConvertKitPlugin($I, $_ENV['CONVERTKIT_API_KEY_NO_DATA'], $_ENV['CONVERTKIT_API_SECRET_NO_DATA']);
		$I->enableDebugLog($I);
	}

	/**
	 * Test that the refresh buttons for Forms, Landing Pages and Tags works when adding a new Page.
	 * 
	 * @since 	1.9.8.0
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testRefreshResourcesOnPage(AcceptanceTester $I)
	{
		// Navigate to Pages > Add New
		$I->amOnAdminPage('post-new.php?post_type=page');

		// Close the Gutenberg "Welcome to the block editor" dialog if it's displayed
		$I->maybeCloseGutenbergWelcomeModal($I);

		// Programmatically change the Plugin's API keys to use a ConvertKit account that has resources.
		$I->haveOptionInDatabase('_wp_convertkit_settings', [
			'api_key'    => $_ENV['CONVERTKIT_API_KEY'],
			'api_secret' => $_ENV['CONVERTKIT_API_SECRET'],
		]);

		// Click the Forms refresh button.
		$I->click('button.wp-convertkit-refresh-resources[data-resources="forms"]');

		// Wait for button to change its state from disabled.
		$I->wait(2);

		// Confirm that an expected Form now displays in the dropdown.
		$I->seeElementInDOM('option[value="' . $_ENV['CONVERTKIT_API_FORM_ID'] . '"]');


		// @TODO.

		// Click the Landing Pages refresh button.
		// @TODO.

		// Confirm that Landing Pages now display in the dropdown.
		// @TODO.

		// Click the Tags refresh button.
		// @TODO.

		// Confirm that Tags now display in the dropdown.
		// @TODO.
	}

	/**
	 * Test that the refresh buttons for Forms and Tags works when Quick Editing a Page.
	 * 
	 * @since 	1.9.8.0
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testRefreshResourcesOnQuickEdit(AcceptanceTester $I)
	{
	}

	/**
	 * Test that the refresh buttons for Forms and Tags works when Bulk Editing Pages.
	 * 
	 * @since 	1.9.8.0
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testRefreshResourcesOnBulkEdit(AcceptanceTester $I)
	{
	}

	/**
	 * Test that the refresh button for Forms works when editing a Category.
	 * 
	 * @since 	1.9.8.0
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testRefreshResourcesOnCategory(AcceptanceTester $I)
	{
	}

	/**
	 * Deactivate and reset Plugin(s) after each test, if the test passes.
	 * We don't use _after, as this would provide a screenshot of the Plugin
	 * deactivation and not the true test error.
	 * 
	 * @since 	1.9.8.0
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function _passed(AcceptanceTester $I)
	{
		$I->deactivateConvertKitPlugin($I);
		$I->resetConvertKitPlugin($I);
	}
}