<?php
/**
 * Tests the filter dropdown for Restrict Content in the Pages WP_List_Table.
 *
 * @since   2.1.0
 */
class RestrictContentFilterPageCest
{
	/**
	 * Run common actions before running the test functions in this class.
	 *
	 * @since   2.1.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _before(AcceptanceTester $I)
	{
		// Activate ConvertKit plugin.
		$I->activateConvertKitPlugin($I);
	}

	/**
	 * Test that no dropdown filter on the Pages screen is displayed when no API keys are configured.
	 *
	 * @since   2.1.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testNoFilterDisplayedWhenNoAPIKeys(AcceptanceTester $I)
	{
		// Navigate to Pages.
		$I->amOnAdminPage('edit.php?post_type=page');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check no filter is displayed, as the Plugin isn't configured.
		$I->dontSeeElementInDOM('#wp-convertkit-restrict-content-filter');
	}

	/**
	 * Test that no dropdown filter on the Pages screen is displayed when Restrict
	 * Content is disabled.
	 *
	 * @since   2.1.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testNoFilterDisplayedWhenRestrictContentDisabled(AcceptanceTester $I)
	{
		// Setup Plugin using API keys that have no resources.
		$I->setupConvertKitPlugin($I, $_ENV['CONVERTKIT_API_KEY'], $_ENV['CONVERTKIT_API_SECRET']);

		// Navigate to Pages.
		$I->amOnAdminPage('edit.php?post_type=page');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check no filter is displayed, as the ConvertKit account has no resources.
		$I->dontSeeElementInDOM('#wp-convertkit-restrict-content-filter');
	}

	/**
	 * Test that no dropdown filter on the Pages screen is displayed when the ConvertKit
	 * account has no Forms, Tag and Products.
	 *
	 * @since   2.1.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testNoFilterDisplayedWhenNoResources(AcceptanceTester $I)
	{
		// Setup Plugin using API keys that have no resources.
		$I->setupConvertKitPlugin($I, $_ENV['CONVERTKIT_API_KEY_NO_DATA'], $_ENV['CONVERTKIT_API_SECRET_NO_DATA']);
		$I->setupConvertKitPluginRestrictContent(
			$I,
			[
				'enabled' => 'on',
			]
		);

		// Navigate to Pages.
		$I->amOnAdminPage('edit.php?post_type=page');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check no filter is displayed, as the ConvertKit account has no resources.
		$I->dontSeeElementInDOM('#wp-convertkit-restrict-content-filter');
	}

	/**
	 * Test that filtering by Product works on the Pages screen.
	 *
	 * @since   2.1.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testFilterByProduct(AcceptanceTester $I)
	{
		// Setup Plugin.
		$I->setupConvertKitPlugin($I);
		$I->setupConvertKitPluginRestrictContent(
			$I,
			[
				'enabled' => 'on',
			]
		);

		// Create Page, set to restrict content to a Product.
		$I->createRestrictedContentPage(
			$I,
			'page',
			'ConvertKit: Page: Restricted Content: Product: Filter Test',
			'Visible content.',
			'Member only content.',
			'product_' . $_ENV['CONVERTKIT_API_PRODUCT_ID']
		);

		// Navigate to Pages.
		$I->amOnAdminPage('edit.php?post_type=page');

		// Wait for the WP_List_Table of Pages to load.
		$I->waitForElementVisible('tbody#the-list');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the Page is listed, and has the 'ConvertKit Member Content' label.
		$I->see('ConvertKit: Page: Restricted Content: Product: Filter Test');
		$I->see('ConvertKit Member Content');

		// Filter by Product.
		$I->selectOption('#wp-convertkit-restrict-content-filter', $_ENV['CONVERTKIT_API_PRODUCT_NAME']);
		$I->click('Filter');

		// Wait for the WP_List_Table of Pages to load.
		$I->waitForElementVisible('tbody#the-list');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the Page is still listed, and has the 'ConvertKit Member Content' label.
		$I->see('ConvertKit: Page: Restricted Content: Product: Filter Test');
		$I->see('ConvertKit Member Content');
	}

	/**
	 * Deactivate and reset Plugin(s) after each test, if the test passes.
	 * We don't use _after, as this would provide a screenshot of the Plugin
	 * deactivation and not the true test error.
	 *
	 * @since   2.1.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _passed(AcceptanceTester $I)
	{
		$I->deactivateConvertKitPlugin($I);
		$I->resetConvertKitPlugin($I);
	}
}
