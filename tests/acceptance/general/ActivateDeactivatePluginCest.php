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
		$I->setupConvertKitPlugin($I);

		// Deactivate this Plugin.
		$I->deactivateConvertKitPlugin($I);
	}

	/**
	 * Test that the ConvertKit Plugin deactivates when clicking the Deactivate link on the Plugins
	 * screen, completing the form fields on the deactivational modal and then submitting.
	 *
	 * @since   2.2.3
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

		// Select each reason, ensuring the text input's placeholder updates.
		$reasons = [
			'temporary'          => 'What problem are you experiencing?',
			'not_working'        => 'What problem are you experiencing?',
			'better_alternative' => 'What\'s the plugin\'s name?',
			'not_required'       => 'What\'s one thing we could improve?',
			'other'              => 'What can we do better?',
		];
		foreach ($reasons as $reason => $placeholder) {
			// Confirm reason can be selected as a radio button.
			$I->selectOption('#convertkit-deactivation-modal input[name="convertkit-deactivation-reason"]', $reason);

			// Confirm input field's placeholder has updated, based on the selected reason.
			$I->assertEquals(
				$I->grabAttributeFrom('input[name="convertkit-deactivation-reason-text"]', 'placeholder'),
				$placeholder
			);
		}

		// Complete the input field and submit.
		$I->fillField('convertkit-deactivation-reason-text', 'Testing');
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
	 * @since   2.2.3
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
