<?php

class PluginSettingsGeneralCest
{
	/**
	 * Run common actions before running the test functions in this class.
	 * 
	 * @since 	1.0.0
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
    public function _before(AcceptanceTester $I)
    {
    	// Login as the Administrator
    	$I->loginAsAdmin();

    	// Go to the Plugins screen in the WordPress Administration interface.
        $I->amOnPluginsPage();

        // Activate the Plugin.
        $I->activatePlugin('convertkit');

        // Check that the Plugin activated successfully.
        $I->seePluginActivated('convertkit');
    }

    /**
	 * Test that no PHP errors or notices are displayed on the Plugin's Setting screen when the Save Changes
	 * button is pressed and no settings are specified.
	 * 
	 * @since 	1.0.0
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
    public function testSaveBlankSettings(AcceptanceTester $I)
    {
    	// Go to the Plugin's Settings Screen.
    	$I->amOnAdminPage('options-general.php?page=_wp_convertkit_settings');

    	// Click the Save Changes button.
    	$I->click('Save Changes');

    	// Check that no PHP warnings or notices were output.
    	$I->checkNoWarningsAndNoticesOnScreen($I);
    }

    /**
	 * Test that no PHP errors or notices are displayed on the Plugin's Setting screen,
	 * and a warning is displayed that the supplied API credentials are invalid, when
	 * saving invalid API credentials.
	 * 
	 * @since 	1.0.0
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
    public function testSaveInvalidAPICredentials(AcceptanceTester $I)
    {
    	// Go to the Plugin's Settings Screen.
    	$I->amOnAdminPage('options-general.php?page=_wp_convertkit_settings');

    	// Complete API Fields.
    	$I->fillField('_wp_convertkit_settings[api_key]', 'fakeApiKey');
		$I->fillField('_wp_convertkit_settings[api_secret]', 'fakeApiSecret');

    	// Click the Save Changes button.
    	$I->click('Save Changes');

    	// Check that no PHP warnings or notices were output.
    	$I->checkNoWarningsAndNoticesOnScreen($I);
    }

    /**
	 * Test that no PHP errors or notices are displayed on the Plugin's Setting screen,
	 * when valid API credentials are saved.
	 * 
	 * @since 	1.0.0
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
    public function testSaveValidAPICredentials(AcceptanceTester $I)
    {
    	// Go to the Plugin's Settings Screen.
    	$I->amOnAdminPage('options-general.php?page=_wp_convertkit_settings');

    	// Complete API Fields.
    	$I->fillField('_wp_convertkit_settings[api_key]', $_ENV['CONVERTKIT_API_KEY']);
		$I->fillField('_wp_convertkit_settings[api_secret]', $_ENV['CONVERTKIT_API_SECRET']);

    	// Click the Save Changes button.
    	$I->click('Save Changes');

    	// Check that no PHP warnings or notices were output.
    	$I->checkNoWarningsAndNoticesOnScreen($I);

    	// Check the value of the fields match the inputs provided.
    	$I->seeInField('_wp_convertkit_settings[api_key]', $_ENV['CONVERTKIT_API_KEY']);
    	$I->seeInField('_wp_convertkit_settings[api_secret]', $_ENV['CONVERTKIT_API_SECRET']);
    }

    /**
	 * Test that no PHP errors or notices are displayed on the Plugin's Setting screen,
	 * when the Default Form is changed.
	 * 
	 * @since 	1.0.0
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
    public function testChangeDefaultFormSetting(AcceptanceTester $I)
    {
    	// Save API Credentials.
    	$this->testSaveValidAPICredentials($I);

    	// Change form from 'Default' to the name of the form in the .env file.
    	$I->selectOption('#default_form', $_ENV['CONVERTKIT_API_FORM_NAME']);

    	// Click the Save Changes button.
    	$I->click('Save Changes');

    	// Check that no PHP warnings or notices were output.
    	$I->checkNoWarningsAndNoticesOnScreen($I);

    	// Check the value of the Default Form field matches the input provided.
    	$I->seeOptionIsSelected('#default_form', $_ENV['CONVERTKIT_API_FORM_NAME']);
    }

    /**
	 * Test that no PHP errors or notices are displayed on the Plugin's Setting screen,
	 * when Debug or Disable JavaScript settings are enabled.
	 * 
	 * @since 	1.0.0
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
    public function testEnableDebugAndDisableJavaScriptSettings(AcceptanceTester $I)
    {
    	// Go to the Plugin's Settings Screen.
    	$I->amOnAdminPage('options-general.php?page=_wp_convertkit_settings');

    	// Tick fields.
    	$I->checkOption('#debug');
		$I->checkOption('#no_scripts');

    	// Click the Save Changes button.
    	$I->click('Save Changes');

    	// Check that no PHP warnings or notices were output.
    	$I->checkNoWarningsAndNoticesOnScreen($I);

    	// Check the fields are ticked.
    	$I->seeCheckboxIsChecked('#debug');
    	$I->seeCheckboxIsChecked('#no_scripts');	
    }
}