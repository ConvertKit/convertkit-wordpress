<?php
/**
 * Tests for ConvertKit Forms on WooCommerce Products.
 * 
 * @since 	1.9.6
 */
class WooCommerceFormCest
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

    	// Go to the Plugins screen in the WordPress Administration interface.
        $I->amOnPluginsPage();

        // Activate the WooCommerce Plugin.
        $I->activatePlugin('woocommerce');

        // Check that the Plugin activated successfully.
        $I->seePluginActivated('woocommerce');

        // Check that no PHP warnings or notices were output.
    	$I->checkNoWarningsAndNoticesOnScreen($I);
    }

    /**
	 * Test that the 'Default' option for the Default Form setting in the Plugin Settings works when
	 * creating and viewing a new WooCommerce Product, and there is no Default Form specified in the Plugin
	 * settings.
	 * 
	 * @since 	1.9.6
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
    public function testAddNewProductUsingDefaultFormWithNoDefaultFormSpecifiedInPlugin(AcceptanceTester $I)
    {
    	// Navigate to Products > Add New
        $I->amOnAdminPage('post-new.php?post_type=product');

        // Check that no PHP warnings or notices were output.
    	$I->checkNoWarningsAndNoticesOnScreen($I);

    	// Check that the metabox is displayed.
    	$I->seeElementInDOM('#wp-convertkit-meta-box');

    	// Check that the Form option is displayed.
    	$I->seeElementInDOM('#wp-convertkit-form');

    	// Change Form to Default
    	$I->selectOption('#wp-convertkit-form', 'Default');

    	// Define a Product Title.
    	$I->fillField('#title', 'ConvertKit: Form: Default: None');

    	// Click the Publish button.
    	$I->click('#publish');

    	// Check the value of the Form field matches the input provided.
    	$I->seeOptionIsSelected('#wp-convertkit-form', 'Default');

		// Load the Product on the frontend site.
	    $I->amOnPage('/product/convertkit-form-default-none');

        // Check that no PHP warnings or notices were output.
    	$I->checkNoWarningsAndNoticesOnScreen($I);

	    // Confirm that no ConvertKit Form is displayed.
	    $I->dontSeeElementInDOM('form[data-sv-form]');
    }

    /**
	 * Test that the Default Form specified in the Plugin Settings works when
	 * creating and viewing a new WooCommerce Product.
	 * 
	 * @since 	1.9.6
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
    public function testAddNewProductUsingDefaultForm(AcceptanceTester $I)
    {
    	// Specify the Default Form in the Plugin Settings.
    	$defaultFormID = $I->setupConvertKitPluginDefaultFormForWooCommerceProducts($I);

    	// Navigate to Products > Add New
        $I->amOnAdminPage('post-new.php?post_type=product');

        // Check that no PHP warnings or notices were output.
    	$I->checkNoWarningsAndNoticesOnScreen($I);

    	// Check that the metabox is displayed.
    	$I->seeElementInDOM('#wp-convertkit-meta-box');

    	// Check that the Form option is displayed.
    	$I->seeElementInDOM('#wp-convertkit-form');

    	// Change Form to Default
    	$I->selectOption('#wp-convertkit-form', 'Default');

    	// Define a Product Title.
    	$I->fillField('#title', 'ConvertKit: Form: Default');

    	// Click the Publish button.
    	$I->click('#publish');

    	// Check the value of the Form field matches the input provided.
    	$I->seeOptionIsSelected('#wp-convertkit-form', 'Default');

		// Load the Product on the frontend site.
	    $I->amOnPage('/product/convertkit-form-default');

        // Check that no PHP warnings or notices were output.
    	$I->checkNoWarningsAndNoticesOnScreen($I);

	    // Confirm that the ConvertKit Default Form displays.
	    $I->seeElementInDOM('form[data-sv-form="' . $defaultFormID . '"]');
    }

    /**
	 * Test that 'None' Form specified in the Product Settings works when
	 * creating and viewing a new WooCommerce Product.
	 * 
	 * @since 	1.9.6
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
    public function testAddNewProductUsingNoForm(AcceptanceTester $I)
    {
    	// Navigate to Products > Add New
        $I->amOnAdminPage('post-new.php?post_type=product');

        // Check that no PHP warnings or notices were output.
    	$I->checkNoWarningsAndNoticesOnScreen($I);

    	// Check that the metabox is displayed.
    	$I->seeElementInDOM('#wp-convertkit-meta-box');

    	// Check that the Form option is displayed.
    	$I->seeElementInDOM('#wp-convertkit-form');

    	// Change Form to Default
    	$I->selectOption('#wp-convertkit-form', 'None');

    	// Define a Product Title.
    	$I->fillField('#title', 'ConvertKit: Form: None');

    	// Click the Publish button.
    	$I->click('#publish');

    	// Check the value of the Form field matches the input provided.
    	$I->seeOptionIsSelected('#wp-convertkit-form', 'None');

		// Load the Product on the frontend site.
	    $I->amOnPage('/product/convertkit-form-none');

        // Check that no PHP warnings or notices were output.
    	$I->checkNoWarningsAndNoticesOnScreen($I);

	    // Confirm that no ConvertKit Form is displayed.
	    $I->dontSeeElementInDOM('form[data-sv-form]');
    }

    /**
	 * Test that the Form specified in the Product Settings works when
	 * creating and viewing a new WooCommerce Product.
	 * 
	 * @since 	1.9.6
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
    public function testAddNewProductUsingDefinedForm(AcceptanceTester $I)
    {
    	// Navigate to Products > Add New
        $I->amOnAdminPage('post-new.php?post_type=product');

        // Check that no PHP warnings or notices were output.
    	$I->checkNoWarningsAndNoticesOnScreen($I);

    	// Check that the metabox is displayed.
    	$I->seeElementInDOM('#wp-convertkit-meta-box');

    	// Check that the Form option is displayed.
    	$I->seeElementInDOM('#wp-convertkit-form');

    	// Change Form to value specified in the .env file.
    	$I->selectOption('#wp-convertkit-form', $_ENV['CONVERTKIT_API_FORM_NAME']);

    	// Define a Product Title.
    	$I->fillField('#title', 'ConvertKit: Form: Specific');

    	// Click the Publish button.
    	$I->click('#publish');

    	// Check the value of the Form field matches the input provided.
    	$I->seeOptionIsSelected('#wp-convertkit-form', $_ENV['CONVERTKIT_API_FORM_NAME']);

    	// Get Form ID.
    	$formID = $I->grabValueFrom('#wp-convertkit-form');

		// Load the Product on the frontend site.
	    $I->amOnPage('/product/convertkit-form-specific');

        // Check that no PHP warnings or notices were output.
    	$I->checkNoWarningsAndNoticesOnScreen($I);

	    // Confirm that the ConvertKit Form displays.
	    $I->seeElementInDOM('form[data-sv-form="' . $formID . '"]');
    }
}