<?php
/**
 * Tests Plugin activation and deactivation.
 *
 * @since   1.9.6
 */
class ActivateDeactivatePluginCest
{
	/**
	 * Activate the Plugin and confirm a success notification
	 * is displayed with no errors.
	 *
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testPluginActivationAndDeactivation(AcceptanceTester $I)
	{
		$I->activateConvertKitPlugin($I);
		$I->deactivateConvertKitPlugin($I);
	}

	/**
	 * Test for no errors when this Plugin is activated after other
	 * ConvertKit Plugins (downloaded from wordpress.org) are activated.
	 *
	 * @since   2.0.1
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testPluginActivationAndDeactivationWithOtherPlugins(AcceptanceTester $I)
	{
		// Activate other ConvertKit Plugins from wordpress.org.
		$I->activateThirdPartyPlugin($I, 'convertkit-for-woocommerce');

		// Activate this Plugin.
		// If this Plugin calls a function that doesn't exist in the outdated ConvertKit WordPress Library,
		// activating this Plugin will fail, therefore failing the test.
		$I->activateConvertKitPlugin($I);

		// Setup API Keys at Settings > ConvertKit, which will use WordPress Libraries and show errors
		// if there's a conflict e.g. an older WordPress Library was loaded from another ConvertKit Plugin.
		$I->loadConvertKitSettingsGeneralScreen($I);

		// Complete API Fields.
		$I->fillField('_wp_convertkit_settings[api_key]', $_ENV['CONVERTKIT_API_KEY']);
		$I->fillField('_wp_convertkit_settings[api_secret]', $_ENV['CONVERTKIT_API_SECRET']);

		// Click the Save Changes button.
		$I->click('Save Changes');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Deactivate Plugins.
		$I->deactivateConvertKitPlugin($I);
		$I->deactivateThirdPartyPlugin($I, 'convertkit-for-woocommerce');
	}
}
