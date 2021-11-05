<?php

class PluginSettingsToolsCest
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
	 * Test that the Debug Log section is populated when debugging is enabled and an action is
	 * performed that will populate the log.
	 * 
	 * @since 	1.0.0
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
    public function testDebugLogExists(AcceptanceTester $I)
    {
    	// Go to the Plugin's Settings Screen.
    	$I->loadConvertKitSettingsGeneralScreen($I);

    	// Complete API Fields and Debugging
    	$I->fillField('_wp_convertkit_settings[api_key]', $_ENV['CONVERTKIT_API_KEY']);
		$I->fillField('_wp_convertkit_settings[api_secret]', $_ENV['CONVERTKIT_API_SECRET']);
    	$I->checkOption('#debug');

    	// Click the Save Changes button.
    	$I->click('Save Changes');

    	// Check that no PHP warnings or notices were output.
    	$I->checkNoWarningsAndNoticesOnScreen($I);

    	// Go to the Plugin's Settings > Tools Screen.
    	$I->loadConvertKitSettingsToolsScreen($I);

    	// Check that no PHP warnings or notices were output.
    	$I->checkNoWarningsAndNoticesOnScreen($I);

    	// Check that the Debug Log textarea contains some expected output i.e.
    	// does not show the 'No logs have been generated.' message.
    	$I->dontSeeInField('textarea[name="convertkit-debug-log-contents"]', 'No logs have been generated.');
    }

    /**
	 * Test that the System Info section is populated.
	 * 
	 * @since 	1.0.0
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
    public function testSystemInfoExists(AcceptanceTester $I)
    {
    	// Go to the Plugin's Settings > Tools Screen.
    	$I->loadConvertKitSettingsToolsScreen($I);

    	// Check that the System Info textarea contains some expected output.
    	$I->seeFieldContains($I, '#system-info-textarea', '### Begin System Info ###');
    	$I->seeFieldContains($I, '#system-info-textarea', '### End System Info ###');
    }
}