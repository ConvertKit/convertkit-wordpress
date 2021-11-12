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
		$I->activateConvertKitPlugin($I);
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
		$I->loadConvertKitSettingsGeneralScreen($I);

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
		$I->loadConvertKitSettingsGeneralScreen($I);

		// Complete API Fields with incorrect data.
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
		$I->setupConvertKitPlugin($I);
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
		// Setup Plugin.
		$I->setupConvertKitPlugin($I);

		// Go to the Plugin's Settings Screen.
		$I->loadConvertKitSettingsGeneralScreen($I);

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
	 * Test that no PHP errors or notices are displayed on the Plugin's Setting screen
	 * when Debug settings are enabled and disabled.
	 * 
	 * @since 	1.0.0
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testEnableAndDisableDebugSettings(AcceptanceTester $I)
	{
		// Setup API Keys in ConvertKit Plugin.
		$I->setupConvertKitPlugin($I);

		// Go to the Plugin's Settings Screen.
		$I->loadConvertKitSettingsGeneralScreen($I);
		
		// Tick field.
		$I->checkOption('#debug');

		// Click the Save Changes button.
		$I->click('Save Changes');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check the field remains ticked.
		$I->seeCheckboxIsChecked('#debug');	

		// Untick field.
		$I->uncheckOption('#debug');

		// Click the Save Changes button.
		$I->click('Save Changes');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check the field remains unticked.
		$I->dontSeeCheckboxIsChecked('#debug');	
	}

	/**
	 * Test that no PHP errors or notices are displayed on the Plugin's Setting screen
	 * when the Disable JavaScript settings are enabled and disabled.
	 * 
	 * @since 	1.0.0
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testEnableAndDisableJavaScriptSettings(AcceptanceTester $I)
	{
		// Go to the Plugin's Settings Screen.
		$I->loadConvertKitSettingsGeneralScreen($I);
		
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