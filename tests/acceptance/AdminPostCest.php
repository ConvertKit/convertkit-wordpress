<?php
/**
 * Tests for creating and editing Posts in the WordPress Administration.
 * 
 * @since 	1.0.0
 */
class AdminPostCest
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
	 * Test Posts > Add New when the Plugin is activated but not configured.
	 * 
	 * @since 	1.0.0
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
    public function testAddNewPostWithoutPluginSetup(AcceptanceTester $I)
    {
        // Navigate to Posts > Add New
        $I->amOnAdminPage('post-new.php');

        // Check that no PHP warnings or notices were output.
    	$I->checkNoWarningsAndNoticesOnScreen($I);
    }

    /**
	 * Test Posts > Add New when the Plugin is activated and configured.
	 * 
	 * @since 	1.0.0
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
    public function testAddNewPostWithPluginSetup(AcceptanceTester $I)
    {
    	// Setup Plugin.
    	$I->setupConvertKitPlugin($I);
    	
        // Navigate to Posts > Add New
        $I->amOnAdminPage('post-new.php');

        // Check that no PHP warnings or notices were output.
    	$I->checkNoWarningsAndNoticesOnScreen($I);
    }

}