<?php
/**
 * Tests for ConvertKit Forms on WooCommerce Products.
 * 
 * @since 	1.9.6
 */
class IntegrationWooCommerceProductFormCest
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
		// Add a Product using the Classic Editor.
		$I->addClassicEditorPage($I, 'product', 'ConvertKit: Product: Form: Default: None');

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

		// Add a Product using the Classic Editor.
		$I->addClassicEditorPage($I, 'product', 'ConvertKit: Product: Form: Default');

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
		// Don't use addClassicEditorPage(); on WooCommerce Products, it results in the Publish button no longer working
		// for some inexplicible reason.
		$I->amOnAdminPage('post-new.php?post_type=product');

		// Change Form to None.
		$I->fillSelect2Field($I, '#select2-wp-convertkit-form-container', 'None', 'aria-owns');

		// Define a Product Title.
		$I->fillField('#title', 'ConvertKit: Product: Form: None');

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
		// Don't use addClassicEditorPage(); on WooCommerce Products, it results in the Publish button no longer working
		// for some inexplicible reason.
		$I->amOnAdminPage('post-new.php?post_type=product');

		// Define a Product Title.
		$I->fillField('#title', 'ConvertKit: Product: Form: Defined');

		// Change Form to Form setting in .env file.
		$I->fillSelect2Field($I, '#select2-wp-convertkit-form-container', $_ENV['CONVERTKIT_API_FORM_NAME'], 'aria-owns');

		// Define a Product Title.
		$I->fillField('#title', 'ConvertKit: Product: Form: Defined');

		// Publish and view the Product.
		$I->publishAndViewClassicEditorPage($I);

		// Confirm that the ConvertKit Form displays.
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