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
		$I->setupConvertKitPlugin($I);
		$I->enableDebugLog($I);
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
		// Add a Page using the Classic Editor.
		$I->addClassicEditorPage($I, 'page', 'ConvertKit: Page: Product: Shortcode: Visual Editor');

		// Add shortcode to Page, setting the Product setting to the value specified in the .env file.
		$I->addVisualEditorShortcode(
			$I,
			'ConvertKit Product',
			[
				'product' => [ 'select', $_ENV['CONVERTKIT_API_PRODUCT_NAME'] ],
			],
			'[convertkit_product product="' . $_ENV['CONVERTKIT_API_PRODUCT_ID'] . '" text="Buy my product"]'
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
		// Add a Page using the Classic Editor.
		$I->addClassicEditorPage($I, 'page', 'ConvertKit: Page: Product: Shortcode: Text Editor');

		// Add shortcode to Page, setting the Product setting to the value specified in the .env file.
		$I->addTextEditorShortcode(
			$I,
			'convertkit-product',
			[
				'product' => [ 'select', $_ENV['CONVERTKIT_API_PRODUCT_NAME'] ],
			],
			'[convertkit_product product="' . $_ENV['CONVERTKIT_API_PRODUCT_ID'] . '" text="Buy my product"]'
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
		// Add a Page using the Classic Editor.
		$I->addClassicEditorPage($I, 'page', 'ConvertKit: Page: Product: Shortcode: Text Param');

		// Add shortcode to Page, setting the Product setting to the value specified in the .env file.
		$I->addVisualEditorShortcode(
			$I,
			'ConvertKit Product',
			[
				'product' => [ 'select', $_ENV['CONVERTKIT_API_PRODUCT_NAME'] ],
				'text'	  => 'Buy now',
			],
			'[convertkit_product product="' . $_ENV['CONVERTKIT_API_PRODUCT_ID'] . '" text="Buy now"]'
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
		// Add a Page using the Classic Editor.
		$I->addClassicEditorPage($I, 'page', 'ConvertKit: Page: Product: Shortcode: Blank Text Param');

		// Add shortcode to Page, setting the Product setting to the value specified in the .env file.
		$I->addVisualEditorShortcode(
			$I,
			'ConvertKit Product',
			[
				'product' => [ 'select', $_ENV['CONVERTKIT_API_PRODUCT_NAME'] ],
				'text'	  => '',
			],
			'[convertkit_product product="' . $_ENV['CONVERTKIT_API_PRODUCT_ID'] . '" text=""]'
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewClassicEditorPage($I);

		// Confirm that the ConvertKit Product is displayed.
		$I->seeProductOutput($I, $_ENV['CONVERTKIT_API_PRODUCT_URL'], 'Buy my product');
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
				'post_content' => '[convertkit_product product="' . $_ENV['CONVERTKIT_API_PRODUCT_ID'] . '" text="Buy my product" background_color="' . $backgroundColor . '" text_color="' . $textColor . '"]',
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
