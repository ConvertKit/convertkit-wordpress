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

		// Confirm the options table now contains Legacy Forms and Landing Pages.
		$legacyLandingPages = $I->grabOptionFromDatabase('convertkit_landing_pages_legacy');
		var_dump( $legacyLandingPages );
		die();
		/*
		$I->assertArrayHasKey('access_token', $settings);
		$I->assertArrayHasKey('refresh_token', $settings);
		$I->assertArrayHasKey('token_expires', $settings);

		// Confirm the API Key and Secret are retained, in case we need them in the future.
		$I->assertArrayHasKey('api_key', $settings);
		$I->assertArrayHasKey('api_secret', $settings);
		$I->assertEquals($settings['api_key'], $_ENV['CONVERTKIT_API_KEY']);
		$I->assertEquals($settings['api_secret'], $_ENV['CONVERTKIT_API_SECRET']);

		// Go to the Plugin's Settings Screen.
		$I->loadConvertKitSettingsGeneralScreen($I);

		// Confirm the Plugin authorized by checking for a Disconnect button.
		$I->see('ConvertKit WordPress');
		$I->see('Disconnect');

		// Check the order of the Form resources are alphabetical, with 'None' as the first choice.
		$I->checkSelectFormOptionOrder(
			$I,
			'#_wp_convertkit_settings_page_form',
			[
				'None',
			]
		);
		*/
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
