<?php

class ActivateDeactivatePluginCest
{
	/**
	 * Activate the Plugin and confirm a success notification
	 * is displayed with no errors.
	 * 
	 * @since 	1.9.6
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testPluginActivation(AcceptanceTester $I)
	{
		$I->activateConvertKitPlugin($I);
	}

	/**
	 * Deactivate the Plugin and confirm a success notification
	 * is displayed with no errors.
	 * 
	 * @since 	1.9.6
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testPluginDeactivation(AcceptanceTester $I)
	{
		$I->deactivateConvertKitPlugin($I);
	}
}