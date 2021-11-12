<?php
/**
 * Tests for the ConvertKit Form shortcode.
 * 
 * @since 	1.9.6
 */
class PageShortcodeFormCest
{
	/**
	 * Run common actions before running the test functions in this class.
	 * 
	 * @since 	1.9.6
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
    public function _before(AcceptanceTester $I)
    {
    	$I->activateConvertKitPlugin($I);
    	$I->setupConvertKitPlugin($I);
    }

    /**
	 * Test the [convertkit form] shortcode works when a valid Form ID is specified.
	 * 
	 * @since 	1.9.6
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
    public function testFormShortcodeWithValidFormParameter(AcceptanceTester $I)
    {
    	// Create Page with Shortcode.
    	$I->havePageInDatabase([
    		'post_name' 	=> 'convertkit-form-shortcode-valid-form-param',
    		'post_content' 	=> '[convertkit form=' . $_ENV['CONVERTKIT_API_FORM_ID'] . ']',
    	]);

		// Load the Page on the frontend site.
	    $I->amOnPage('/convertkit-form-shortcode-valid-form-param');

	    // Check that no PHP warnings or notices were output.
    	$I->checkNoWarningsAndNoticesOnScreen($I);

	    // Confirm that the ConvertKit Form is displayed.
	    $I->seeElementInDOM('form[data-sv-form]');
    }

    /**
	 * Test the [convertkit form] shortcode does not output errors when an invalid Form ID is specified.
	 * 
	 * @since 	1.9.6
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
    public function testFormShortcodeWithInvalidFormParameter(AcceptanceTester $I)
    {
    	// Create Page with Shortcode.
    	$I->havePageInDatabase([
    		'post_name' 	=> 'convertkit-form-shortcode-invalid-form-param',
    		'post_content' 	=> '[convertkit form=1]',
    	]);

		// Load the Page on the frontend site.
	    $I->amOnPage('/convertkit-form-shortcode-invalid-form-param');

	    // Check that no PHP warnings or notices were output.
    	$I->checkNoWarningsAndNoticesOnScreen($I);

	    // Confirm that the ConvertKit Form is not displayed.
	    $I->dontSeeElementInDOM('form[data-sv-form]');
    }

    /**
	 * Test the [convertkit id] shortcode works when a valid Form ID is specified.
	 * 
	 * @since 	1.9.6
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
    public function testFormShortcodeWithValidIDParameter(AcceptanceTester $I)
    {
    	// Create Page with Shortcode.
    	$I->havePageInDatabase([
    		'post_name' 	=> 'convertkit-form-shortcode-valid-id-param',
    		'post_content' 	=> '[convertkit id=' . $_ENV['CONVERTKIT_API_FORM_ID'] . ']',
    	]);

		// Load the Page on the frontend site.
	    $I->amOnPage('/convertkit-form-shortcode-valid-id-param');

	    // Check that no PHP warnings or notices were output.
    	$I->checkNoWarningsAndNoticesOnScreen($I);

	    // Confirm that the ConvertKit Form is displayed.
	    $I->seeElementInDOM('form[data-sv-form]');
    }

    /**
	 * Test the [convertkit form] shortcode does not output errors when an invalid Form ID is specified.
	 * 
	 * @since 	1.9.6
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
    public function testFormShortcodeWithInvalidIDParameter(AcceptanceTester $I)
    {
    	// Create Page with Shortcode.
    	$I->havePageInDatabase([
    		'post_name' 	=> 'convertkit-form-shortcode-invalid-id-param',
    		'post_content' 	=> '[convertkit id=1]',
    	]);

		// Load the Page on the frontend site.
	    $I->amOnPage('/convertkit-form-shortcode-invalid-id-param');

	    // Check that no PHP warnings or notices were output.
    	$I->checkNoWarningsAndNoticesOnScreen($I);

	    // Confirm that the ConvertKit Form is not displayed.
	    $I->dontSeeElementInDOM('form[data-sv-form]');
    }
}