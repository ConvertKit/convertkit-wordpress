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
				'post_title'  => 'Kit: Post: 1.4.6',
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
				'post_title'  => 'Kit: Page: 1.4.6',
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
	 * Tests that an Access Token and Refresh Token are obtained using an API Key and Secret
	 * when upgrading to 2.5.0 or later.
	 *
	 * @since   2.5.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testGetAccessTokenByAPIKeyAndSecret(AcceptanceTester $I)
	{
		// Setup ConvertKit Plugin's settings with an API Key and Secret.
		$I->haveOptionInDatabase(
			'_wp_convertkit_settings',
			[
				'api_key'         => $_ENV['CONVERTKIT_API_KEY'],
				'api_secret'      => $_ENV['CONVERTKIT_API_SECRET'],
				'debug'           => 'on',
				'no_scripts'      => '',
				'no_css'          => '',
				'post_form'       => $_ENV['CONVERTKIT_API_FORM_ID'],
				'page_form'       => $_ENV['CONVERTKIT_API_FORM_ID'],
				'product_form'    => $_ENV['CONVERTKIT_API_FORM_ID'],
				'non_inline_form' => '',
			]
		);

		// Define an installation version older than 2.5.0.
		$I->haveOptionInDatabase('convertkit_version', '2.4.0');

		// Activate the Plugin, as if we just upgraded to 2.5.0 or higher.
		$I->activateConvertKitPlugin($I);

		// Confirm the options table now contains an Access Token and Refresh Token.
		$settings = $I->grabOptionFromDatabase('_wp_convertkit_settings');
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
		$I->see('Kit WordPress');
		$I->see('Disconnect');

		// Check the order of the Form resources are alphabetical, with 'None' as the first choice.
		$I->checkSelectFormOptionOrder(
			$I,
			'#_wp_convertkit_settings_page_form',
			[
				'None',
			]
		);
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
