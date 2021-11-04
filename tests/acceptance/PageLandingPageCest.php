<?php
/**
 * Tests for ConvertKit Landing Pages on WordPress Pages.
 * 
 * @since 	1.0.0
 */
class PageLandingPageCest
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
    	$I->setupConvertKitPlugin($I);
    }

    /**
	 * Test that 'None' Landing Page specified in the Page Settings works when
	 * creating and viewing a new WordPress Page.
	 * 
	 * @since 	1.0.0
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
    public function testAddNewPageUsingNoLandingPage(AcceptanceTester $I)
    {
    	// Navigate to Pages > Add New
        $I->amOnAdminPage('post-new.php?post_type=page');

        // Check that no PHP warnings or notices were output.
    	$I->checkNoWarningsAndNoticesOnScreen($I);

    	// Change Landing Page to 'None'
    	$I->selectOption('#wp-convertkit-landing_page', 'None');

    	// Define a Page Title.
    	$I->fillField('#post-title-0', 'ConvertKit: Landing Page: None');

    	// Click the Publish button.
    	$I->click('.editor-post-publish-button__button');

    	// When the pre-publish panel displays, click Publish again.
    	$I->performOn('.editor-post-publish-panel__prepublish', function($I) {
    		$I->click('.editor-post-publish-panel__header-publish-button .editor-post-publish-button__button');	
    	});

    	// Check the value of the Landing Page field matches the input provided.
    	$I->performOn( '.post-publish-panel__postpublish-buttons', function($I) {
    		$I->seeOptionIsSelected('#wp-convertkit-landing_page', 'None');
    	});

	    // Load the Page on the frontend site.
	    $I->amOnPage('/convertkit-landing-page-none');

        // Check that no PHP warnings or notices were output.
    	$I->checkNoWarningsAndNoticesOnScreen($I);

	    // Confirm that no ConvertKit Landing Page is displayed.
	    $I->dontSeeElementInDOM('form[data-sv-form]');
    }

    /**
	 * Test that the Landing Page specified in the Page Settings works when
	 * creating and viewing a new WordPress Page.
	 * 
	 * @since 	1.0.0
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
    public function testAddNewPageUsingDefinedLandingPage(AcceptanceTester $I)
    {
    	// Navigate to Pages > Add New
        $I->amOnAdminPage('post-new.php?post_type=page');

        // Check that no PHP warnings or notices were output.
    	$I->checkNoWarningsAndNoticesOnScreen($I);

    	// Change Landing Page to value specified in the .env file.
    	$I->selectOption('#wp-convertkit-landing_page', $_ENV['CONVERTKIT_API_LANDING_PAGE_NAME']);

    	// Define a Page Title.
    	$I->fillField('#post-title-0', 'ConvertKit: Landing Page: Specific');

    	// Click the Publish button.
    	$I->click('.editor-post-publish-button__button');
    	
    	// When the pre-publish panel displays, click Publish again.
    	$I->performOn('.editor-post-publish-panel__prepublish', function($I) {
    		$I->click('.editor-post-publish-panel__header-publish-button .editor-post-publish-button__button');	
    	});

    	// Check the value of the Landing Page field matches the input provided.
    	$I->performOn( '.post-publish-panel__postpublish-buttons', function($I) {
    		$I->seeOptionIsSelected('#wp-convertkit-landing_page', $_ENV['CONVERTKIT_API_LANDING_PAGE_NAME']);
    	});

    	// Get Landing Page ID.
    	$landingPageID = $I->grabValueFrom('#wp-convertkit-landing_page');

	    // Load the Page on the frontend site
	    $I->amOnPage('/convertkit-landing-page-specific');

        // Check that no PHP warnings or notices were output.
    	$I->checkNoWarningsAndNoticesOnScreen($I);

	    // Confirm that the ConvertKit Landing Page displays.
	    $I->dontSeeElementInDOM('body.page'); // WordPress didn't load its template, which is correct.
	    $I->seeElementInDOM('form[data-sv-form="' . $landingPageID . '"]'); // ConvertKit injected its Landing Page Form, which is correct.
    }
}