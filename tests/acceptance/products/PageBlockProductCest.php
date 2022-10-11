<?php
/**
 * Tests for the ConvertKit Product Gutenberg Block.
 * 
 * @since 	1.9.8.5
 */
class PageBlockProductCest
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
	 * Test the Product block works when using a valid Product parameter.
	 * 
	 * @since 	1.9.8.5
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testProductBlockWithValidProductParameter(AcceptanceTester $I)
	{
		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Product: Valid Product Param');

		// Configure metabox's Form setting = None, ensuring we only test the block in Gutenberg.
		$I->configureMetaboxSettings($I, 'wp-convertkit-meta-box', [
			'form' => [ 'select2', 'None' ],
		]);

		// Add block to Page, setting the Product setting to the value specified in the .env file.
		$I->addGutenbergBlock($I, 'ConvertKit Product', 'convertkit-product', [
			'product' => [ 'select', $_ENV['CONVERTKIT_API_PRODUCT_NAME'] ],
		]);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that the block displays.
		$I->seeProductOutput($I, $_ENV['CONVERTKIT_API_PRODUCT_URL'], 'Buy my product');
	}

	/**
	 * Test the Product block works when not defining a Product parameter.
	 * 
	 * @since 	1.9.8.5
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testProductBlockWithNoProductParameter(AcceptanceTester $I)
	{
		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Product: No Product Param');

		// Configure metabox's Form setting = None, ensuring we only test the block in Gutenberg.
		$I->configureMetaboxSettings($I, 'wp-convertkit-meta-box', [
			'form' => [ 'select2', 'None' ],
		]);

		// Add block to Page.
		$I->addGutenbergBlock($I, 'ConvertKit Product', 'convertkit-product');

		// Confirm that the Product block displays instructions to the user on how to select a Product.
		$I->see('Select a Product using the Product option in the Gutenberg sidebar.', [
			'css' => '.convertkit-no-content',
		]);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that no ConvertKit Product button is displayed.
		$I->dontSeeProductOutput($I);
	}

	/**
	 * Test the Product block's text parameter works.
	 * 
	 * @since 	1.9.8.5
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testProductBlockWithTextParameter(AcceptanceTester $I)
	{
		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Product: Text Param');

		// Add block to Page, setting the date format.
		$I->addGutenbergBlock($I, 'ConvertKit Product', 'convertkit-product', [
			'product' 	=> [ 'select', $_ENV['CONVERTKIT_API_PRODUCT_NAME'] ],
			'text' 		=> [ 'text', 'Buy Now' ],
		]);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that the block displays.
		$I->seeProductOutput($I, $_ENV['CONVERTKIT_API_PRODUCT_URL'], 'Buy Now');
	}

	/**
	 * Test the Product block's theme color parameters works.
	 * 
	 * @since 	1.9.8.5
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
			'post_content' 	=> '<!-- wp:convertkit/product {"product":"36377","backgroundColor":"'.$backgroundColor.'","textColor":"'.$textColor.'"} /-->',
		]);

		// Load the Page on the frontend site.
		$I->amOnPage('/convertkit-page-product-block-theme-color-params');

		// Wait for frontend web site to load.
		$I->waitForElementVisible('body.page-template-default');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the block displays.
		$I->seeProductOutput($I, $_ENV['CONVERTKIT_API_PRODUCT_URL']);

		// Confirm that the chosen colors are applied as CSS styles.
		$I->seeInSource('class="wp-block-button__link convertkit-product has-text-color has-'.$textColor.'-color has-background has-'.$backgroundColor.'-background-color');
	}

	/**
	 * Test the Product block's hex color parameters works.
	 * 
	 * @since 	1.9.8.5
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testProductBlockWithHexColorParameters(AcceptanceTester $I)
	{
		// Define colors.
		$backgroundColor = '#ee1616';
		$textColor = '#1212c0';

		// It's tricky to interact with Gutenberg's color picker, so we programmatically create the Page
		// instead to then confirm the color settings apply on the output.
		// We don't need to test the color picker itself, as it's a Gutenberg supplied component, and our
		// other Acceptance tests confirm that the block can be added in Gutenberg etc.
		$I->havePageInDatabase([
			'post_name' 	=> 'convertkit-page-product-block-hex-color-params',
			'post_content' 	=> '<!-- wp:convertkit/product {"product":"36377","style":{"color":{"text":"'.$textColor.'","background":"'.$backgroundColor.'"}}} /-->',
		]);

		// Load the Page on the frontend site.
		$I->amOnPage('/convertkit-page-product-block-hex-color-params');

		// Wait for frontend web site to load.
		$I->waitForElementVisible('body.page-template-default');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the block displays.
		$I->seeProductOutput($I, $_ENV['CONVERTKIT_API_PRODUCT_URL'], 'Buy my product');

		// Confirm that the chosen colors are applied as CSS styles.
		$I->seeInSource('<a href="'.$_ENV['CONVERTKIT_API_PRODUCT_URL'].'" class="wp-block-button__link convertkit-product has-text-color has-background" style="color:'.$textColor.';background-color:'.$backgroundColor.'"');
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