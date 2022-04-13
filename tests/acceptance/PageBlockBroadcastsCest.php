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
		$I->wait(2);

		// Navigate to Pages > Add New
		$I->amOnAdminPage('post-new.php?post_type=page');

		// Close the Gutenberg "Welcome to the block editor" dialog if it's displayed
		$I->maybeCloseGutenbergWelcomeModal($I);

		// Change Form to None, so that no Plugin level Form is displayed, ensuring we only
		// test the Form block in Gutenberg.
		$I->selectOption('#wp-convertkit-form', 'None');
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
		// Define a Page Title.
		$I->fillField('.editor-post-title__input', 'ConvertKit: Page: Broadcasts: Default Params');

		// Add block to Page.
		$I->gutenbergAddBlock($I, 'ConvertKit Broadcasts', 'convertkit-broadcasts');

		// Click the Publish button.
		$I->click('.editor-post-publish-button__button');
		
		// When the pre-publish panel displays, click Publish again.
		$I->performOn('.editor-post-publish-panel__prepublish', function($I) {
			$I->click('.editor-post-publish-panel__header-publish-button .editor-post-publish-button__button');	
		});

		// Wait for confirmation that the Page published.
		$I->waitForElementVisible('.post-publish-panel__postpublish-buttons a.components-button');

		// Load the Page on the frontend site.
		$I->click('.post-publish-panel__postpublish-buttons a.components-button');

		// Wait for frontend web site to load.
		$I->waitForElementVisible('body.page-template-default');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

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
		// Define a Page Title.
		$I->fillField('.editor-post-title__input', 'ConvertKit: Page: Broadcasts: Date Format Param');

		// Add block to Page.
		$I->gutenbergAddBlock($I, 'ConvertKit Broadcasts', 'convertkit-broadcasts');

		// When the sidebar appears, define the date format.
		$I->waitForElementVisible('.interface-interface-skeleton__sidebar[aria-label="Editor settings"]');
		$I->selectOption('#convertkit_broadcasts_date_format', 'Y-m-d');

		// Click the Publish button.
		$I->click('.editor-post-publish-button__button');
		
		// When the pre-publish panel displays, click Publish again.
		$I->performOn('.editor-post-publish-panel__prepublish', function($I) {
			$I->click('.editor-post-publish-panel__header-publish-button .editor-post-publish-button__button');	
		});

		// Wait for confirmation that the Page published.
		$I->waitForElementVisible('.post-publish-panel__postpublish-buttons a.components-button');

		// Load the Page on the frontend site.
		$I->click('.post-publish-panel__postpublish-buttons a.components-button');

		// Wait for frontend web site to load.
		$I->waitForElementVisible('body.page-template-default');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the block displays.
		$I->seeElementInDOM('ul.convertkit-broadcasts');
		$I->seeElementInDOM('ul.convertkit-broadcasts li.convertkit-broadcast');
		$I->seeElementInDOM('ul.convertkit-broadcasts li.convertkit-broadcast a');	

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
		// Define a Page Title.
		$I->fillField('.editor-post-title__input', 'ConvertKit: Page: Broadcasts: Limit Param');

		// Add block to Page.
		$I->gutenbergAddBlock($I, 'ConvertKit Broadcasts', 'convertkit-broadcasts');

		// When the sidebar appears, define the limit.
		$I->waitForElementVisible('.interface-interface-skeleton__sidebar[aria-label="Editor settings"]');
		$I->fillField('#convertkit_broadcasts_limit', 2);

		// Click the Publish button.
		$I->click('.editor-post-publish-button__button');
		
		// When the pre-publish panel displays, click Publish again.
		$I->performOn('.editor-post-publish-panel__prepublish', function($I) {
			$I->click('.editor-post-publish-panel__header-publish-button .editor-post-publish-button__button');	
		});

		// Wait for confirmation that the Page published.
		$I->waitForElementVisible('.post-publish-panel__postpublish-buttons a.components-button');

		// Load the Page on the frontend site.
		$I->click('.post-publish-panel__postpublish-buttons a.components-button');

		// Wait for frontend web site to load.
		$I->waitForElementVisible('body.page-template-default');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the block displays.
		$I->seeElementInDOM('ul.convertkit-broadcasts');
		$I->seeElementInDOM('ul.convertkit-broadcasts li.convertkit-broadcast');
		$I->seeElementInDOM('ul.convertkit-broadcasts li.convertkit-broadcast a');	

		// Confirm that the expected number of Broadcasts are displayed.
		$I->seeNumberOfElements('li.convertkit-broadcast', 2);
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
		$I->seeElementInDOM('ul.convertkit-broadcasts');
		$I->seeElementInDOM('ul.convertkit-broadcasts li.convertkit-broadcast');
		$I->seeElementInDOM('ul.convertkit-broadcasts li.convertkit-broadcast a');

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
		$I->seeElementInDOM('ul.convertkit-broadcasts');
		$I->seeElementInDOM('ul.convertkit-broadcasts li.convertkit-broadcast');
		$I->seeElementInDOM('ul.convertkit-broadcasts li.convertkit-broadcast a');

		// Confirm that our stylesheet loaded.
		$I->seeInSource('<link rel="stylesheet" id="convertkit-broadcasts-css" href="'.$_ENV['TEST_SITE_WP_URL'].'/wp-content/plugins/convertkit/resources/frontend/css/gutenberg-block-broadcasts.css');

		// Confirm that the chosen colors are applied as CSS styles.
		$I->seeInSource('<ul class="convertkit-broadcasts has-text-color has-background" style="color:'.$textColor.';background-color:'.$backgroundColor.'">');
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