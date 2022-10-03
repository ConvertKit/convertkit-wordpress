<?php
/**
 * Tests that the Gutenberg LinkControl links to ConvertKit Products.
 * 
 * @since 	1.9.8.5
 */
class PageLinkProductCest
{
	/**
	 * Run common actions before running the test functions in this class.
	 * 
	 * @since 	1.9.8.5
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function _before(AcceptanceTester $I)
	{
		$I->activateConvertKitPlugin($I);
		$I->setupConvertKitPlugin($I);
		$I->enableDebugLog($I);
	}

	/**
	 * Test that linking text in a paragraph to a ConvertKit Product works.
	 * 
	 * @since 	1.9.8.5
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testLinkParagraphTextToProduct(AcceptanceTester $I)
	{
		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Product: Link Text');

		// Configure metabox's Form setting = None, ensuring we only test the block in Gutenberg.
		$I->configureMetaboxSettings($I, 'wp-convertkit-meta-box', [
			'form' => [ 'select2', 'None' ],
		]);

		// Add paragraph to Page.
		$I->click('.is-root-container');
		$I->fillField('.is-root-container p', 'This is some text. ');

		// Focus away from paragraph and then back to the paragraph, so that the block toolbar displays.
		$I->click('div.edit-post-visual-editor__post-title-wrapper h1');
		$I->click('.is-root-container p');
		$I->waitForElementVisible('.is-root-container p.is-selected');

		// Click link button in block toolbar.
		$I->waitForElementVisible('.block-editor-block-toolbar button[aria-label="Link"]');
		$I->click('.block-editor-block-toolbar button[aria-label="Link"]');

		// Enter Product name in search field.
		$I->waitForElementVisible('.block-editor-link-control__search-input-wrapper input.block-editor-url-input__input');
		$I->fillField('.block-editor-link-control__search-input-wrapper input.block-editor-url-input__input', 'Newsletter Subscription');
		$I->waitForElementVisible('.block-editor-link-control__search-results-wrapper');
		$I->see('Newsletter Subscription');

		// Click the Product name to create a link to it.
		$I->click('Newsletter Subscription', '.block-editor-link-control__search-results');

		// Confirm that the Product text exists in the paragraph.
		$I->see('Newsletter Subscription', '.is-root-container p.is-selected');

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that the commerce.js script exists.
		$I->seeInSource('commerce.js');

		// Confirm that the link displays.
		$I->seeInSource('<a href="https://cheerful-architect-3237.ck.page/products/newsletter-subscription" data-commerce');
	}

	/**
	 * Test that linking text in a button to a ConvertKit Product works.
	 * 
	 * @since 	1.9.8.5
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testLinkButtonTextToProduct(AcceptanceTester $I)
	{
		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Product: Link Text');

		// Configure metabox's Form setting = None, ensuring we only test the block in Gutenberg.
		$I->configureMetaboxSettings($I, 'wp-convertkit-meta-box', [
			'form' => [ 'select2', 'None' ],
		]);

		// Add button to Page.
		$I->addGutenbergBlock($I, 'Buttons', 'buttons');

		// Add text inside button.
		$I->click('.is-root-container');
		$I->fillField('.is-root-container .wp-block-button__link', 'Buy Now');

		// Link text in Paragraph.
		// @TODO
		$I->seeInSource('sdfsdfsdf');

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that the block displays.
	}

	/**
	 * Deactivate and reset Plugin(s) after each test, if the test passes.
	 * We don't use _after, as this would provide a screenshot of the Plugin
	 * deactivation and not the true test error.
	 * 
	 * @since 	1.9.8.5
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function _passed(AcceptanceTester $I)
	{
		$I->deactivateConvertKitPlugin($I);
		$I->resetConvertKitPlugin($I);
	}
}