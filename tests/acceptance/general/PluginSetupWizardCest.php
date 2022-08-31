<?php

class PluginSetupWizardCest
{
	/**
	 * Test that the Setup Wizard displays when the Plugin is activated.
	 * 
	 * @since 	1.9.8.5
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testSetupWizardDisplays(AcceptanceTester $I)
	{
		// Activate Plugin.
		$this->_activatePlugin($I);

		// Confirm setup wizard is displayed.

	}

	/**
	 * Test that the Setup Wizard displays when the Plugin is activated on a site
	 * where the Plugin has previously been activated and configured with API Keys.
	 * 
	 * @since 	1.9.8.5
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testSetupWizardDoesNotDisplayWhenConfigured(AcceptanceTester $I)
	{
		// Define Plugin settings as if the Plugin were previously activated and configured.
		$I->haveOptionInDatabase('_wp_convertkit_settings', [
			'api_key'    => $_ENV['CONVERTKIT_API_KEY'],
			'api_secret' => $_ENV['CONVERTKIT_API_SECRET'],
			'debug'      => 'on',
			'no_scripts' => '',
			'no_css'     => '',
		]);

		// Activate Plugin.
		$this->_activatePlugin($I);

		// Confirm setup wizard does not display.

	}

	/**
	 * Tests each screen on the Setup Wizard, covering:
	 * - expected buttons and text are displayed,
	 * - buttons link to expected locations,
	 * - completed steps are marked as completed,
	 * - next / back buttons are / are not displayed as necessary,
	 * - configuration or misconfiguration results in expected outcomes.
	 * 
	 * @since 	1.9.8.5
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testSetupWizardScreens(AcceptanceTester $I)
	{
		/**
		 * Setup
		 */
		// Test Register and Connect buttons.

		// Test Exit wizard link.

		/**
		 * Connect Account
		 */
		// Confirm expected buttons display.

		// Confirm Exit wizard link displays.

		// Check click here links work.

		// Test invalid API credentials.

		// Test valid API credentials.

		/**
		 * Form Configuration
		 */
		// Confirm expected buttons display.

		// Confirm Exit wizard link displays.

		// Select a Form for Posts, and confirm the preview link displays the selected Form.

		// Select a Form for Pages, and confirm the preview link displays the selected Form.
		
		/**
		 * Done
		 */
		// Confirm no next / back buttons display.

		// Confirm expected Dashboard and Plugin Settings buttons displayed.

		// Check settings stored in database.

			
	}

	/**
	 * Activate the Plugin, without checking it is activated, so that its Setup Wizard
	 * screen loads.
	 * 
	 * This differs from the activateConvertKitPlugin() method, which will ignore a Setup Wizard
	 * screen by reloading the Plugins screen to confirm a Plugin's activation.
	 * 
	 * @since 	1.9.8.5
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	private function _activatePlugin(AcceptanceTester $I)
	{
		$I->loginAsAdmin();
		$I->amOnPluginsPage();
		$I->activatePlugin($name);
	}

	/**
	 * Deactivate and reset Plugin(s) after each test, if the test passes.
	 * We don't use _after, as this would provide a screenshot of the Plugin
	 * deactivation and not the true test error.
	 * 
	 * @since 	1.9.8.5
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function _passed(AcceptanceTester $I)
	{
		$I->deactivateConvertKitPlugin($I);
		$I->resetConvertKitPlugin($I);
	}
}