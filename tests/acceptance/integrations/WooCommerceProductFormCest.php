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
		// Navigate to Products > Add New.
		$I->amOnAdminPage('post-new.php?post_type=product');

		// Define a Product Title.
		$I->fillField('#title', 'ConvertKit: Product: Form: Default: None');

		// Wait, otherwise publishing will fail in WooCommerce.
		$I->wait(1);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewClassicEditorPage($I);

		// Confirm that a ConvertKit Form is not displayed.
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

		// Navigate to Products > Add New.
		$I->amOnAdminPage('post-new.php?post_type=product');

		// Define a Product Title.
		$I->fillField('#title', 'ConvertKit: Product: Form: Default');

		// Wait, otherwise publishing will fail in WooCommerce.
		$I->wait(1);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewClassicEditorPage($I);

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
		// Navigate to Products > Add New.
		$I->amOnAdminPage('post-new.php?post_type=product');

		// Change Form to None.
		$I->fillSelect2Field($I, '#select2-wp-convertkit-form-container', 'None', 'aria-owns');

		// Define a Product Title.
		$I->fillField('#title', 'ConvertKit: Product: Form: None');

		// Wait, otherwise publishing will fail in WooCommerce.
		$I->wait(1);

		// Publish and view the Product.
		$I->publishAndViewClassicEditorPage($I);

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
		// Navigate to Products > Add New.
		$I->amOnAdminPage('post-new.php?post_type=product');

		// Change Form to Form setting in .env file.
		$I->fillSelect2Field($I, '#select2-wp-convertkit-form-container', $_ENV['CONVERTKIT_API_FORM_NAME'], 'aria-owns');

		// Define a Product Title.
		$I->fillField('#title', 'ConvertKit: Product: Form: Defined');

		// Wait, otherwise publishing will fail in WooCommerce.
		$I->wait(1);

		// Publish and view the Product.
		$I->publishAndViewClassicEditorPage($I);

		// Confirm that the ConvertKit Form displays.
		$I->seeElementInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_ID'] . '"]');
	}

	/**
	 * Test that the Default Form for Products displays when the Default option is chosen via
	 * WordPress' Quick Edit functionality.
	 * 
	 * @since 	1.9.8.0
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testQuickEditUsingDefaultForm(AcceptanceTester $I)
	{
		// Specify the Default Form in the Plugin Settings.
		$I->setupConvertKitPluginDefaultFormForWooCommerceProducts($I);

		// Programmatically create a Product.
		$productID = $I->havePostInDatabase([
			'post_type' 	=> 'product',
			'post_title' 	=> 'ConvertKit: Product: Form: Default: Quick Edit',
		]);

		// Quick Edit the Product in the Products WP_List_Table.
		$I->quickEdit($I, 'product', $productID, [
			'form' => [ 'select', 'Default' ],
		]);

		// Load the Product on the frontend site.
		$I->amOnPage('/?p='.$productID);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the ConvertKit Default Form displays.
		$I->seeElementInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_ID'] . '"]');
	}

	/**
	 * Test that the defined form displays when chosen via
	 * WordPress' Quick Edit functionality.
	 * 
	 * @since 	1.9.8.0
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testQuickEditUsingDefinedForm(AcceptanceTester $I)
	{
		// Programmatically create a Product.
		$productID = $I->havePostInDatabase([
			'post_type' 	=> 'product',
			'post_title' 	=> 'ConvertKit: Product: Form: ' . $_ENV['CONVERTKIT_API_FORM_NAME'] . ': Quick Edit',
		]);

		// Quick Edit the Product in the Products WP_List_Table.
		$I->quickEdit($I, 'product', $productID, [
			'form' => [ 'select', $_ENV['CONVERTKIT_API_FORM_NAME'] ],
		]);

		// Load the Product on the frontend site.
		$I->amOnPage('/?p='.$productID);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the ConvertKit Default Form displays.
		$I->seeElementInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_ID'] . '"]');
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
		$I->deactivateThirdPartyPlugin($I, 'classic-editor');
		$I->deactivateThirdPartyPlugin($I, 'woocommerce');
		$I->resetConvertKitPlugin($I);
	}
}