<?php
/**
 * Tests for the ConvertKit Form's Gutenberg Block.
 * 
 * @since 	1.9.6
 */
class PageBlockFormCest
{
	/**
	 * Run common actions before running the test functions in this class.
	 * 
	 * @since 	1.9.6
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function _before(AcceptanceTester $I)
	{
		$I->activateConvertKitPlugin($I);
		$I->setupConvertKitPlugin($I);
		$I->enableDebugLog($I);

		// Navigate to Pages > Add New
		$I->amOnAdminPage('post-new.php?post_type=page');

		// Close the Gutenberg "Welcome to the block editor" dialog if it's displayed
		$I->maybeCloseGutenbergWelcomeModal($I);

		// Change Form to None, so that no Plugin level Form is displayed, ensuring we only
		// test the Form block in Gutenberg.
		$I->selectOption('#wp-convertkit-form', 'None');
	}

	/**
	 * Test the Form block works when a valid Form is selected.
	 * 
	 * @since 	1.9.6
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testFormBlockWithValidFormParameter(AcceptanceTester $I)
	{
		// Define a Page Title.
		$I->fillField('.editor-post-title__input', 'ConvertKit: Form: Block: Valid Form Param');

		// Click Add Block Button.
		$I->click('button.edit-post-header-toolbar__inserter-toggle');

		// When the Blocks sidebar appears, search for the ConvertKit Form block.
		$I->waitForElementVisible('.interface-interface-skeleton__secondary-sidebar[aria-label="Block library"]');
		$I->fillField('.block-editor-inserter__content input[type=search]', 'ConvertKit Form');
		$I->seeElementInDOM('.block-editor-inserter__panel-content button.editor-block-list-item-convertkit-form');
		$I->click('.block-editor-inserter__panel-content button.editor-block-list-item-convertkit-form');

		// When the sidebar appears, select the Form.
		$I->waitForElementVisible('.interface-interface-skeleton__sidebar[aria-label="Editor settings"]');
		$I->selectOption('#convertkit_form_form', $_ENV['CONVERTKIT_API_FORM_NAME']);

		// Click the Publish button.
		$I->click('.editor-post-publish-button__button');
		
		// When the pre-publish panel displays, click Publish again.
		$I->performOn('.editor-post-publish-panel__prepublish', function($I) {
			$I->click('.editor-post-publish-panel__header-publish-button .editor-post-publish-button__button');	
		});

		// Wait for confirmation that the Page published.
		$I->waitForElementVisible('.post-publish-panel__postpublish-buttons a.components-button');

		// Load the Page on the frontend site.
		$I->click('View Page');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the ConvertKit Form is displayed.
		$I->seeElementInDOM('form[data-sv-form]');
	}

	/**
	 * Test the Form block works when a valid Legacy Form is selected.
	 * 
	 * @since 	1.9.6
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testFormBlockWithValidLegacyFormParameter(AcceptanceTester $I)
	{
		// Define a Page Title.
		$I->fillField('.editor-post-title__input', 'ConvertKit: Legacy Form: Block: Valid Form Param');

		// Click Add Block Button.
		$I->click('button.edit-post-header-toolbar__inserter-toggle');

		// When the Blocks sidebar appears, search for the ConvertKit Form block.
		$I->waitForElementVisible('.interface-interface-skeleton__secondary-sidebar[aria-label="Block library"]');
		$I->fillField('.block-editor-inserter__content input[type=search]', 'ConvertKit Form');
		$I->seeElementInDOM('.block-editor-inserter__panel-content button.editor-block-list-item-convertkit-form');
		$I->click('.block-editor-inserter__panel-content button.editor-block-list-item-convertkit-form');

		// When the sidebar appears, select the Form.
		$I->waitForElementVisible('.interface-interface-skeleton__sidebar[aria-label="Editor settings"]');
		$I->selectOption('#convertkit_form_form', $_ENV['CONVERTKIT_API_LEGACY_FORM_NAME']);

		// Click the Publish button.
		$I->click('.editor-post-publish-button__button');
		
		// When the pre-publish panel displays, click Publish again.
		$I->performOn('.editor-post-publish-panel__prepublish', function($I) {
			$I->click('.editor-post-publish-panel__header-publish-button .editor-post-publish-button__button');	
		});

		// Wait for confirmation that the Page published.
		$I->waitForElementVisible('.post-publish-panel__postpublish-buttons a.components-button');

		// Load the Page on the frontend site.
		$I->click('View Page');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the ConvertKit Form is displayed.
		$I->seeInSource('<form id="ck_subscribe_form" class="ck_subscribe_form" action="https://api.convertkit.com/landing_pages/' . $_ENV['CONVERTKIT_API_LEGACY_FORM_ID'] . '/subscribe" data-remote="true">');
	}

	/**
	 * Test the Form block works when no Form is selected.
	 * 
	 * @since 	1.9.6
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testFormBlockWithNoFormParameter(AcceptanceTester $I)
	{
		// Define a Page Title.
		$I->fillField('.editor-post-title__input', 'ConvertKit: Form: Block: No Form Param');

		// Click Add Block Button.
		$I->click('button.edit-post-header-toolbar__inserter-toggle');

		// When the Blocks sidebar appears, search for the ConvertKit Form block.
		$I->waitForElementVisible('.interface-interface-skeleton__secondary-sidebar[aria-label="Block library"]');
		$I->fillField('.block-editor-inserter__content input[type=search]', 'ConvertKit Form');
		$I->seeElementInDOM('.block-editor-inserter__panel-content button.editor-block-list-item-convertkit-form');
		$I->click('.block-editor-inserter__panel-content button.editor-block-list-item-convertkit-form');

		// Click the Publish button.
		$I->click('.editor-post-publish-button__button');
		
		// When the pre-publish panel displays, click Publish again.
		$I->performOn('.editor-post-publish-panel__prepublish', function($I) {
			$I->click('.editor-post-publish-panel__header-publish-button .editor-post-publish-button__button');	
		});

		// Wait for confirmation that the Page published.
		$I->waitForElementVisible('.post-publish-panel__postpublish-buttons a.components-button');

		// Load the Page on the frontend site.
		$I->click('View Page');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that no ConvertKit Form is displayed.
		$I->dontSeeElementInDOM('form[data-sv-form]');
	}

	/**
	 * Deactivate and reset Plugin(s) after each test, if the test passes.
	 * We don't use _after, as this would provide a screenshot of the Plugin
	 * deactivation and not the true test error.
	 * 
	 * @since 	1.9.6.7
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function _passed(AcceptanceTester $I)
	{
		$I->deactivateConvertKitPlugin($I);
		$I->resetConvertKitPlugin($I);
	}
}