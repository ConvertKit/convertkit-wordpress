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
        $I->activateConvertKitPlugin($I);
    }
}