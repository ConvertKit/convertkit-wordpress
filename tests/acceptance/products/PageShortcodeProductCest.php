<?php
/**
 * Tests for the ConvertKit Product shortcode.
 *
 * @since   1.9.8.5
 */
class PageShortcodeProductCest
{
	/**
	 * Run common actions before running the test functions in this class.
	 *
	 * @since   1.9.8.5
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _before(AcceptanceTester $I)
	{
		$I->activateConvertKitPlugin($I);
	}

	/**
	 * Test the [convertkit_product] shortcode works when a valid Product ID is specified,
	 * using the Classic Editor (TinyMCE / Visual).
	 *
	 * @since   1.9.7.5
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testProductShortcodeInVisualEditorWithValidProductParameter(AcceptanceTester $I)
	{
		// Setup ConvertKit Plugin with no default form specified.
		$I->setupConvertKitPluginNoDefaultForms($I);
		$I->setupConvertKitPluginResources($I);

		// Add a Page using the Classic Editor.
		$I->addClassicEditorPage($I, 'page', 'ConvertKit: Page: Product: Shortcode: Visual Editor');

		// Add shortcode to Page, setting the Product setting to the value specified in the .env file.
		$I->addVisualEditorShortcode(
			$I,
			'ConvertKit Product',
			[
				'product' => [ 'select', $_ENV['CONVERTKIT_API_PRODUCT_NAME'] ],
			],
			'[convertkit_product product="' . $_ENV['CONVERTKIT_API_PRODUCT_ID'] . '" text="Buy my product" disable_modal_on_mobile="0"]'
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewClassicEditorPage($I);

		// Confirm that the ConvertKit Product is displayed.
		$I->seeProductOutput($I, $_ENV['CONVERTKIT_API_PRODUCT_URL'], 'Buy my product');
	}

	/**
	 * Test the [convertkit_product] shortcode works when a valid Product ID is specified,
	 * using the Text Editor.
	 *
	 * @since   1.9.7.5
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testProductShortcodeInTextEditorWithValidProductParameter(AcceptanceTester $I)
	{
		// Setup ConvertKit Plugin with no default form specified.
		$I->setupConvertKitPluginNoDefaultForms($I);
		$I->setupConvertKitPluginResources($I);

		// Add a Page using the Classic Editor.
		$I->addClassicEditorPage($I, 'page', 'ConvertKit: Page: Product: Shortcode: Text Editor');

		// Add shortcode to Page, setting the Product setting to the value specified in the .env file.
		$I->addTextEditorShortcode(
			$I,
			'convertkit-product',
			[
				'product' => [ 'select', $_ENV['CONVERTKIT_API_PRODUCT_NAME'] ],
			],
			'[convertkit_product product="' . $_ENV['CONVERTKIT_API_PRODUCT_ID'] . '" text="Buy my product" disable_modal_on_mobile="0"]'
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewClassicEditorPage($I);

		// Confirm that the ConvertKit Product is displayed.
		$I->seeProductOutput($I, $_ENV['CONVERTKIT_API_PRODUCT_URL'], 'Buy my product');
	}

	/**
	 * Test the [convertkit_product] shortcode does not output errors when an invalid Product ID is specified.
	 *
	 * @since   1.9.8.5
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testProductShortcodeWithInvalidProductParameter(AcceptanceTester $I)
	{
		// Setup ConvertKit Plugin with no default form specified.
		$I->setupConvertKitPluginNoDefaultForms($I);
		$I->setupConvertKitPluginResources($I);

		// Create Page with Shortcode.
		$I->havePageInDatabase(
			[
				'post_name'    => 'convertkit-product-shortcode-invalid-product-param',
				'post_content' => '[convertkit_product=1]',
			]
		);

		// Load the Page on the frontend site.
		$I->amOnPage('/convertkit-product-shortcode-invalid-product-param');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that no ConvertKit Product button is displayed.
		$I->dontSeeProductOutput($I);
	}

	/**
	 * Test the Product shortcode's text parameter works.
	 *
	 * @since   2.0.3
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testProductShortcodeInVisualEditorWithTextParameter(AcceptanceTester $I)
	{
		// Setup ConvertKit Plugin with no default form specified.
		$I->setupConvertKitPluginNoDefaultForms($I);
		$I->setupConvertKitPluginResources($I);

		// Add a Page using the Classic Editor.
		$I->addClassicEditorPage($I, 'page', 'ConvertKit: Page: Product: Shortcode: Text Param');

		// Add shortcode to Page, setting the Product setting to the value specified in the .env file.
		$I->addVisualEditorShortcode(
			$I,
			'ConvertKit Product',
			[
				'product' => [ 'select', $_ENV['CONVERTKIT_API_PRODUCT_NAME'] ],
				'text'    => [ 'input', 'Buy now' ],
			],
			'[convertkit_product product="' . $_ENV['CONVERTKIT_API_PRODUCT_ID'] . '" text="Buy now"  disable_modal_on_mobile="0"]'
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewClassicEditorPage($I);

		// Confirm that the ConvertKit Product is displayed.
		$I->seeProductOutput($I, $_ENV['CONVERTKIT_API_PRODUCT_URL'], 'Buy now');
	}

	/**
	 * Test the Product shortcode's default text value is output when the text parameter is blank.
	 *
	 * @since   2.0.3
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testProductShortcodeInVisualEditorWithBlankTextParameter(AcceptanceTester $I)
	{
		// Setup ConvertKit Plugin with no default form specified.
		$I->setupConvertKitPluginNoDefaultForms($I);
		$I->setupConvertKitPluginResources($I);

		// Add a Page using the Classic Editor.
		$I->addClassicEditorPage($I, 'page', 'ConvertKit: Page: Product: Shortcode: Blank Text Param');

		// Add shortcode to Page, setting the Product setting to the value specified in the .env file.
		$I->addVisualEditorShortcode(
			$I,
			'ConvertKit Product',
			[
				'product' => [ 'select', $_ENV['CONVERTKIT_API_PRODUCT_NAME'] ],
				'text'    => [ 'input', '' ],
			],
			'[convertkit_product product="' . $_ENV['CONVERTKIT_API_PRODUCT_ID'] . '"  disable_modal_on_mobile="0"]'
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewClassicEditorPage($I);

		// Confirm that the ConvertKit Product is displayed.
		$I->seeProductOutput($I, $_ENV['CONVERTKIT_API_PRODUCT_URL'], 'Buy my product');
	}

	/**
	 * Test the Product shortcode works when using a valid discount code.
	 *
	 * @since   2.4.1
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testProductShortcodeInVisualEditorWithValidDiscountCodeParameter(AcceptanceTester $I)
	{
		// Setup ConvertKit Plugin with no default form specified.
		$I->setupConvertKitPluginNoDefaultForms($I);
		$I->setupConvertKitPluginResources($I);

		// Add a Page using the Classic Editor.
		$I->addClassicEditorPage($I, 'page', 'ConvertKit: Page: Product: Shortcode: Valid Discount Code Param');

		// Add shortcode to Page, setting the Product setting to the value specified in the .env file.
		$I->addVisualEditorShortcode(
			$I,
			'ConvertKit Product',
			[
				'product'       => [ 'select', $_ENV['CONVERTKIT_API_PRODUCT_NAME'] ],
				'discount_code' => [ 'input', $_ENV['CONVERTKIT_API_PRODUCT_DISCOUNT_CODE'] ],
			],
			'[convertkit_product product="' . $_ENV['CONVERTKIT_API_PRODUCT_ID'] . '" text="Buy my product" discount_code="' . $_ENV['CONVERTKIT_API_PRODUCT_DISCOUNT_CODE'] . '"  disable_modal_on_mobile="0"]'
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewClassicEditorPage($I);

		// Confirm that the ConvertKit Product is displayed.
		$I->seeProductOutput($I, $_ENV['CONVERTKIT_API_PRODUCT_URL'], 'Buy my product');

		// Confirm the discount code has been applied.
		$I->switchToIFrame('iframe[data-active]');
		$I->waitForElementVisible('.formkit-main');
		$I->see('$0.00');
	}

	/**
	 * Test the Product shortcode works when using an invalid discount code.
	 *
	 * @since   2.4.1
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testProductShortcodeInVisualEditorWithInvalidDiscountCodeParameter(AcceptanceTester $I)
	{
		// Setup ConvertKit Plugin with no default form specified.
		$I->setupConvertKitPluginNoDefaultForms($I);
		$I->setupConvertKitPluginResources($I);

		// Add a Page using the Classic Editor.
		$I->addClassicEditorPage($I, 'page', 'ConvertKit: Page: Product: Shortcode: Valid Discount Code Param');

		// Add shortcode to Page, setting the Product setting to the value specified in the .env file.
		$I->addVisualEditorShortcode(
			$I,
			'ConvertKit Product',
			[
				'product'       => [ 'select', $_ENV['CONVERTKIT_API_PRODUCT_NAME'] ],
				'discount_code' => [ 'input', 'fake' ],
			],
			'[convertkit_product product="' . $_ENV['CONVERTKIT_API_PRODUCT_ID'] . '" text="Buy my product" discount_code="fake"  disable_modal_on_mobile="0"]'
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewClassicEditorPage($I);

		// Confirm that the ConvertKit Product is displayed.
		$I->seeProductOutput($I, $_ENV['CONVERTKIT_API_PRODUCT_URL'], 'Buy my product');

		// Confirm the discount code is not valid, but the modal displays so the user can still purchase.
		$I->switchToIFrame('iframe[data-active]');
		$I->waitForElementVisible('.formkit-main');
		$I->see('The coupon is not valid.');
	}

	/**
	 * Test the Product shortcode opens the ConvertKit Product in the same window instead
	 * of a modal when the Disable modal on mobile option is enabled.
	 *
	 * @since   2.4.1
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testProductShortcodeWithDisableModalOnMobileParameterEnabled(AcceptanceTester $I)
	{
		// Setup ConvertKit Plugin with no default form specified.
		$I->setupConvertKitPluginNoDefaultForms($I);
		$I->setupConvertKitPluginResources($I);

		// Add a Page using the Classic Editor.
		$I->addClassicEditorPage($I, 'page', 'ConvertKit: Page: Product: Shortcode: Disable Modal on Mobile');

		// Add shortcode to Page, setting the Product setting to the value specified in the .env file.
		$I->addVisualEditorShortcode(
			$I,
			'ConvertKit Product',
			[
				'product'                 => [ 'select', $_ENV['CONVERTKIT_API_PRODUCT_NAME'] ],
				'disable_modal_on_mobile' => [ 'toggle', 'Yes' ],
			],
			'[convertkit_product product="' . $_ENV['CONVERTKIT_API_PRODUCT_ID'] . '" text="Buy my product" disable_modal_on_mobile="1"]'
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewClassicEditorPage($I);

		// Get Page URL.
		$url = $I->grabFromCurrentUrl();

		// Change user agent to a mobile user agent.
		$I->changeUserAgent($_ENV['TEST_SITE_HTTP_USER_AGENT_MOBILE']);

		// Load page.
		$I->amOnPage($url);

		// Confirm that the shortcode displays without the data-commerce attribute.
		$I->seeElementInDOM('.convertkit-product a');
		$I->dontSeeElementInDOM('.convertkit-product a[data-commerce]');

		// Confirm that clicking the button opens the URL in the same browser tab, and not a modal.
		$I->click('.convertkit-product a');
		$I->waitForElementVisible('body[data-template]');

		// Change user agent back, as it persists through tests.
		$I->changeUserAgent($_ENV['TEST_SITE_HTTP_USER_AGENT']);
	}

	/**
	 * Test the [convertkit_product] shortcode hex colors works when defined.
	 *
	 * @since   1.9.8.5
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testProductShortcodeWithHexColorParameters(AcceptanceTester $I)
	{
		// Setup ConvertKit Plugin with no default form specified.
		$I->setupConvertKitPluginNoDefaultForms($I);
		$I->setupConvertKitPluginResources($I);

		// Define colors.
		$backgroundColor = '#ee1616';
		$textColor       = '#1212c0';

		// It's tricky to interact with WordPress's color picker, so we programmatically create the Page
		// instead to then confirm the color settings apply on the output.
		// We don't need to test the color picker itself, as it's a WordPress supplied component, and our
		// other Acceptance tests confirm that the shortcode can be added in the Classic Editor.
		$I->havePageInDatabase(
			[
				'post_name'    => 'convertkit-page-product-shortcode-hex-color-params',
				'post_content' => '[convertkit_product product="' . $_ENV['CONVERTKIT_API_PRODUCT_ID'] . '" text="Buy my product"  disable_modal_on_mobile="0" background_color="' . $backgroundColor . '" text_color="' . $textColor . '"]',
			]
		);

		// Load the Page on the frontend site.
		$I->amOnPage('/convertkit-page-product-shortcode-hex-color-params');

		// Wait for frontend web site to load.
		$I->waitForElementVisible('body.page-template-default');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the ConvertKit Product is displayed.
		$I->seeProductOutput($I, $_ENV['CONVERTKIT_API_PRODUCT_URL'], 'Buy my product', $textColor, $backgroundColor);
	}

	/**
	 * Test the [convertkit_product] shortcode parameters are correctly escaped on output,
	 * to prevent XSS.
	 *
	 * @since   2.0.5
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testProductShortcodeParameterEscaping(AcceptanceTester $I)
	{
		// Setup ConvertKit Plugin with no default form specified.
		$I->setupConvertKitPluginNoDefaultForms($I);
		$I->setupConvertKitPluginResources($I);

		// Define a 'bad' shortcode.
		$I->havePageInDatabase(
			[
				'post_name'    => 'convertkit-page-product-shortcode-parameter-escaping',
				'post_content' => '[convertkit_product product="' . $_ENV['CONVERTKIT_API_PRODUCT_ID'] . '" text=\'Buy my product\' text_color=\'red" onmouseover="alert(1)"\']',
			]
		);

		// Load the Page on the frontend site.
		$I->amOnPage('/convertkit-page-product-shortcode-parameter-escaping');

		// Wait for frontend web site to load.
		$I->waitForElementVisible('body.page-template-default');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the output is escaped.
		$I->seeInSource('style="color:red&quot; onmouseover=&quot;alert(1)&quot;"');
		$I->dontSeeInSource('style="color:red" onmouseover="alert(1)""');

		// Confirm that the ConvertKit Product is displayed.
		$I->seeProductOutput($I, $_ENV['CONVERTKIT_API_PRODUCT_URL'], 'Buy my product');
	}

	/**
	 * Test the Product shortcode displays a message with a link to the Plugin's
	 * setup wizard, when the Plugin has no API key specified.
	 *
	 * @since   2.2.4
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testProductShortcodeWhenNoAPIKey(AcceptanceTester $I)
	{
		// Add a Page using the Classic Editor.
		$I->addClassicEditorPage($I, 'page', 'ConvertKit: Page: Product: Shortcode: No API Key');

		// Open Visual Editor modal for the shortcode.
		$I->openVisualEditorShortcodeModal(
			$I,
			'ConvertKit Product'
		);

		// Confirm an error notice displays.
		$I->waitForElementVisible('#convertkit-modal-body-body div.notice');

		// Confirm that the modal displays instructions to the user on how to enter their API Key.
		$I->see(
			'No API Key specified.',
			[
				'css' => '#convertkit-modal-body-body',
			]
		);

		// Click the link to confirm it loads the Plugin's settings screen.
		$I->click(
			'Click here to add your API Key.',
			[
				'css' => '#convertkit-modal-body-body',
			]
		);

		// Switch to next browser tab, as the link opens in a new tab.
		$I->switchToNextTab();

		// Confirm the Plugin's setup wizard is displayed.
		$I->seeInCurrentUrl('options.php?page=convertkit-setup');

		// Close tab.
		$I->closeTab();

		// Close modal.
		$I->click('#convertkit-modal-body-head button.mce-close');

		// Save page to avoid alert box when _passed() runs to deactivate the Plugin.
		$I->publishAndViewClassicEditorPage($I);
	}

	/**
	 * Test the Product shortcode displays a message with a link to ConvertKit,
	 * when the ConvertKit account has no forms.
	 *
	 * @since   2.2.3
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testProductShortcodeWhenNoProducts(AcceptanceTester $I)
	{
		// Setup Plugin.
		$I->setupConvertKitPluginAPIKeyNoData($I);
		$I->setupConvertKitPluginResourcesNoData($I);

		// Add a Page using the Classic Editor.
		$I->addClassicEditorPage($I, 'page', 'ConvertKit: Page: Product: Shortcode: No Products');

		// Open Visual Editor modal for the shortcode.
		$I->openVisualEditorShortcodeModal(
			$I,
			'ConvertKit Product'
		);

		// Confirm an error notice displays.
		$I->waitForElementVisible('#convertkit-modal-body-body div.notice');

		// Confirm that the Product shortcode displays instructions to the user on how to add a Form in ConvertKit.
		$I->see(
			'No products exist in ConvertKit.',
			[
				'css' => '#convertkit-modal-body-body',
			]
		);

		// Click the link to confirm it loads ConvertKit.
		$I->click(
			'Click here to create your first product.',
			[
				'css' => '#convertkit-modal-body-body',
			]
		);

		// Switch to next browser tab, as the link opens in a new tab.
		$I->switchToNextTab();

		// Confirm the ConvertKit login screen loaded.
		$I->waitForElementVisible('input[name="user[email]"]');

		// Close tab.
		$I->closeTab();

		// Close modal.
		$I->click('#convertkit-modal-body-head button.mce-close');

		// Save page to avoid alert box when _passed() runs to deactivate the Plugin.
		$I->publishAndViewClassicEditorPage($I);
	}

	/**
	 * Deactivate and reset Plugin(s) after each test, if the test passes.
	 * We don't use _after, as this would provide a screenshot of the Plugin
	 * deactivation and not the true test error.
	 *
	 * @since   1.9.8.5.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _passed(AcceptanceTester $I)
	{
		$I->deactivateThirdPartyPlugin($I, 'classic-editor');
		$I->deactivateConvertKitPlugin($I);
		$I->resetConvertKitPlugin($I);
	}
}
