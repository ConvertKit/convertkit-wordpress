<?php

class CheckNoErrorsOnBlankInstallCest
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
    }

    /**
	 * Check that no PHP errors or notices are displayed on the Plugin's Setting screen when the Plugin is activated
	 * and not configured.
	 * 
	 * @since 	1.0.0
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
    public function testWarningAndNoticeIsNotDisplayedOnSettingsScreen(AcceptanceTester $I)
    {
    	// Go to the Plugin's Settings Screen.
    	$I->amOnAdminPage('options-general.php?page=_wp_convertkit_settings');

    	// Check that the Settings Screen did load.
    	$I->see('ConvertKit', 'h1');

    	// Check that no PHP warnings or notices were output.
    	$I->checkNoWarningsAndNoticesOnScreen($I);

    	// Go to the Tools tab on the Plugin's Settings Screen.
    	$I->click('Tools', 'a.nav-tab');

    	// Check that the Settings Screen did load.
    	$I->see('ConvertKit', 'h1');

    	// Check that no PHP warnings or notices were output.
    	$I->checkNoWarningsAndNoticesOnScreen($I);
    }

    /**
	 * Check that no errors are displayed on Pages > Add New
	 * 
	 * @since 	1.0.0
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
    public function testAddNewPage(AcceptanceTester $I)
    {
        // Navigate to Pages > Add New
        $I->amOnAdminPage('post-new.php?post_type=page');

        // Check that no PHP warnings or notices were output.
    	$I->checkNoWarningsAndNoticesOnScreen($I);
    }

    /**
	 * Check that no errors are displayed on Posts > Add New
	 * 
	 * @since 	1.0.0
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
    public function testAddNewPost(AcceptanceTester $I)
    {
        // Navigate to Pages > Add New
        $I->amOnAdminPage('post-new.php');

        // Check that no PHP warnings or notices were output.
    	$I->checkNoWarningsAndNoticesOnScreen($I);
    }

    /**
	 * Check that no errors are displayed on Posts > Categories > Edit Uncategorized
	 * 
	 * @since 	1.0.0
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
    public function testEditCategory(AcceptanceTester $I)
    {
    	// Navigate to Posts > Categories > Edit Uncategorized
        $I->amOnAdminPage('term.php?taxonomy=category&tag_ID=1');

        // Check that no PHP warnings or notices were output.
    	$I->checkNoWarningsAndNoticesOnScreen($I);
    }
}