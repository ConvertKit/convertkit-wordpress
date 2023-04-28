<?php
/**
 * Tests for the ConvertKit Product Link Gutenberg Block Formatter.
 *
 * @since   2.2.0
 */
class PageBlockFormatterProductLinkCest
{
	/**
	 * Run common actions before running the test functions in this class.
	 *
	 * @since   2.2.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _before(AcceptanceTester $I)
	{
		$I->activateConvertKitPlugin($I);

		// Setup ConvertKit Plugin with no default form specified.
		$I->setupConvertKitPlugin($I, $_ENV['CONVERTKIT_API_KEY'], $_ENV['CONVERTKIT_API_SECRET'], '', '', '');
		$I->setupConvertKitPluginResources($I);
	}

	/**
	 * Test the Product Link formatter works when selecting a product.
	 *
	 * @since   2.2.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testProductLinkFormatter(AcceptanceTester $I)
	{
		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Product Link Formatter');

		// Configure metabox's Form setting = None, ensuring we only test the block in Gutenberg.
		$I->configureMetaboxSettings(
			$I,
			'wp-convertkit-meta-box',
			[
				'form' => [ 'select2', 'None' ],
			]
		);

		// Add paragraph to Page.
		$I->addGutenbergParagraphBlock($I, $_ENV['CONVERTKIT_API_PRODUCT_NAME']);

		// Select text.
		$I->selectAllText($I, '.wp-block-post-content p[data-empty="false"]');

		// Apply formatter to link the selected text.
		$I->applyGutenbergFormatter(
			$I,
			'ConvertKit Product Trigger',
			'convertkit-product-link',
			[
				// Product.
				'data-id' => [ 'select', $_ENV['CONVERTKIT_API_PRODUCT_NAME'] ],
			]
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that the link displays, links to the expected URL and the ConvertKit Product Modal works.
		$I->seeProductLink($I, $_ENV['CONVERTKIT_API_PRODUCT_URL'], $_ENV['CONVERTKIT_API_PRODUCT_NAME']);
	}

	/**
	 * Test the Product Link formatter is applied and removed when selecting a product, and then
	 * selecting the 'None' option.
	 *
	 * @since   2.2.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testProductLinkFormatterToggleProductSelection(AcceptanceTester $I)
	{
		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Product Link Formatter: Product Toggle');

		// Configure metabox's Form setting = None, ensuring we only test the block in Gutenberg.
		$I->configureMetaboxSettings(
			$I,
			'wp-convertkit-meta-box',
			[
				'form' => [ 'select2', 'None' ],
			]
		);

		// Add paragraph to Page.
		$I->addGutenbergParagraphBlock($I, 'Buy now');

		// Select text.
		$I->selectAllText($I, '.wp-block-post-content p[data-empty="false"]');

		// Apply formatter to link the selected text.
		$I->applyGutenbergFormatter(
			$I,
			'ConvertKit Product Trigger',
			'convertkit-product-link',
			[
				// Product.
				'data-id' => [ 'select', $_ENV['CONVERTKIT_API_PRODUCT_NAME'] ],
			]
		);

		// Apply the formatter again, this time selecting the 'None' option.
		$I->applyGutenbergFormatter(
			$I,
			'ConvertKit Product Trigger',
			'convertkit-product-link',
			[
				// Form.
				'data-id' => [ 'select', 'None' ],
			]
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that the link does not display, as no product was selected.
		$I->dontSeeElementInDOM('a[data-commerce]');
	}

	/**
	 * Test the Product Link formatter works when no product is selected.
	 *
	 * @since   2.2.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testFormLinkFormatterWithNoForm(AcceptanceTester $I)
	{
		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Product Link Formatter: No Product');

		// Configure metabox's Form setting = None, ensuring we only test the block in Gutenberg.
		$I->configureMetaboxSettings(
			$I,
			'wp-convertkit-meta-box',
			[
				'form' => [ 'select2', 'None' ],
			]
		);

		// Add paragraph to Page.
		$I->addGutenbergParagraphBlock($I, 'Buy now');

		// Select text.
		$I->selectAllText($I, '.wp-block-post-content p[data-empty="false"]');

		// Apply formatter to link the selected text.
		$I->applyGutenbergFormatter(
			$I,
			'ConvertKit Product Trigger',
			'convertkit-product-link',
			[
				// Form.
				'data-id' => [ 'select', 'None' ],
			]
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that the link does not display, as no product was selected.
		$I->dontSeeElementInDOM('a[data-commerce]');
	}

	/**
	 * Deactivate and reset Plugin(s) after each test, if the test passes.
	 * We don't use _after, as this would provide a screenshot of the Plugin
	 * deactivation and not the true test error.
	 *
	 * @since   2.2.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _passed(AcceptanceTester $I)
	{
		$I->deactivateConvertKitPlugin($I);
		$I->resetConvertKitPlugin($I);
	}
}
