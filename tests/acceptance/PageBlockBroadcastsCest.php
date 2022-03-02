<?php
/**
 * Tests for the ConvertKit Broadcasts Gutenberg Block.
 * 
 * @since 	1.9.6.9
 */
class PageBlockBroadcastsCest
{
	/**
	 * Run common actions before running the test functions in this class.
	 * 
	 * @since 	1.9.6.9
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
	 * @since 	1.9.6.9
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testBroadcastsBlockWithDefaultParameters(AcceptanceTester $I)
	{
		// Define a Page Title.
		$I->fillField('.editor-post-title__input', 'ConvertKit: Broadcasts: Default Params');

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
		$I->seeInSource('<time datetime="2022-03-01">March 1, 2022</time>');

		// Confirm that the default expected number of Broadcasts are displayed.
		$I->seeNumberOfElements('li.convertkit-broadcast', 10);
	}

	/**
	 * Test the Broadcasts block's date format parameter works.
	 * 
	 * @since 	1.9.6.9
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testBroadcastsBlockWithDateFormatParameter(AcceptanceTester $I)
	{
		// Define a Page Title.
		$I->fillField('.editor-post-title__input', 'ConvertKit: Broadcasts: Date Format Param');

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
		$I->seeInSource('<time datetime="2022-03-01">2022-03-01</time>');

		// Confirm that the default expected number of Broadcasts are displayed.
		$I->seeNumberOfElements('li.convertkit-broadcast', 10);
	}

	/**
	 * Test the Broadcasts block's limit parameter works.
	 * 
	 * @since 	1.9.6.9
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testBroadcastsBlockWithLimitParameter(AcceptanceTester $I)
	{
		// Define a Page Title.
		$I->fillField('.editor-post-title__input', 'ConvertKit: Broadcasts: Limit Param');

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
	 * Deactivate and reset Plugin(s) after each test, if the test passes.
	 * We don't use _after, as this would provide a screenshot of the Plugin
	 * deactivation and not the true test error.
	 * 
	 * @since 	1.9.6.9
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function _passed(AcceptanceTester $I)
	{
		$I->deactivateConvertKitPlugin($I);
		$I->resetConvertKitPlugin($I);
	}
}