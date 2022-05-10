<?php
namespace Helper\Acceptance;

// Define any custom actions related to the ConvertKit Plugin that
// would be used across multiple tests.
// These are then available in $I->{yourFunctionName}

class Plugin extends \Codeception\Module
{
	/**
	 * Helper method to activate the ConvertKit Plugin, checking
	 * it activated and no errors were output.
	 * 
	 * @since 	1.9.6
	 */
	public function activateConvertKitPlugin($I)
	{
		$I->activateThirdPartyPlugin($I, 'convertkit');
	}

	/**
	 * Helper method to deactivate the ConvertKit Plugin, checking
	 * it activated and no errors were output.
	 * 
	 * @since 	1.9.6
	 */
	public function deactivateConvertKitPlugin($I)
	{
		$I->deactivateThirdPartyPlugin($I, 'convertkit');
	}

	/**
	 * Helper method to setup the Plugin's API Key and Secret.
	 * 
	 * @since 	1.9.6
	 * 
	 * @param 	mixed 	$apiKey 	API Key (if specified, used instead of CONVERTKIT_API_KEY)
	 * @param 	mixed 	$apiSecret 	API Secret (if specified, used instead of CONVERTKIT_API_SECRET)
	 */
	public function setupConvertKitPlugin($I, $apiKey = false, $apiSecret = false)
	{
		// Go to the Plugin's Settings Screen.
		$I->loadConvertKitSettingsGeneralScreen($I);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Determine API Key and Secret to use.
		$convertKitAPIKey = ($apiKey !== false ? $apiKey : $_ENV['CONVERTKIT_API_KEY']);
		$convertKitAPISecret = ($apiSecret !== false ? $apiSecret : $_ENV['CONVERTKIT_API_SECRET']);

		// Complete API Fields.
		$I->fillField('_wp_convertkit_settings[api_key]', $convertKitAPIKey);
		$I->fillField('_wp_convertkit_settings[api_secret]', $convertKitAPISecret);

		// Click the Save Changes button.
		$I->click('Save Changes');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check the value of the fields match the inputs provided.
		$I->seeInField('_wp_convertkit_settings[api_key]', $convertKitAPIKey);
		$I->seeInField('_wp_convertkit_settings[api_secret]', $convertKitAPISecret);
	}

	/**
	 * Helper method to setup the Plugin's Default Form setting for Pages and Posts.
	 * 
	 * @since 	1.9.6
	 */
	public function setupConvertKitPluginDefaultForm($I)
	{
		// Go to the Plugin's Settings Screen.
		$I->loadConvertKitSettingsGeneralScreen($I);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Select Default Form for Pages and Posts.
		$I->fillSelect2Field($I, '#select2-_wp_convertkit_settings_page_form-container', $_ENV['CONVERTKIT_API_FORM_NAME']);
		$I->fillSelect2Field($I, '#select2-_wp_convertkit_settings_post_form-container', $_ENV['CONVERTKIT_API_FORM_NAME']);

		// Click the Save Changes button.
		$I->click('Save Changes');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check the value of the fields match the inputs provided.
		$I->seeInField('_wp_convertkit_settings[page_form]', $_ENV['CONVERTKIT_API_FORM_NAME']);
		$I->seeInField('_wp_convertkit_settings[post_form]', $_ENV['CONVERTKIT_API_FORM_NAME']);

		// Return Form ID for Pages
		return $I->grabValueFrom('_wp_convertkit_settings[page_form]');
	}

	/**
	 * Helper method to setup the Plugin's Default Legacy Form setting for Pages and Posts.
	 * 
	 * @since 	1.9.6
	 */
	public function setupConvertKitPluginDefaultLegacyForm($I)
	{
		// Go to the Plugin's Settings Screen.
		$I->loadConvertKitSettingsGeneralScreen($I);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Select Default Form for Pages and Posts.
		$I->fillSelect2Field($I, '#select2-_wp_convertkit_settings_page_form-container', $_ENV['CONVERTKIT_API_LEGACY_FORM_NAME']);
		$I->fillSelect2Field($I, '#select2-_wp_convertkit_settings_post_form-container', $_ENV['CONVERTKIT_API_LEGACY_FORM_NAME']);

		// Click the Save Changes button.
		$I->click('Save Changes');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check the value of the fields match the inputs provided.
		$I->seeInField('_wp_convertkit_settings[page_form]', $_ENV['CONVERTKIT_API_LEGACY_FORM_NAME']);
		$I->seeInField('_wp_convertkit_settings[post_form]', $_ENV['CONVERTKIT_API_LEGACY_FORM_NAME']);

		// Return Form ID for Pages
		return $I->grabValueFrom('_wp_convertkit_settings[page_form]');
	}

	/**
	 * Helper method to setup the Plugin's Default Form setting for WooCommerce Products.
	 * 
	 * @since 	1.9.6
	 */
	public function setupConvertKitPluginDefaultFormForWooCommerceProducts($I)
	{
		// Go to the Plugin's Settings Screen.
		$I->loadConvertKitSettingsGeneralScreen($I);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Select option.
		$I->fillSelect2Field($I, '#select2-_wp_convertkit_settings_product_form-container', $_ENV['CONVERTKIT_API_FORM_NAME']);

		// Click the Save Changes button.
		$I->click('Save Changes');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check the value of the fields match the inputs provided.
		$I->seeInField('_wp_convertkit_settings[product_form]', $_ENV['CONVERTKIT_API_FORM_NAME']);

		// Return Form ID
		return $I->grabValueFrom('_wp_convertkit_settings[product_form]');
	}

	/**
	 * Helper method to reset the ConvertKit Plugin settings, as if it's a clean installation.
	 * 
	 * @since 	1.9.6.7
	 */
	public function resetConvertKitPlugin($I)
	{
		// Plugin Settings.
		$I->dontHaveOptionInDatabase('_wp_convertkit_settings');
		$I->dontHaveOptionInDatabase('convertkit_version');

		// Resources.
		$I->dontHaveOptionInDatabase('convertkit_forms');
		$I->dontHaveOptionInDatabase('convertkit_forms_last_queried');
		$I->dontHaveOptionInDatabase('convertkit_landing_pages');
		$I->dontHaveOptionInDatabase('convertkit_landing_pages_last_queried');
		$I->dontHaveOptionInDatabase('convertkit_tags');
		$I->dontHaveOptionInDatabase('convertkit_tags_last_queried');

		// Review Request.
		$I->dontHaveOptionInDatabase('convertkit-review-request');
		$I->dontHaveOptionInDatabase('convertkit-review-dismissed');

		// Upgrades.
		$I->dontHaveOptionInDatabase('_wp_convertkit_upgrade_posts');
	}

	/**
	 * Helper method to load the Plugin's Settings > General screen.
	 * 
	 * @since 	1.9.6
	 */
	public function loadConvertKitSettingsGeneralScreen($I)
	{
		$I->amOnAdminPage('options-general.php?page=_wp_convertkit_settings');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);
	}

	/**
	 * Helper method to load the Plugin's Settings > Tools screen.
	 * 
	 * @since 	1.9.6
	 */
	public function loadConvertKitSettingsToolsScreen($I)
	{
		$I->amOnAdminPage('options-general.php?page=_wp_convertkit_settings&tab=tools');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);
	}

	/**
	 * Helper method to enable the Plugin's Settings > General > Debug option.
	 * 
	 * @since 	1.9.6
	 */
	public function enableDebugLog($I)
	{
		// Go to the Plugin's Settings Screen.
		$I->loadConvertKitSettingsGeneralScreen($I);
		
		// Tick field.
		$I->checkOption('#debug');

		// Click the Save Changes button.
		$I->click('Save Changes');
	}

	/**
	 * Helper method to clear the Plugin's debug log.
	 * 
	 * @since 	1.9.6
	 */
	public function clearDebugLog($I)
	{
		// Go to the Plugin's Tools Screen.
		$I->loadConvertKitSettingsToolsScreen($I);
		
		// Click the Clear log button.
		$I->click('Clear log');
	}

	/**
	 * Helper method to determine if the given entry exists in the Plugin Debug Log screen's textarea.
	 * 
	 * @since 	1.9.6
	 */
	public function seeInPluginDebugLog($I, $entry)
	{
		$I->loadConvertKitSettingsToolsScreen($I);
		$I->seeInSource($entry);
	}

	/**
	 * Helper method to determine if the given entry does not exist in the Plugin Debug Log screen's textarea.
	 * 
	 * @since 	1.9.6
	 */
	public function dontSeeInPluginDebugLog($I, $entry)
	{
		$I->loadConvertKitSettingsToolsScreen($I);
		$I->dontSeeInSource($entry);
	}

	/**
	 * Check that expected HTML exists in the DOM of the page we're viewing for
	 * a Broadcasts block or shortcode.
	 * 
	 * @since 	1.9.7.5
	 *
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function seeBroadcastsOutput($I)
	{
		// Confirm that the block displays.
		$I->seeElementInDOM('ul.convertkit-broadcasts');
		$I->seeElementInDOM('ul.convertkit-broadcasts li.convertkit-broadcast');
		$I->seeElementInDOM('ul.convertkit-broadcasts li.convertkit-broadcast a');
	}
}
