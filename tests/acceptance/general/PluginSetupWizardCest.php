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
		$I->see('Welcome to the ConvertKit Setup Wizard');
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
		$I->dontSee('Welcome to the ConvertKit Setup Wizard');
	}

	/**
	 * Test that the Setup Wizard exit link works.
	 * 
	 * @since 	1.9.8.5
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testSetupWizardExitLink(AcceptanceTester $I)
	{
		// Activate Plugin.
		$this->_activatePlugin($I);

		// Confirm expected setup wizard screen is displayed.
		$this->_seeExpectedSetupWizardScreen($I, 1, 'Welcome to the ConvertKit Setup Wizard');

		// Click Exit wizard link.
		$I->click('Exit wizard');

		// Confirm exit.
		$I->acceptPopup();

		// Confirm Plugin settings screen loaded.
		$I->seeInCurrentUrl('options-general.php?page=_wp_convertkit_settings');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);
	}

	/**
	 * Test that the Setup Wizard > Setup > Register button works.
	 *
	 * @since 	1.9.8.5
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testSetupWizardSetupScreenRegisterButton(AcceptanceTester $I)
	{
		// Activate Plugin.
		$this->_activatePlugin($I);

		// Confirm expected setup wizard screen is displayed.
		$this->_seeExpectedSetupWizardScreen($I, 1, 'Welcome to the ConvertKit Setup Wizard');

		// Test Register button opens a new tab.
		$I->click('Register');
		$I->wait(2); // Required, otherwise switchToNextTab fails.
		$I->switchToNextTab();
		$I->seeInCurrentUrl('users/signup?utm_source=wordpress&utm_content=convertkit');

		// Close newly opened tab from above button.
		$I->closeTab();

		// Confirm that the original tab is now displaying the Connect Account screen.
		$this->_seeExpectedSetupWizardScreen($I, 2, 'Connect your ConvertKit account');
	}

	/**
	 * Test that the Setup Wizard > Setup > Connect button works.
	 *
	 * @since 	1.9.8.5
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testSetupWizardSetupScreenConnectButton(AcceptanceTester $I)
	{
		// Activate Plugin.
		$this->_activatePlugin($I);

		// Confirm expected setup wizard screen is displayed.
		$this->_seeExpectedSetupWizardScreen($I, 1, 'Welcome to the ConvertKit Setup Wizard');

		// Test Connect button
		$I->click('Connect');

		// Confirm expected setup wizard screen is displayed.
		$this->_seeExpectedSetupWizardScreen($I, 2, 'Connect your ConvertKit account');
	}

	/**
	 * Test that the Setup Wizard > Connect Account screen works as expected when valid API credentials
	 * are specified.
	 *
	 * @since 	1.9.8.5
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testSetupWizardConnectAccountScreen(AcceptanceTester $I)
	{
		// Activate Plugin.
		$this->_activatePlugin($I);

		// Confirm expected setup wizard screen is displayed.
		$this->_seeExpectedSetupWizardScreen($I, 1, 'Welcome to the ConvertKit Setup Wizard');

		// Test Connect button
		$I->click('Connect');

		// Confirm expected setup wizard screen is displayed.
		$this->_seeExpectedSetupWizardScreen($I, 2, 'Connect your ConvertKit account');

		// Fill fields with invalid API Keys.
		$I->fillField('api_key', $_ENV['CONVERTKIT_API_KEY']);
		$I->fillField('api_secret', $_ENV['CONVERTKIT_API_SECRET']);

		// Click Connect button.
		$I->click('Connect');

		// Confirm expected setup wizard screen is displayed.
		$this->_seeExpectedSetupWizardScreen($I, 3, 'Display an email capture form');
	}

	/**
	 * Test that the Setup Wizard > Connect Account screen works as expected when invalid API credentials
	 * are specified.
	 *
	 * @since 	1.9.8.5
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testSetupWizardConnectAccountScreenWithInvalidAPICredentials(AcceptanceTester $I)
	{
		// Activate Plugin.
		$this->_activatePlugin($I);

		// Confirm expected setup wizard screen is displayed.
		$this->_seeExpectedSetupWizardScreen($I, 1, 'Welcome to the ConvertKit Setup Wizard');

		// Test Connect button
		$I->click('Connect');

		// Confirm expected setup wizard screen is displayed.
		$this->_seeExpectedSetupWizardScreen($I, 2, 'Connect your ConvertKit account');

		// Fill fields with invalid API Keys.
		$I->fillField('api_key', 'fakeApiKey');
		$I->fillField('api_secret', 'fakeApiSecret');

		// Click Connect button.
		$I->click('Connect');

		// Confirm expected setup wizard screen is still displayed.
		$this->_seeExpectedSetupWizardScreen($I, 2, 'Connect your ConvertKit account');

		// Confirm error notification is displayed.
		$I->seeElement('div.notice.notice-error.is-dismissible');

		// Dismiss notification.
		$I->click('div.notice-error button.notice-dismiss');

		// Confirm notification no longer displayed.
		$I->wait(1);
		$I->dontSeeElement('div.notice.notice-error.is-dismissible');
	}

	/**
	 * Test that the Setup Wizard > Form Configuration screen works as expected.
	 *
	 * @since 	1.9.8.5
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testSetupWizardFormConfigurationScreen(AcceptanceTester $I)
	{
		// Activate Plugin.
		$this->_activatePlugin($I);

		// Define Plugin settings.
		$I->haveOptionInDatabase('_wp_convertkit_settings', [
			'api_key'    => $_ENV['CONVERTKIT_API_KEY'],
			'api_secret' => $_ENV['CONVERTKIT_API_SECRET'],
		]);

		// Load Step 3/4.
		$I->amOnAdminPage('index.php?page=convertkit-setup&step=3');

		// Confirm expected setup wizard screen is displayed.
		$this->_seeExpectedSetupWizardScreen($I, 3, 'Display an email capture form');

		// Select a Post Form.
		$I->fillSelect2Field($I, '#select2-wp-convertkit-form-posts-container', $_ENV['CONVERTKIT_API_FORM_NAME']);

		// Open preview.
		$I->click('a#convertkit-preview-form-post');
		$I->wait(2); // Required, otherwise switchToNextTab fails.

		// Switch to newly opened tab.
		$I->switchToNextTab();

		// Confirm expected Form is displayed.
		$I->seeElementInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_ID'] . '"]');

		// Close newly opened tab.
		$I->closeTab();

		// Select a Page Form.
		$I->fillSelect2Field($I, '#select2-wp-convertkit-form-page-container', $_ENV['CONVERTKIT_API_FORM_NAME']);

		// Open preview.
		$I->click('a#convertkit-preview-form-page');
		$I->wait(2); // Required, otherwise switchToNextTab fails.

		// Switch to newly opened tab.
		$I->switchToNextTab();

		// Confirm expected Form is displayed.
		$I->seeElementInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_ID'] . '"]');

		// Close newly opened tab.
		$I->closeTab();

		// Click Finish Setup button.
		$I->click('Finish Setup');

		// Confirm expected setup wizard screen is displayed.
		$this->_seeExpectedSetupWizardScreen($I, 4, 'Setup complete');
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
		$I->activatePlugin('convertkit');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);
	}

	/**
	 * Runs tests on a Setup Wizard screen, to confirm that the expected step, title and buttons
	 * are displayed.
	 * 
	 * @since 	1.9.8.5
	 * 
	 * @param 	AcceptanceTester 	$I 		Tester
	 * @param 	int 				$step 	Current step
	 * @param 	string 				$title 	Expected title
	 */
	private function _seeExpectedSetupWizardScreen(AcceptanceTester $I, $step, $title)
	{
		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm expected setup wizard screen loaded.
		$I->seeInCurrentUrl('index.php?page=convertkit-setup');
		
		// Confirm expected title is displayed.
		$I->see($title);

		// Confirm current and previous steps are highlighted as 'done'.
		// @TODO.

		// Confirm Step text is correct.
		$I->see('Step '.$step.' of 4');

		// Depending on the step, confirm previous/next buttons exist with expected links.
		// @TODO.
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