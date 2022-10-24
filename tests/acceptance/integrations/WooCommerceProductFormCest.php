<?php
/**
 * Tests for ConvertKit Forms on WooCommerce Products.
 *
 * @since   1.9.6
 */
class WooCommerceProductFormCest
{
	/**
	 * Run common actions before running the test functions in this class.
	 *
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I  Tester.
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
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I  Tester.
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
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I  Tester.
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
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I  Tester.
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
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I  Tester.
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
	 * Test the [convertkit_form] shortcode is inserted into the applicable Content or Excerpt Visual Editor
	 * instances when adding a WooCommerce Product.
	 *
	 * @since   1.9.8.3
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testAddNewProductUsingFormShortcodeInVisualEditor(AcceptanceTester $I)
	{
		// Add a Product using the Classic Editor.
		$I->addClassicEditorPage($I, 'product', 'ConvertKit: Product: Form: Shortcode: Visual Editor');

		// Configure metabox's Form setting = None, ensuring we only test the shortcode in the Classic Editor.
		$I->fillSelect2Field($I, '#select2-wp-convertkit-form-container', 'None', 'aria-owns');

		// Add shortcode to Excerpt (Product short description), setting the Form setting to the value specified in the .env file,
		// and confirming that the expected shortcode is displayed in the Excerpt field.
		$I->addVisualEditorShortcode(
			$I,
			'ConvertKit Form',
			'convertkit-form',
			[
				'form' => [ 'select', $_ENV['CONVERTKIT_API_FORM_NAME'] ],
			],
			'[convertkit_form form="' . $_ENV['CONVERTKIT_API_FORM_ID'] . '"]',
			'excerpt' // The ID of the Product short description field.
		);

		// Add shortcode to Content, setting the Form setting to the value specified in the .env file,
		// and confirming that the expected shortcode is displayed in the Excerpt field.
		$I->addVisualEditorShortcode(
			$I,
			'ConvertKit Form',
			'convertkit-form',
			[
				'form' => [ 'select', $_ENV['CONVERTKIT_API_FORM_NAME'] ],
			],
			'[convertkit_form form="' . $_ENV['CONVERTKIT_API_FORM_ID'] . '"]',
			'content' // The ID of the Content field.
		);

		// Publish and view the Product on the frontend site.
		$I->publishAndViewClassicEditorPage($I);

		// Confirm that the ConvertKit Form is displayed.
		$I->seeElementInDOM('form[data-sv-form]');
	}

	/**
	 * Test the [convertkit_form] shortcode is inserted into the applicable Content or Excerpt Text Editor
	 * instances when adding a WooCommerce Product.
	 *
	 * @since   1.9.8.3
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testAddNewProductUsingFormShortcodeInTextEditor(AcceptanceTester $I)
	{
		// Add a Product using the Classic Editor.
		$I->addClassicEditorPage($I, 'product', 'ConvertKit: Product: Form: Shortcode: Text Editor');

		// Configure metabox's Form setting = None, ensuring we only test the shortcode in the Classic Editor.
		$I->fillSelect2Field($I, '#select2-wp-convertkit-form-container', 'None', 'aria-owns');

		// Add shortcode to Excerpt (Product short description), setting the Form setting to the value specified in the .env file,
		// and confirming that the expected shortcode is displayed in the Excerpt field.
		$I->addTextEditorShortcode(
			$I,
			'ConvertKit Form',
			'convertkit-form',
			[
				'form' => [ 'select', $_ENV['CONVERTKIT_API_FORM_NAME'] ],
			],
			'[convertkit_form form="' . $_ENV['CONVERTKIT_API_FORM_ID'] . '"]',
			'excerpt' // The ID of the Product short description field.
		);

		// Add shortcode to Content, setting the Form setting to the value specified in the .env file,
		// and confirming that the expected shortcode is displayed in the Excerpt field.
		$I->addTextEditorShortcode(
			$I,
			'ConvertKit Form',
			'convertkit-form',
			[
				'form' => [ 'select', $_ENV['CONVERTKIT_API_FORM_NAME'] ],
			],
			'[convertkit_form form="' . $_ENV['CONVERTKIT_API_FORM_ID'] . '"]',
			'content' // The ID of the Content field.
		);

		// Publish and view the Product on the frontend site.
		$I->publishAndViewClassicEditorPage($I);

		// Confirm that the ConvertKit Form is displayed.
		$I->seeElementInDOM('form[data-sv-form]');
	}

	/**
	 * Test that the Default Form for Products displays when the Default option is chosen via
	 * WordPress' Quick Edit functionality.
	 *
	 * @since   1.9.8.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testQuickEditUsingDefaultForm(AcceptanceTester $I)
	{
		// Specify the Default Form in the Plugin Settings.
		$I->setupConvertKitPluginDefaultFormForWooCommerceProducts($I);

		// Programmatically create a Product.
		$productID = $I->havePostInDatabase(
			[
				'post_type'  => 'product',
				'post_title' => 'ConvertKit: Product: Form: Default: Quick Edit',
			]
		);

		// Quick Edit the Product in the Products WP_List_Table.
		$I->quickEdit(
			$I,
			'product',
			$productID,
			[
				'form' => [ 'select', 'Default' ],
			]
		);

		// Load the Product on the frontend site.
		$I->amOnPage('/?p=' . $productID);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the ConvertKit Default Form displays.
		$I->seeElementInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_ID'] . '"]');
	}

	/**
	 * Test that the defined form displays when chosen via
	 * WordPress' Quick Edit functionality.
	 *
	 * @since   1.9.8.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testQuickEditUsingDefinedForm(AcceptanceTester $I)
	{
		// Programmatically create a Product.
		$productID = $I->havePostInDatabase(
			[
				'post_type'  => 'product',
				'post_title' => 'ConvertKit: Product: Form: ' . $_ENV['CONVERTKIT_API_FORM_NAME'] . ': Quick Edit',
			]
		);

		// Quick Edit the Product in the Products WP_List_Table.
		$I->quickEdit(
			$I,
			'product',
			$productID,
			[
				'form' => [ 'select', $_ENV['CONVERTKIT_API_FORM_NAME'] ],
			]
		);

		// Load the Product on the frontend site.
		$I->amOnPage('/?p=' . $productID);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the ConvertKit Default Form displays.
		$I->seeElementInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_ID'] . '"]');
	}

	/**
	 * Test that the Default Form for Products displays when the Default option is chosen via
	 * WordPress' Bulk Edit functionality.
	 *
	 * @since   1.9.8.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testBulkEditUsingDefaultForm(AcceptanceTester $I)
	{
		// Specify the Default Form in the Plugin Settings.
		$I->setupConvertKitPluginDefaultFormForWooCommerceProducts($I);

		// Programmatically create two Products.
		$productIDs = array(
			$I->havePostInDatabase(
				[
					'post_type'  => 'product',
					'post_title' => 'ConvertKit: Product: Form: Default: Bulk Edit #1',
				]
			),
			$I->havePostInDatabase(
				[
					'post_type'  => 'product',
					'post_title' => 'ConvertKit: Product: Form: Default: Bulk Edit #2',
				]
			),
		);

		// Bulk Edit the Products in the Products WP_List_Table.
		$I->bulkEdit(
			$I,
			'product',
			$productIDs,
			[
				'form' => [ 'select', 'Default' ],
			]
		);

		// Iterate through Products to run frontend tests.
		foreach ($productIDs as $productID) {
			// Load Product on the frontend site.
			$I->amOnPage('/?p=' . $productID);

			// Check that no PHP warnings or notices were output.
			$I->checkNoWarningsAndNoticesOnScreen($I);

			// Confirm that the ConvertKit Default Form displays.
			$I->seeElementInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_ID'] . '"]');
		}
	}

	/**
	 * Test that the defined form displays when chosen via
	 * WordPress' Bulk Edit functionality.
	 *
	 * @since   1.9.8.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testBulkEditUsingDefinedForm(AcceptanceTester $I)
	{
		// Programmatically create two Products.
		$productIDs = array(
			$I->havePostInDatabase(
				[
					'post_type'  => 'product',
					'post_title' => 'ConvertKit: Product: Form: ' . $_ENV['CONVERTKIT_API_FORM_NAME'] . ': Bulk Edit #1',
				]
			),
			$I->havePostInDatabase(
				[
					'post_type'  => 'product',
					'post_title' => 'ConvertKit: Product: Form: ' . $_ENV['CONVERTKIT_API_FORM_NAME'] . ': Bulk Edit #2',
				]
			),
		);

		// Bulk Edit the Products in the Products WP_List_Table.
		$I->bulkEdit(
			$I,
			'product',
			$productIDs,
			[
				'form' => [ 'select', $_ENV['CONVERTKIT_API_FORM_NAME'] ],
			]
		);

		// Iterate through Products to run frontend tests.
		foreach ($productIDs as $productID) {
			// Load Product on the frontend site.
			$I->amOnPage('/?p=' . $productID);

			// Check that no PHP warnings or notices were output.
			$I->checkNoWarningsAndNoticesOnScreen($I);

			// Confirm that the ConvertKit Default Form displays.
			$I->seeElementInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_ID'] . '"]');
		}
	}

	/**
	 * Test that the existing settings are honored and not changed
	 * when the Bulk Edit options are set to 'No Change'.
	 *
	 * @since   1.9.8.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testBulkEditWithNoChanges(AcceptanceTester $I)
	{
		// Programmatically create two Products with a defined form.
		$productIDs = array(
			$I->havePostInDatabase(
				[
					'post_type'  => 'product',
					'post_title' => 'ConvertKit: Product: Form: ' . $_ENV['CONVERTKIT_API_FORM_NAME'] . ': Bulk Edit with No Change #1',
					'meta_input' => [
						'_wp_convertkit_post_meta' => [
							'form'         => $_ENV['CONVERTKIT_API_FORM_ID'],
							'landing_page' => '',
							'tag'          => '',
						],
					],
				]
			),
			$I->havePostInDatabase(
				[
					'post_type'  => 'product',
					'post_title' => 'ConvertKit: Product: Form: ' . $_ENV['CONVERTKIT_API_FORM_NAME'] . ': Bulk Edit with No Change #2',
					'meta_input' => [
						'_wp_convertkit_post_meta' => [
							'form'         => $_ENV['CONVERTKIT_API_FORM_ID'],
							'landing_page' => '',
							'tag'          => '',
						],
					],
				]
			),
		);

		// Bulk Edit the Products in the Products WP_List_Table.
		$I->bulkEdit(
			$I,
			'product',
			$productIDs,
			[
				'form' => [ 'select', '— No Change —' ],
			]
		);

		// Iterate through Products to run frontend tests.
		foreach ($productIDs as $productID) {
			// Load Page on the frontend site.
			$I->amOnPage('/?p=' . $productID);

			// Check that no PHP warnings or notices were output.
			$I->checkNoWarningsAndNoticesOnScreen($I);

			// Confirm that the ConvertKit Form displays.
			$I->seeElementInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_ID'] . '"]');
		}
	}

	/**
	 * Test that the Bulk Edit fields do not display when a search on a WP_List_Table
	 * returns no results.
	 *
	 * @since   1.9.8.1
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testBulkEditFieldsHiddenWhenNoProductsFound(AcceptanceTester $I)
	{
		// Emulate the user searching for Products with a query string that yields no results.
		$I->amOnAdminPage('edit.php?post_type=product&s=nothing');

		// Confirm that the Bulk Edit fields do not display.
		$I->dontSeeElement('#convertkit-bulk-edit');
	}

	/**
	 * Deactivate and reset Plugin(s) after each test, if the test passes.
	 * We don't use _after, as this would provide a screenshot of the Plugin
	 * deactivation and not the true test error.
	 *
	 * @since   1.9.6.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _passed(AcceptanceTester $I)
	{
		$I->deactivateConvertKitPlugin($I);
		$I->deactivateThirdPartyPlugin($I, 'classic-editor');
		$I->deactivateThirdPartyPlugin($I, 'woocommerce');
		$I->resetConvertKitPlugin($I);
	}
}
