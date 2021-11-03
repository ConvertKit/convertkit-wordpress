<?php
/**
 * Tests for creating and editing Pages in the WordPress Administration.
 * 
 * @since 	1.0.0
 */
class AdminPageCest
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
	 * Test Pages > Add New when the Plugin is activated but not configured.
	 * 
	 * @since 	1.0.0
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
    public function testAddNewPageWithoutPluginSetup(AcceptanceTester $I)
    {
        // Navigate to Pages > Add New
        $I->amOnAdminPage('post-new.php?post_type=page');

        // Check that no PHP warnings or notices were output.
    	$I->checkNoWarningsAndNoticesOnScreen($I);

    	// Check that the ConvertKit Meta Box exists
    	$I->seeElement('#wp-convertkit-meta-box');
    }

    /**
	 * Test Pages > Add New when the Plugin is activated and configured.
	 * 
	 * @since 	1.0.0
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
    public function testAddNewPageWithPluginSetup(AcceptanceTester $I)
    {
    	// Setup Plugin.
    	$I->setupConvertKitPlugin($I);

        // Navigate to Pages > Add New
        $I->amOnAdminPage('post-new.php?post_type=page');

        // Check that no PHP warnings or notices were output.
    	$I->checkNoWarningsAndNoticesOnScreen($I);

    	// Check that the ConvertKit Meta Box exists
    	$I->seeElement('#wp-convertkit-meta-box');
    }
}