<?php
/**
 * Tests for creating and editing Posts > Categories in the WordPress Administration.
 * 
 * @since 	1.0.0
 */
class CategoryFormCest
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
}