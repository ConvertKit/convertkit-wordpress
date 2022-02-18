<?php
/**
 * Tests for ConvertKit Forms on WooCommerce Products.
 * 
 * @since 	1.9.6
 */
class WooCommerceProductFormCest
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
		$I->activateThirdPartyPlugin($I, 'woocommerce');
		$I->setupConvertKitPlugin($I);
		$I->enableDebugLog($I);
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
		$I->fillSelect2Field($I, '#select2-wp-convertkit-form-container', 'Default', 'aria-owns');

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
		$I->fillSelect2Field($I, '#select2-wp-convertkit-form-container', 'Default', 'aria-owns');

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

		// Change Form to None.
		$I->fillSelect2Field($I, '#select2-wp-convertkit-form-container', 'None', 'aria-owns');

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
		$I->fillSelect2Field($I, '#select2-wp-convertkit-form-container', $_ENV['CONVERTKIT_API_FORM_NAME'], 'aria-owns');

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

	/**
	 * Deactivate and reset Plugin(s) after each test, if the test passes.
	 * We don't use _after, as this would provide a screenshot of the Plugin
	 * deactivation and not the true test error.
	 * 
	 * @since 	1.9.6.7
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function _passed(AcceptanceTester $I)
	{
		$I->deactivateConvertKitPlugin($I);
		$I->deactivateThirdPartyPlugin($I, 'woocommerce');
		$I->resetConvertKitPlugin($I);
	}
}