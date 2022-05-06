<?php
/**
 * Tests for the ConvertKit Broadcasts Gutenberg Block.
 * 
 * @since 	1.9.7.4
 */
class PageBlockBroadcastsCest
{
	/**
	 * Run common actions before running the test functions in this class.
	 * 
	 * @since 	1.9.7.4
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
	 * Test the Broadcasts block works when using the default parameters.
	 * 
	 * @since 	1.9.7.4
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testBroadcastsBlockWithDefaultParameters(AcceptanceTester $I)
	{
		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Broadcasts: Default Params');

		// Add block to Page, setting its Form setting to the required ConvertKit Form.
		$I->addGutenbergBlock($I, 'ConvertKit Broadcasts', 'convertkit-broadcasts');

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that the block displays.
		$I->seeElementInDOM('ul.convertkit-broadcasts');
		$I->seeElementInDOM('ul.convertkit-broadcasts li.convertkit-broadcast');
		$I->seeElementInDOM('ul.convertkit-broadcasts li.convertkit-broadcast a');

		// Confirm that the default date format is as expected.
		$I->seeInSource('<time datetime="2022-04-08">April 8, 2022</time>');

		// Confirm that the default expected number of Broadcasts are displayed.
		$I->seeNumberOfElements('li.convertkit-broadcast', [1,10]);
	}

	/**
	 * Test the Broadcasts block's date format parameter works.
	 * 
	 * @since 	1.9.7.4
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testBroadcastsBlockWithDateFormatParameter(AcceptanceTester $I)
	{
		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Broadcasts: Date Format Param');

		// Add block to Page, setting its Form setting to the required ConvertKit Form.
		$I->addGutenbergBlock($I, 'ConvertKit Broadcasts', 'convertkit-broadcasts', [
			'date_format' => [ 'select', 'Y-m-d' ],
		]);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that the block displays.
		$this->_seeBroadcastsBlock($I);	

		// Confirm that the date format is as expected.
		$I->seeInSource('<time datetime="2022-04-08">2022-04-08</time>');

		// Confirm that the default expected number of Broadcasts are displayed.
		$I->seeNumberOfElements('li.convertkit-broadcast', [1,10]);
	}

	/**
	 * Test the Broadcasts block's limit parameter works.
	 * 
	 * @since 	1.9.7.4
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testBroadcastsBlockWithLimitParameter(AcceptanceTester $I)
	{
		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Broadcasts: Limit Param');

		// Add block to Page, setting its Form setting to the required ConvertKit Form.
		$I->addGutenbergBlock($I, 'ConvertKit Broadcasts', 'convertkit-broadcasts', [
			'limit' => [ 'input', '2' ],
		]);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that the block displays.
		$this->_seeBroadcastsBlock($I);

		// Confirm that the expected number of Broadcasts are displayed.
		$I->seeNumberOfElements('li.convertkit-broadcast', 2);
	}

	/**
	 * Test the Broadcasts block renders when the limit parameter is blank.
	 * 
	 * @since 	1.9.7.4
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testBroadcastsBlockWithBlankLimitParameter(AcceptanceTester $I)
	{
		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Broadcasts: Blank Limit Param');

		// Add block to Page, setting its Form setting to the required ConvertKit Form.
		$I->addGutenbergBlock($I, 'ConvertKit Broadcasts', 'convertkit-broadcasts');

		// When the sidebar appears, blank the limit parameter as the user might, by pressing the backspace
		// key twice.
		$I->waitForElementVisible('.interface-interface-skeleton__sidebar[aria-label="Editor settings"]');
		$I->pressKey('#convertkit_broadcasts_limit', \Facebook\WebDriver\WebDriverKeys::BACKSPACE );
		$I->pressKey('#convertkit_broadcasts_limit', \Facebook\WebDriver\WebDriverKeys::BACKSPACE );

		// Confirm that the block did not encounter an error and fail to render.
		$I->checkGutenbergBlockHasNoErrors($I, 'ConvertKit Broadcasts');

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that the block displays.
		$this->_seeBroadcastsBlock($I);	

		// Confirm that the expected number of Broadcasts are displayed.
		$I->seeNumberOfElements('li.convertkit-broadcast', [1,10]);
	}

	/**
	 * Test the Broadcasts block's theme color parameters works.
	 * 
	 * @since 	1.9.7.4
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testBroadcastsBlockWithThemeColorParameters(AcceptanceTester $I)
	{
		// Define colors.
		$backgroundColor = 'white';
		$textColor = 'purple';

		// It's tricky to interact with Gutenberg's color picker, so we programmatically create the Page
		// instead to then confirm the color settings apply on the output.
		// We don't need to test the color picker itself, as it's a Gutenberg supplied component, and our
		// other Acceptance tests confirm that the block can be added in Gutenberg etc.
		$I->havePageInDatabase([
			'post_name' 	=> 'convertkit-page-broadcasts-block-theme-color-params',
			'post_content' 	=> '<!-- wp:convertkit/broadcasts {"backgroundColor":"'.$backgroundColor.'","textColor":"'.$textColor.'"} /-->',
		]);

		// Load the Page on the frontend site.
		$I->amOnPage('/convertkit-page-broadcasts-block-theme-color-params');

		// Wait for frontend web site to load.
		$I->waitForElementVisible('body.page-template-default');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the block displays.
		$this->_seeBroadcastsBlock($I);

		// Confirm that our stylesheet loaded.
		$I->seeInSource('<link rel="stylesheet" id="convertkit-broadcasts-css" href="'.$_ENV['TEST_SITE_WP_URL'].'/wp-content/plugins/convertkit/resources/frontend/css/gutenberg-block-broadcasts.css');

		// Confirm that the chosen colors are applied as CSS styles.
		$I->seeInSource('<ul class="convertkit-broadcasts has-text-color has-'.$textColor.'-color has-background has-'.$backgroundColor.'-background-color"');
	}

	/**
	 * Test the Broadcasts block's hex color parameters works.
	 * 
	 * @since 	1.9.7.4
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
		$this->_seeBroadcastsBlock($I);

		// Confirm that our stylesheet loaded.
		$I->seeInSource('<link rel="stylesheet" id="convertkit-broadcasts-css" href="'.$_ENV['TEST_SITE_WP_URL'].'/wp-content/plugins/convertkit/resources/frontend/css/gutenberg-block-broadcasts.css');

		// Confirm that the chosen colors are applied as CSS styles.
		$I->seeInSource('<ul class="convertkit-broadcasts has-text-color has-background" style="color:'.$textColor.';background-color:'.$backgroundColor.'">');
	}

	/**
	 * Check that expected HTML exists in the DOM of the page we're viewing for
	 * a Broadcasts block.
	 * 
	 * @since 	1.9.7.5
	 *
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	private function _seeBroadcastsBlock($I)
	{
		// Confirm that the block displays.
		$I->seeElementInDOM('ul.convertkit-broadcasts');
		$I->seeElementInDOM('ul.convertkit-broadcasts li.convertkit-broadcast');
		$I->seeElementInDOM('ul.convertkit-broadcasts li.convertkit-broadcast a');
	}

	/**
	 * Deactivate and reset Plugin(s) after each test, if the test passes.
	 * We don't use _after, as this would provide a screenshot of the Plugin
	 * deactivation and not the true test error.
	 * 
	 * @since 	1.9.7.4
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function _passed(AcceptanceTester $I)
	{
		$I->deactivateConvertKitPlugin($I);
		$I->resetConvertKitPlugin($I);
	}
}