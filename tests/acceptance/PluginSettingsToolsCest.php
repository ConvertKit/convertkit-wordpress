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
    	// Login as the Administrator
    	$I->loginAsAdmin();

    	// Go to the Plugins screen in the WordPress Administration interface.
        $I->amOnPluginsPage();

        // Activate the Plugin.
        $I->activatePlugin('convertkit');

        // Check that the Plugin activated successfully.
        $I->seePluginActivated('convertkit');

        // Go to the Plugin's Settings Screen.
    	$I->amOnAdminPage('options-general.php?page=_wp_convertkit_settings&tab=tools');

    	// Check that no PHP warnings or notices were output.
    	$I->checkNoWarningsAndNoticesOnScreen($I);
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
    	// Check that the System Info textarea contains some expected output.
    	$I->seeFieldContains($I, '#system-info-textarea', '### Begin System Info ###');
    	$I->seeFieldContains($I, '#system-info-textarea', '### End System Info ###');
    }
}