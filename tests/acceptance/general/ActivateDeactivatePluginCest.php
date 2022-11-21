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
		$I->activateThirdPartyPlugin($I, 'gravity-forms');
		$I->activateThirdPartyPlugin($I, 'convertkit-gravity-forms');

		// Activate this Plugin.
		$I->activateConvertKitPlugin($I);

		// Setup API Keys at Settings > ConvertKit, which will use WordPress Libraries and show errors
		// if there's a conflict e.g. an older WordPress Library was loaded from another ConvertKit Plugin.
		$I->setupConvertKitPlugin($I);

		// Deactivate this Plugin.
		$I->deactivateConvertKitPlugin($I);
	}

	/**
	 * Test that the ConvertKit Plugin deactivates when clicking the Deactivate link on the Plugins
	 * screen, completing the form fields on the deactivational modal and then submitting.
	 *
	 * @since   2.0.3
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testPluginDeactivationModal(AcceptanceTester $I)
	{
		$I->activateConvertKitPlugin($I);

		// Deactivate using the 'Deactivate' link on the Plugins screen, which will trigger
		// ConvertKit's deactivation modal.
		$I->click('a#deactivate-convertkit');

		// Wait for modal to appear.
		$I->waitForElementVisible('#convertkit-deactivation-modal');

		// Fill modal fields.
		$I->selectOption('#convertkit-deactivation-modal input[name="reason"]', 'not_working');
		$I->fillField('reason_text', 'Testing');
		$I->click('#convertkit-deactivation-modal input[type="submit"]');

		// Check that the Plugin deactivated successfully.
		$I->seePluginDeactivated('convertkit');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);
	}

	/**
	 * Test that the ConvertKit Plugin deactivates when clicking the Deactivate link on the Plugins
	 * screen, ignoring the form fields on the deactivational modal and then submitting.
	 *
	 * @since   2.0.3
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testPluginDeactivationModalWithNoFormFieldValues(AcceptanceTester $I)
	{
		$I->activateConvertKitPlugin($I);

		// Deactivate using the 'Deactivate' link on the Plugins screen, which will trigger
		// ConvertKit's deactivation modal.
		$I->click('a#deactivate-convertkit');

		// Wait for modal to appear.
		$I->waitForElementVisible('#convertkit-deactivation-modal');

		// Submit deactivation modal.
		$I->click('#convertkit-deactivation-modal input[type="submit"]');

		// Check that the Plugin deactivated successfully.
		$I->seePluginDeactivated('convertkit');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);
	}
}
