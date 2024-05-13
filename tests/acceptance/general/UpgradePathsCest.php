<?php
/**
 * Tests edge cases when upgrading between specific ConvertKit Plugin versions.
 *
 * @since   1.9.6.4
 */
class UpgradePathsCest
{
	/**
	 * Check for undefined index errors for a Post when upgrading from 1.4.6 or earlier to 1.4.7 or later.
	 *
	 * @since   1.9.6.4
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testUndefinedIndexForPost(AcceptanceTester $I)
	{
		// Activate and Setup ConvertKit plugin.
		$I->activateConvertKitPlugin($I);
		$I->setupConvertKitPlugin($I);
		$I->setupConvertKitPluginResources($I);

		// Create a Post with Post Meta that does not include landing_page and tag keys,
		// mirroring how 1.4.6 and earlier of the Plugin worked.
		$postID = $I->havePageInDatabase(
			[
				'post_type'   => 'post',
				'post_status' => 'publish',
				'post_title'  => 'ConvertKit: Post: 1.4.6',
				'post_name'   => 'convertkit-post-1-4-6',
				'meta_input'  => [
					// 1.4.6 and earlier wouldn't set a landing_page or tag meta keys if no values were specified
					// in the Meta Box.
					'_wp_convertkit_post_meta' => [
						'form' => '0',
					],
				],
			]
		);

		// Load the Post on the frontend site.
		$I->amOnPage('convertkit-post-1-4-6');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);
	}

	/**
	 * Check for undefined index errors for a Page when upgrading from 1.4.6 or earlier to 1.4.7 or later.
	 *
	 * @since   1.9.6.4
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testUndefinedIndexForPage(AcceptanceTester $I)
	{
		// Activate and Setup ConvertKit plugin.
		$I->activateConvertKitPlugin($I);
		$I->setupConvertKitPlugin($I);
		$I->setupConvertKitPluginResources($I);

		// Create a Page with Post Meta that does not include landing_page and tag keys,
		// mirroring how 1.4.6 and earlier of the Plugin worked.
		$postID = $I->havePageInDatabase(
			[
				'post_type'   => 'post',
				'post_status' => 'publish',
				'post_title'  => 'ConvertKit: Page: 1.4.6',
				'post_name'   => 'convertkit-page-1-4-6',
				'meta_input'  => [
					// 1.4.6 and earlier wouldn't set a landing_page or tag meta keys if no values were specified
					// in the Meta Box.
					'_wp_convertkit_post_meta' => [
						'form' => '0',
					],
				],
			]
		);

		// Load the Post on the frontend site.
		$I->amOnPage('convertkit-page-1-4-6');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);
	}

	/**
	 * Tests that any existing Legacy Forms and Landing Pages are correctly cached, and therefore available
	 * for selection when upgrading to 2.5.0 or later.
	 *
	 * @since   2.5.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testLegacyResourcesCached(AcceptanceTester $I)
	{
		// Setup ConvertKit Plugin.
		$I->setupConvertKitPlugin($I);
		$I->setupConvertKitPluginResources($I);

		// Define an installation version older than 2.5.0.
		$I->haveOptionInDatabase('convertkit_version', '2.4.0');

		// Activate the Plugin, as if we just upgraded to 2.5.0 or higher.
		$I->activateConvertKitPlugin($I);

		// Go to the Plugin's Settings Screen.
		$I->loadConvertKitSettingsGeneralScreen($I);

		// Confirm the options table now contains Legacy Forms and Landing Pages.
		$legacyForms = $I->grabOptionFromDatabase('convertkit_forms_legacy');
		$I->assertArrayHasKey($_ENV['CONVERTKIT_API_LEGACY_FORM_ID'], $legacyForms);
		$I->assertEquals($_ENV['CONVERTKIT_API_LEGACY_FORM_ID'], $legacyForms[ $_ENV['CONVERTKIT_API_LEGACY_FORM_ID'] ]['id'] );

		$legacyLandingPages = $I->grabOptionFromDatabase('convertkit_landing_pages_legacy');
		$I->assertArrayHasKey($_ENV['CONVERTKIT_API_LEGACY_LANDING_PAGE_ID'], $legacyLandingPages);
		$I->assertEquals($_ENV['CONVERTKIT_API_LEGACY_LANDING_PAGE_ID'], $legacyLandingPages[ $_ENV['CONVERTKIT_API_LEGACY_LANDING_PAGE_ID'] ]['id'] );

		// Confirm the options table for the original resources no longer contains Legacy Forms and Landing Pages,
		// as the v4 API won't return those.
		$forms = $I->grabOptionFromDatabase('convertkit_forms');
		$I->assertArrayNotHasKey($_ENV['CONVERTKIT_API_LEGACY_FORM_ID'], $forms);

		$landingPages = $I->grabOptionFromDatabase('convertkit_landing_pages');
		$I->assertArrayNotHasKey($_ENV['CONVERTKIT_API_LEGACY_LANDING_PAGE_ID'], $landingPages);

		// Confirm the Legacy Form can be selected.
		// This confirms they are cached as API calls to refresh resources are always made on the Plugin Settings screen.
		$I->fillSelect2Field($I, '#select2-_wp_convertkit_settings_page_form-container', $_ENV['CONVERTKIT_API_LEGACY_FORM_NAME']);
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
