<?php
/**
 * Tests for the ConvertKit Product's Divi Module.
 *
 * @since   2.5.7
 */
class DiviProductCest
{
	/**
	 * Run common actions before running the test functions in this class.
	 *
	 * @since   2.5.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _before(AcceptanceTester $I)
	{
		$I->activateConvertKitPlugin($I);
		$I->activateThirdPartyPlugin($I, 'divi-builder');
	}

	/**
	 * Test the Product module works when a valid Product is selected
	 * using Divi's backend editor.
	 *
	 * @since   2.5.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testProductModuleInBackendEditor(AcceptanceTester $I)
	{
		// Setup Plugin, without defining default Forms.
		$I->setupConvertKitPluginNoDefaultForms($I);
		$I->setupConvertKitPluginResources($I);

		// Create a Divi Page in the backend editor.
		$I->createDiviPageInBackendEditor($I, 'Kit: Page: Product: Divi: Backend Editor');

		// Insert the Product module.
		$I->insertDiviRowWithModule(
			$I,
			'Kit Product',
			'convertkit_product',
			'product',
			$_ENV['CONVERTKIT_API_PRODUCT_ID']
		);

		// Save Divi module and view the page on the frontend site.
		$I->saveDiviModuleInBackendEditorAndViewPage($I);

		// Confirm that the module displays.
		$I->seeProductOutput($I, $_ENV['CONVERTKIT_API_PRODUCT_URL'], 'Buy my product');

		// Deactivate Classic Editor.
		$I->deactivateThirdPartyPlugin($I, 'classic-editor');
	}

	/**
	 * Test the Product module works when a valid Product is selected
	 * using Divi's backend editor.
	 *
	 * @since   2.5.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testProductModuleInFrontendEditor(AcceptanceTester $I)
	{
		// Setup Plugin, without defining default Forms.
		$I->setupConvertKitPluginNoDefaultForms($I);
		$I->setupConvertKitPluginResources($I);

		// Create a Divi Page in the frontend editor.
		$url = $I->createDiviPageInFrontendEditor($I, 'Kit: Page: Product: Divi: Frontend Editor');

		// Insert the Product module.
		$I->insertDiviRowWithModule(
			$I,
			'Kit Product',
			'convertkit_product',
			'product',
			$_ENV['CONVERTKIT_API_PRODUCT_ID']
		);

		// Save Divi module and view the page on the frontend site.
		$I->saveDiviModuleInFrontendEditorAndViewPage($I, $url);

		// Confirm that the module displays.
		$I->seeProductOutput($I, $_ENV['CONVERTKIT_API_PRODUCT_URL'], 'Buy my product');
	}

	/**
	 * Test the Product module displays the expected message when the Plugin has no credentials
	 *
	 * @since   2.5.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testProductModuleInFrontendEditorWhenNoCredentials(AcceptanceTester $I)
	{
		// Create a Divi Page in the frontend editor.
		$I->createDiviPageInFrontendEditor($I, 'Kit: Page: Product: Divi: Frontend: No Credentials', false);

		// Insert the Product module.
		$I->insertDiviRowWithModule(
			$I,
			'Kit Product',
			'convertkit_product'
		);

		// Confirm the on screen message displays.
		$I->seeInSource('Not connected to Kit');
		$I->seeInSource('Connect your Kit account at Settings > Kit, and then refresh this page to select a product.');
	}

	/**
	 * Test the Product module displays the expected message when the ConvertKit account
	 * has no products.
	 *
	 * @since   2.5.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testProductModuleInFrontendEditorWhenNoProducts(AcceptanceTester $I)
	{
		// Setup Plugin.
		$I->setupConvertKitPluginCredentialsNoData($I);
		$I->setupConvertKitPluginResourcesNoData($I);

		// Create a Divi Page in the frontend editor.
		$I->createDiviPageInFrontendEditor($I, 'Kit: Page: Product: Divi: Product: No Products');

		// Insert the Product module.
		$I->insertDiviRowWithModule(
			$I,
			'Kit Product',
			'convertkit_product'
		);

		// Confirm the on screen message displays.
		$I->seeInSource('No products exist in Kit');
		$I->seeInSource('Add a product to your ConvertKit account, and then refresh this page to select a product.');
	}

	/**
	 * Test the Product module works when no Product is selected.
	 *
	 * @since   2.5.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testProductModuleWithNoProductParameter(AcceptanceTester $I)
	{
		// Setup Plugin, without defining default Forms.
		$I->setupConvertKitPluginNoDefaultForms($I);
		$I->setupConvertKitPluginResources($I);

		// Create Page with Product module in Divi.
		$pageID = $I->createPageWithDiviModuleProgrammatically(
			$I,
			'Kit: Product: Divi Module: No Product Param',
			'convertkit_product',
			'product',
			''
		);

		// Load Page.
		$I->amOnPage('?p=' . $pageID);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that no ConvertKit Product is displayed.
		$I->dontSeeProductOutput($I);
	}


	/**
	 * Deactivate and reset Plugin(s) after each test, if the test passes.
	 * We don't use _after, as this would provide a screenshot of the Plugin
	 * deactivation and not the true test error.
	 *
	 * @since   2.5.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _passed(AcceptanceTester $I)
	{
		$I->deactivateThirdPartyPlugin($I, 'divi-builder');
		$I->deactivateConvertKitPlugin($I);
		$I->resetConvertKitPlugin($I);
	}
}
