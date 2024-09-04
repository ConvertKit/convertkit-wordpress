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

		// Activate Classic Editor Plugin.
		$I->activateThirdPartyPlugin($I, 'classic-editor');

		// Add a Page using the Classic Editor.
		$I->addClassicEditorPage($I, 'page', 'ConvertKit: Page: Form: Divi: Backend Editor');

		// Configure metabox's Form setting = None, ensuring we only test the block in Gutenberg.
		$I->configureMetaboxSettings(
			$I,
			'wp-convertkit-meta-box',
			[
				'form' => [ 'select2', 'None' ],
			]
		);

		// Scroll to Publish meta box, so its buttons are not hidden.
		$I->scrollTo('#submitdiv');

		// Wait for the Publish button to change its state from disabled (WordPress disables it for a moment when auto-saving).
		$I->waitForElementVisible('input#publish:not(:disabled)');

		// Click the Publish button twice, because Divi is flaky at best.
		$I->click('input#publish');
		$I->wait(2);
		$I->click('input#publish');

		// Wait for notice to display.
		$I->waitForElementVisible('.notice-success');

		// Remove transient set by Divi that would show the welcome modal.
		$I->dontHaveTransientInDatabase('et_builder_show_bfb_welcome_modal');

		// Click Divi Builder button.
		$I->click('#et_pb_toggle_builder');

		// Dismiss modal.
		$I->waitForElementVisible('.et-core-modal-action-dont-restore');
		$I->click('.et-core-modal-action-dont-restore');

		// Click Build from scratch button.
		$I->waitForElementVisible('.et-fb-page-creation-card-build_from_scratch');
		$I->click('Start Building', '.et-fb-page-creation-card-build_from_scratch');

		// Insert row.
		$I->waitForElementVisible('li[data-layout="4_4"]');
		$I->click('li[data-layout="4_4"]');

		// Search for module.
		$I->waitForElementVisible('input[name="filterByTitle"]');
		$I->fillField('filterByTitle', 'ConvertKit Product');

		// Insert module.
		$I->waitForElementVisible('li.convertkit_product');
		$I->click('li.convertkit_product');

		// Select Product.
		$I->waitForElementVisible('#et-fb-product');
		$I->click('#et-fb-product');
		$I->click('li[data-value="' . $_ENV['CONVERTKIT_API_PRODUCT_ID'] . '"]', '#et-fb-product');

		// Save module.
		$I->click('button[data-tip="Save Changes"]');

		// Update page.
		$I->click('Update');

		// Load the Page on the frontend site.
		$I->waitForElementVisible('.notice-success');
		$I->click('.notice-success a');

		// Wait for frontend web site to load.
		$I->waitForElementVisible('body');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

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

		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Divi: Frontend');

		// Configure metabox's Form setting = None, ensuring we only test the block in Gutenberg.
		$I->configureMetaboxSettings(
			$I,
			'wp-convertkit-meta-box',
			[
				'form' => [ 'select2', 'None' ],
			]
		);

		// Publish Page.
		$url = $I->publishGutenbergPage($I);

		// Click Divi Builder button.
		$I->click('Use Divi Builder');

		// Reload page to dismiss modal.
		$I->wait(5);
		$I->amOnUrl($url . '?et_fb=1&PageSpeed=off');

		// Click Build from scratch button.
		$I->waitForElementVisible('.et-fb-page-creation-card-build_from_scratch', 30);
		$I->click('Start Building', '.et-fb-page-creation-card-build_from_scratch');

		// Insert row.
		$I->waitForElementVisible('li[data-layout="4_4"]');
		$I->click('li[data-layout="4_4"]');

		// Search for module.
		$I->waitForElementVisible('input[name="filterByTitle"]');
		$I->fillField('filterByTitle', 'ConvertKit Product');

		// Insert module.
		$I->waitForElementVisible('li.convertkit_product');
		$I->click('li.convertkit_product');

		// Select Product.
		$I->waitForElementVisible('#et-fb-product');
		$I->click('#et-fb-product');
		$I->click('li[data-value="' . $_ENV['CONVERTKIT_API_PRODUCT_ID'] . '"]', '#et-fb-product');

		// Save module.
		$I->click('button[data-tip="Save Changes"]');

		// Save page.
		$I->click('.et-fb-page-settings-bar__toggle-button');
		$I->waitForElementVisible('button.et-fb-button--publish');
		$I->click('button.et-fb-button--publish');
		$I->wait(3);

		// Load page without Divi frontend builder.
		$I->amOnUrl($url);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

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
		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Divi: Product: Frontend: No Credentials');

		// Publish Page.
		$url = $I->publishGutenbergPage($I);

		// Click Divi Builder button.
		$I->click('Use Divi Builder');

		// Reload page to dismiss modal.
		$I->wait(5);
		$I->amOnUrl($url . '?et_fb=1&PageSpeed=off');

		// Click Build from scratch button.
		$I->waitForElementVisible('.et-fb-page-creation-card-build_from_scratch', 30);
		$I->click('Start Building', '.et-fb-page-creation-card-build_from_scratch');

		// Insert row.
		$I->waitForElementVisible('li[data-layout="4_4"]');
		$I->click('li[data-layout="4_4"]');

		// Search for module.
		$I->waitForElementVisible('input[name="filterByTitle"]');
		$I->fillField('filterByTitle', 'ConvertKit Product');

		// Insert module.
		$I->waitForElementVisible('li.convertkit_product');
		$I->click('li.convertkit_product');

		// Confirm the on screen message displays.
		$I->seeInSource('Not connected to ConvertKit');
		$I->seeInSource('Connect your ConvertKit account at Settings > ConvertKit, and then refresh this page to select a product.');
	}

	/**
	 * Test the Product module displays the expected message when the ConvertKit account
	 * has no products.
	 *
	 * @since   2.5.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testProductModuleInFrontendEditorWhenNoProduct(AcceptanceTester $I)
	{
		// Setup Plugin.
		$I->setupConvertKitPluginCredentialsNoData($I);
		$I->setupConvertKitPluginResourcesNoData($I);

		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Divi: Frontend: No Products');

		// Publish Page.
		$url = $I->publishGutenbergPage($I);

		// Click Divi Builder button.
		$I->click('Use Divi Builder');

		// Reload page to dismiss modal.
		$I->wait(5);
		$I->amOnUrl($url . '?et_fb=1&PageSpeed=off');

		// Click Build from scratch button.
		$I->waitForElementVisible('.et-fb-page-creation-card-build_from_scratch', 30);
		$I->click('Start Building', '.et-fb-page-creation-card-build_from_scratch');

		// Insert row.
		$I->waitForElementVisible('li[data-layout="4_4"]');
		$I->click('li[data-layout="4_4"]');

		// Search for module.
		$I->waitForElementVisible('input[name="filterByTitle"]');
		$I->fillField('filterByTitle', 'ConvertKit Product');

		// Insert module.
		$I->waitForElementVisible('li.convertkit_product');
		$I->click('li.convertkit_product');

		// Confirm the on screen message displays.
		$I->seeInSource('No products exist in ConvertKit');
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
		$pageID = $this->_createPageWithProductModule($I, 'ConvertKit: Page: Product: Divi Module: No Product Param', '');

		// Load Page.
		$I->amOnPage('?p=' . $pageID);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that no ConvertKit Product is displayed.
		$I->dontSeeProductOutput();
	}

	/**
	 * Create a Page in the database comprising of Divi Page Builder data
	 * containing a ConvertKit Product module.
	 *
	 * @since   2.5.7
	 *
	 * @param   AcceptanceTester $I      Tester.
	 * @param   string           $title  Page Title.
	 * @param   int              $productID ConvertKit Product ID.
	 * @return  int                         Page ID
	 */
	private function _createPageWithProductModule(AcceptanceTester $I, $title, $productID)
	{
		return $I->havePostInDatabase(
			[
				'post_title'   => $title,
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_content' => '[et_pb_section fb_built="1" _builder_version="4.27.0" _module_preset="default" global_colors_info="{}"]
					[et_pb_row _builder_version="4.27.0" _module_preset="default"]
						[et_pb_column _builder_version="4.27.0" _module_preset="default" type="4_4"]
							[convertkit_product _builder_version="4.27.0" _module_preset="default" product="' . $productID . '" hover_enabled="0" sticky_enabled="0"][/convertkit_product]
						[/et_pb_column]
					[/et_pb_row]
				[/et_pb_section]',
				'meta_input'   => [
					// Enable Divi Builder.
					'_et_pb_use_builder'         => 'on',
					'_et_pb_built_for_post_type' => 'page',

					// Configure ConvertKit Plugin to not display a default Form,
					// as we are testing for the Form in Elementor.
					'_wp_convertkit_post_meta'   => [
						'form'         => '0',
						'landing_page' => '',
						'tag'          => '',
					],
				],
			]
		);
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
