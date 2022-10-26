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
		$I->activateThirdPartyPlugin($I, 'gravity-forms');
		$I->activateThirdPartyPlugin($I, 'convertkit-gravity-forms');

		$I->activateConvertKitPlugin($I);

		// Setup Settings > ConvertKit, which will use WordPress Libraries and show errors
		// if there's a conflict.
		$I->setupConvertKitPlugin($I);

		$I->see('sdfsdfsdf');
	}
}
