<?php
/**
 * Tests for the ConvertKit Product Gutenberg Block.
 * 
 * @since 	1.9.7.8
 */
class PageBlockProductCest
{
	/**
	 * Run common actions before running the test functions in this class.
	 * 
	 * @since 	1.9.7.8
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
	 * Test the Product block works when using the default parameters.
	 * 
	 * @since 	1.9.7.8
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testBroadcastsBlockWithDefaultParameters(AcceptanceTester $I)
	{
		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Product: Default Params');

		// Add block to Page.
		$I->addGutenbergBlock($I, 'ConvertKit Product', 'convertkit-product');

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that the block displays.
		$I->seeProductOutput($I);
	}

	/**
	 * Test the Product block's text parameter works.
	 * 
	 * @since 	1.9.7.8
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testBroadcastsBlockWithTextParameter(AcceptanceTester $I)
	{
		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Product: Text Param');

		// Add block to Page, setting the date format.
		$I->addGutenbergBlock($I, 'ConvertKit Product', 'convertkit-product', [
			'text' => 'Buy Now',
		]);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that the block displays.
		$I->seeProductOutput($I);

		// Confirm that the button text is as expected.
		// @TODO.
		$I->seeInSource('xxx');
	}

	/**
	 * Test the Product block's theme color parameters works.
	 * 
	 * @since 	1.9.7.8
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testProductBlockWithThemeColorParameters(AcceptanceTester $I)
	{
		// Define colors.
		$backgroundColor = 'white';
		$textColor = 'purple';

		// It's tricky to interact with Gutenberg's color picker, so we programmatically create the Page
		// instead to then confirm the color settings apply on the output.
		// We don't need to test the color picker itself, as it's a Gutenberg supplied component, and our
		// other Acceptance tests confirm that the block can be added in Gutenberg etc.
		$I->havePageInDatabase([
			'post_name' 	=> 'convertkit-page-product-block-theme-color-params',
			'post_content' 	=> '<!-- wp:convertkit/product {"backgroundColor":"'.$backgroundColor.'","textColor":"'.$textColor.'"} /-->',
		]);

		// Load the Page on the frontend site.
		$I->amOnPage('/convertkit-page-product-block-theme-color-params');

		// Wait for frontend web site to load.
		$I->waitForElementVisible('body.page-template-default');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the block displays.
		$I->seeProductOutput($I);

		// Confirm that the chosen colors are applied as CSS styles.
		$I->seeInSource('class="wp-block-button__link convertkit-product has-text-color has-'.$textColor.'-color has-background has-'.$backgroundColor.'-background-color');
	}

	/**
	 * Test the Broadcasts block's hex color parameters works.
	 * 
	 * @since 	1.9.7.8
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testBroadcastsBlockWithHexColorParameters(AcceptanceTester $I)
	{
		// Define colors.
		$backgroundColor = '#ee1616';
		$textColor = '#1212c0';

		// It's tricky to interact with Gutenberg's color picker, so we programmatically create the Page
		// instead to then confirm the color settings apply on the output.
		// We don't need to test the color picker itself, as it's a Gutenberg supplied component, and our
		// other Acceptance tests confirm that the block can be added in Gutenberg etc.
		$I->havePageInDatabase([
			'post_name' 	=> 'convertkit-page-broadcasts-block-hex-color-params',
			'post_content' 	=> '<!-- wp:convertkit/broadcasts {"date_format":"m/d/Y","limit":3,"style":{"color":{"text":"'.$textColor.'","background":"'.$backgroundColor.'"}}} /-->',
		]);

		// Load the Page on the frontend site.
		$I->amOnPage('/convertkit-page-broadcasts-block-hex-color-params');

		// Wait for frontend web site to load.
		$I->waitForElementVisible('body.page-template-default');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the block displays.
		$I->seeBroadcastsOutput($I);

		// Confirm that our stylesheet loaded.
		$I->seeInSource('<link rel="stylesheet" id="convertkit-broadcasts-css" href="'.$_ENV['TEST_SITE_WP_URL'].'/wp-content/plugins/convertkit/resources/frontend/css/broadcasts.css');

		// Confirm that the chosen colors are applied as CSS styles.
		$I->seeInSource('<div class="convertkit-broadcasts has-text-color has-background" style="color:'.$textColor.';background-color:'.$backgroundColor.'"');
	}

	/**
	 * Deactivate and reset Plugin(s) after each test, if the test passes.
	 * We don't use _after, as this would provide a screenshot of the Plugin
	 * deactivation and not the true test error.
	 * 
	 * @since 	1.9.7.8
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function _passed(AcceptanceTester $I)
	{
		$I->deactivateConvertKitPlugin($I);
		$I->resetConvertKitPlugin($I);
	}
}