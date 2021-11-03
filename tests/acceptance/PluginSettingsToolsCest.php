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

    	// Go to the Plugin's Settings > Tools Screen.
    	$I->loadConvertKitSettingsToolsScreen($I);
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