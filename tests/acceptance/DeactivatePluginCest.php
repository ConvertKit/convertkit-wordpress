<?php

class DeactivatePluginCest
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
	 * Deactivate the Plugin and confirm a success notification
	 * is displayed with no errors.
	 * 
	 * @since 	1.0.0
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
    public function testPluginDeactivation(AcceptanceTester $I)
    {
        // Go to the Plugins screen in the WordPress Administration interface.
        $I->amOnPluginsPage();

        // Deactivate the Plugin.
        $I->deactivatePlugin('convertkit');

        // Check that the Plugin deactivated successfully.
        $I->seePluginDeactivated('convertkit');

        // Check that no PHP warnings or notices were output.
    	$I->checkNoWarningsAndNoticesOnScreen($I);
    }
}
