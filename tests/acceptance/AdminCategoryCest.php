<?php
/**
 * Tests for creating and editing Posts > Categories in the WordPress Administration.
 * 
 * @since 	1.0.0
 */
class AdminCategoryCest
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
	 * Test Posts > Categories when the Plugin is activated but not configured.
	 * 
	 * @since 	1.0.0
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
    public function testAddNewCategoryWithoutPluginSetup(AcceptanceTester $I)
    {
        // Navigate to Posts > Categories
        $I->amOnAdminPage('edot-tags.php?taxonomy=category');

        // Check that no PHP warnings or notices were output.
    	$I->checkNoWarningsAndNoticesOnScreen($I);
    }

    /**
	 * Test Posts > Categories when the Plugin is activated and configured.
	 * 
	 * @since 	1.0.0
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
    public function testAddNewCategoryWithPluginSetup(AcceptanceTester $I)
    {
    	// Setup Plugin.
    	$I->setupConvertKitPlugin($I);

        // Navigate to Posts > Categories
        $I->amOnAdminPage('edot-tags.php?taxonomy=category');

        // Check that no PHP warnings or notices were output.
    	$I->checkNoWarningsAndNoticesOnScreen($I);
    }
}