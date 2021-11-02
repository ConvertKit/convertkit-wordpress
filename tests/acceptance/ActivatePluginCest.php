<?php

class ActivatePluginCest
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
    	$I->loginAsAdmin();
    }

    /**
	 * Activate the Plugin and confirm a success notification
	 * is displayed with no errors.
	 * 
	 * @since 	1.0.0
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
    public function testPluginActivation(AcceptanceTester $I)
    {
        // Go to the Plugins screen in the WordPress Administration interface.
        $I->amOnPluginsPage();

        // Activate the Plugin.
        $I->activatePlugin('convertkit');

        // Check that the Plugin activated successfully.
        $I->seePluginActivated('convertkit');

        // Check that no PHP warnings or notices were output.
    	$I->checkNoWarningsAndNoticesOnScreen($I);
    }
}