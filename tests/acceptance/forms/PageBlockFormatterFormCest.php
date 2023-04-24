<?php
/**
 * Tests for the ConvertKit Form Trigger Gutenberg Block Formatter.
 *
 * @since   2.2.0
 */
class PageBlockFormatterFormTriggerCest
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
	 * Test the Form Trigger formatter works when selecting a modal form.
	 *
	 * @since   2.2.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testFormTriggerFormatterWithModalForm(AcceptanceTester $I)
	{
		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Form Trigger Formatter: Modal Form');

		// Configure metabox's Form setting = None, ensuring we only test the block in Gutenberg.
		$I->configureMetaboxSettings(
			$I,
			'wp-convertkit-meta-box',
			[
				'form' => [ 'select2', 'None' ],
			]
		);

		// Add paragraph to Page.
		$I->addGutenbergParagraphBlock($I, 'Subscribe');

		// Select text.
		$I->pressKey( '.wp-block-post-content p[data-empty="false"]', array( \Facebook\WebDriver\WebDriverKeys::COMMAND, 'a' ) );

		// Apply formatter to link the selected text.
		$I->applyGutenbergFormatter(
			$I,
			'ConvertKit Form Trigger',
			'convertkit-form-link',
			[
				// Form.
				'data-id' => [ 'select', $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_NAME'] ],
			]
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that the link displays and works when clicked.
		$I->seeFormTriggerLinkOutput($I, $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_URL'], 'Subscribe');

		// Confirm that one ConvertKit Form is output in the DOM.
		// This confirms that there is only one script on the page for this form, which renders the form.
		$I->seeNumberOfElementsInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_ID'] . '"]', 1);
	}

	/**
	 * Test the Form Trigger formatter is applied and removed when selecting a modal form, and then
	 * selecting the 'None' option.
	 *
	 * @since   2.2.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testFormTriggerFormatterToggleFormSelection(AcceptanceTester $I)
	{
		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Form Trigger Formatter: Modal Form Toggle');

		// Configure metabox's Form setting = None, ensuring we only test the block in Gutenberg.
		$I->configureMetaboxSettings(
			$I,
			'wp-convertkit-meta-box',
			[
				'form' => [ 'select2', 'None' ],
			]
		);

		// Add paragraph to Page.
		$I->addGutenbergParagraphBlock($I, 'Subscribe');

		// Select text.
		$I->pressKey( '.wp-block-post-content p[data-empty="false"]', array( \Facebook\WebDriver\WebDriverKeys::COMMAND, 'a' ) );

		// Apply formatter to link the selected text.
		$I->applyGutenbergFormatter(
			$I,
			'ConvertKit Form Trigger',
			'convertkit-form-link',
			[
				// Form.
				'data-id' => [ 'select', $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_NAME'] ],
			]
		);

		// Apply the formatter again, this time selecting the 'None' option.
		$I->applyGutenbergFormatter(
			$I,
			'ConvertKit Form Trigger',
			'convertkit-form-link',
			[
				// Form.
				'data-id' => [ 'select', 'None' ],
			]
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that the link does not display, as no form was selected.
		$I->dontSeeFormTriggerLinkOutput($I);
	}

	/**
	 * Test the Form Trigger formatter works when no form is selected.
	 *
	 * @since   2.2.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testFormTriggerFormatterWithNoForm(AcceptanceTester $I)
	{
		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Form Trigger Formatter: No Form');

		// Configure metabox's Form setting = None, ensuring we only test the block in Gutenberg.
		$I->configureMetaboxSettings(
			$I,
			'wp-convertkit-meta-box',
			[
				'form' => [ 'select2', 'None' ],
			]
		);

		// Add paragraph to Page.
		$I->addGutenbergParagraphBlock($I, 'Subscribe');

		// Select text.
		$I->pressKey( '.wp-block-post-content p[data-empty="false"]', array( \Facebook\WebDriver\WebDriverKeys::COMMAND, 'a' ) );

		// Apply formatter to link the selected text.
		$I->applyGutenbergFormatter(
			$I,
			'ConvertKit Form Trigger',
			'convertkit-form-link',
			[
				// Form.
				'data-id' => [ 'select', 'None' ],
			]
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that the link does not display, as no form was selected.
		$I->dontSeeFormTriggerLinkOutput($I);
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
