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
		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Form: Block: Valid Form Param');

		// Change Form to None, so that no Plugin level Form is displayed, ensuring we only test the block in Gutenberg.
		$I->selectOption('#wp-convertkit-form', 'None');

		// Add block to Page, setting its Form setting to the required ConvertKit Form.
		$I->addGutenbergBlock($I, 'ConvertKit Form', 'convertkit-form', [
			'form' => [ 'select', $_ENV['CONVERTKIT_API_FORM_NAME'] ]
		]);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

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
		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Legacy Form: Block: Valid Form Param');

		// Change Form to None, so that no Plugin level Form is displayed, ensuring we only test the block in Gutenberg.
		$I->selectOption('#wp-convertkit-form', 'None');

		// Add block to Page.
		$I->addGutenbergBlock($I, 'ConvertKit Form', 'convertkit-form', [
			'form' => [ 'select', $_ENV['CONVERTKIT_API_LEGACY_FORM_NAME'] ]
		]);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that the ConvertKit Form is displayed.
		$I->seeInSource('<form id="ck_subscribe_form" class="ck_subscribe_form" action="https://api.convertkit.com/landing_pages/' . $_ENV['CONVERTKIT_API_LEGACY_FORM_ID'] . '/subscribe" data-remote="true">');
	}

	/**
	 * Test the Form block displays a message explaining why the block cannot be previewed
	 * in the Gutenberg editor when a valid Modal Form is selected.
	 * 
	 * @since 	1.9.6.9
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testFormBlockWithValidModalFormParameter(AcceptanceTester $I)
	{
		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Form: Block: Valid Modal Form Param');

		// Change Form to None, so that no Plugin level Form is displayed, ensuring we only test the block in Gutenberg.
		$I->selectOption('#wp-convertkit-form', 'None');

		// Add block to Page.
		$I->addGutenbergBlock($I, 'ConvertKit Form', 'convertkit-form', [
			'form' => [ 'select', $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_NAME'] ]
		]);

		// Switch to iframe preview for the Form block.
		$I->switchToIFrame('iframe[class="components-sandbox"]');

		// Confirm that the Form block iframe sandbox preview displays that the Modal form was selected, and to view the frontend
		// site to see it (we cannot preview Modal forms in the Gutenberg editor due to Gutenberg using an iframe).
		$I->see('Modal form "' . $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_NAME'] . '" selected. View on the frontend site to see the modal form.');

		// Switch back to main window.
		$I->switchToIFrame();

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that the ConvertKit Form is displayed.
		$I->seeElementInDOM('form[data-sv-form]');
	}

	/**
	 * Test the Form block displays a message explaining why the block cannot be previewed
	 * in the Gutenberg editor when a valid Slide In Form is selected.
	 * 
	 * @since 	1.9.6.9
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testFormBlockWithValidSlideInFormParameter(AcceptanceTester $I)
	{
		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Form: Block: Valid Slide In Form Param');

		// Change Form to None, so that no Plugin level Form is displayed, ensuring we only test the block in Gutenberg.
		$I->selectOption('#wp-convertkit-form', 'None');

		// Add block to Page.
		$I->addGutenbergBlock($I, 'ConvertKit Form', 'convertkit-form', [
			'form' => [ 'select', $_ENV['CONVERTKIT_API_FORM_FORMAT_SLIDE_IN_NAME'] ]
		]);

		// Switch to iframe preview for the Form block.
		$I->switchToIFrame('iframe[class="components-sandbox"]');

		// Confirm that the Form block iframe sandbox preview displays that the Modal form was selected, and to view the frontend
		// site to see it (we cannot preview Modal forms in the Gutenberg editor due to Gutenberg using an iframe).
		$I->see('Slide in form "' . $_ENV['CONVERTKIT_API_FORM_FORMAT_SLIDE_IN_NAME'] . '" selected. View on the frontend site to see the slide in form.');

		// Switch back to main window.
		$I->switchToIFrame();

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that the ConvertKit Form is displayed.
		$I->seeElementInDOM('form[data-sv-form]');
	}

	/**
	 * Test the Form block displays a message explaining why the block cannot be previewed
	 * in the Gutenberg editor when a valid Sticky Bar Form is selected.
	 * 
	 * @since 	1.9.6.9
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testFormBlockWithValidStickyBarFormParameter(AcceptanceTester $I)
	{
		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Form: Block: Valid Sticky Bar Form Param');

		// Change Form to None, so that no Plugin level Form is displayed, ensuring we only test the block in Gutenberg.
		$I->selectOption('#wp-convertkit-form', 'None');

		// Add block to Page.
		$I->addGutenbergBlock($I, 'ConvertKit Form', 'convertkit-form', [
			'form' => [ 'select', $_ENV['CONVERTKIT_API_FORM_FORMAT_STICKY_BAR_NAME'] ]
		]);

		// Switch to iframe preview for the Form block.
		$I->switchToIFrame('iframe[class="components-sandbox"]');

		// Confirm that the Form block iframe sandbox preview displays that the Modal form was selected, and to view the frontend
		// site to see it (we cannot preview Modal forms in the Gutenberg editor due to Gutenberg using an iframe).
		$I->see('Sticky bar form "' . $_ENV['CONVERTKIT_API_FORM_FORMAT_STICKY_BAR_NAME'] . '" selected. View on the frontend site to see the sticky bar form.');

		// Switch back to main window.
		$I->switchToIFrame();

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that the ConvertKit Form is displayed.
		$I->seeElementInDOM('form[data-sv-form]');
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
		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Form: Block: Valid Sticky Bar Form Param');

		// Change Form to None, so that no Plugin level Form is displayed, ensuring we only test the block in Gutenberg.
		$I->selectOption('#wp-convertkit-form', 'None');

		// Add block to Page.
		$I->addGutenbergBlock($I, 'ConvertKit Form', 'convertkit-form');

		// Confirm that the Form block displays instructions to the user on how to select a Form.
		$I->see('Select a Form using the Form option in the Gutenberg sidebar.', [
			'css' => '.convertkit-form-no-content',
		]);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

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