<?php
/**
 * Tests that the Gutenberg LinkControl and Classic Editor Link button correctly
 * link to ConvertKit Products when selected.
 *
 * @since   2.0.0
 */
class PageLinkProductCest
{
	/**
	 * Run common actions before running the test functions in this class.
	 *
	 * @since   2.0.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _before(AcceptanceTester $I)
	{
		$I->activateConvertKitPlugin($I);
		$I->setupConvertKitPlugin($I);
		$I->setupConvertKitPluginResources($I);
	}

	/**
	 * Test that linking text in a paragraph to a ConvertKit Product works.
	 *
	 * @since   2.0.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testLinkParagraphTextToProduct(AcceptanceTester $I)
	{
		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Product: Link Text');

		// Configure metabox's Form setting = None, ensuring we only test the block in Gutenberg.
		$I->configureMetaboxSettings(
			$I,
			'wp-convertkit-meta-box',
			[
				'form' => [ 'select2', 'None' ],
			]
		);

		// Add paragraph to Page.
		$I->addGutenbergParagraphBlock($I, 'This is some text. ');

		// Add link to end of paragraph.
		$I->addGutenbergLinkToParagraph($I, $_ENV['CONVERTKIT_API_PRODUCT_NAME']);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that the link displays, links to the expected URL and the ConvertKit Product Modal works.
		$I->seeProductLink($I, $_ENV['CONVERTKIT_API_PRODUCT_URL'], $_ENV['CONVERTKIT_API_PRODUCT_NAME']);
	}

	/**
	 * Test that linking text in a button to a ConvertKit Product works.
	 *
	 * @since   2.0.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testLinkButtonTextToProduct(AcceptanceTester $I)
	{
		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Product: Button Link');

		// Configure metabox's Form setting = None, ensuring we only test the block in Gutenberg.
		$I->configureMetaboxSettings(
			$I,
			'wp-convertkit-meta-box',
			[
				'form' => [ 'select2', 'None' ],
			]
		);

		// Add button to Page.
		$I->addGutenbergBlock($I, 'Buttons', 'buttons');

		// Add link to button.
		$I->addGutenbergLinkToButton($I, $_ENV['CONVERTKIT_API_PRODUCT_NAME']);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that the link displays, links to the expected URL and the ConvertKit Product Modal works.
		$I->seeProductLink($I, $_ENV['CONVERTKIT_API_PRODUCT_URL'], $_ENV['CONVERTKIT_API_PRODUCT_NAME']);
	}

	/**
	 * Test that linking text in a paragraph to a ConvertKit Product works
	 * in the Classic Editor.
	 *
	 * @since   2.0.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testLinkParagraphTextToProductInClassicEditor(AcceptanceTester $I)
	{
		// Add a Page using the Classic editor.
		$I->addClassicEditorPage($I, 'page', 'ConvertKit: Page: Product: Classic Editor: Link Text');

		// Add link to Product in Classic Editor.
		$I->addClassicEditorLink($I, $_ENV['CONVERTKIT_API_PRODUCT_NAME']);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewClassicEditorPage($I);

		// Confirm that the link displays, links to the expected URL and the ConvertKit Product Modal works.
		$I->seeProductLink($I, $_ENV['CONVERTKIT_API_PRODUCT_URL'], $_ENV['CONVERTKIT_API_PRODUCT_NAME']);
	}

	/**
	 * Deactivate and reset Plugin(s) after each test, if the test passes.
	 * We don't use _after, as this would provide a screenshot of the Plugin
	 * deactivation and not the true test error.
	 *
	 * @since   2.0.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _passed(AcceptanceTester $I)
	{
		$I->deactivateConvertKitPlugin($I);
		$I->resetConvertKitPlugin($I);
	}
}
