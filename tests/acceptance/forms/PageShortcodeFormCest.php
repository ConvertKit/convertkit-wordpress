<?php
/**
 * Tests for the ConvertKit Form shortcode.
 *
 * @since   1.9.6
 */
class PageShortcodeFormCest
{
	/**
	 * Run common actions before running the test functions in this class.
	 *
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _before(AcceptanceTester $I)
	{
		$I->activateConvertKitPlugin($I);
		$I->setupConvertKitPlugin($I);
	}

	/**
	 * Test the [convertkit_form] shortcode works when a valid Form ID is specified,
	 * using the Classic Editor (TinyMCE / Visual).
	 *
	 * @since   1.9.7.5
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testFormShortcodeInVisualEditorWithValidFormParameter(AcceptanceTester $I)
	{
		// Add a Page using the Classic Editor.
		$I->addClassicEditorPage($I, 'page', 'ConvertKit: Page: Form: Shortcode: Visual Editor');

		// Configure metabox's Form setting = None, ensuring we only test the shortcode in the Classic Editor.
		$I->configureMetaboxSettings(
			$I,
			'wp-convertkit-meta-box',
			[
				'form' => [ 'select2', 'None' ],
			]
		);

		// Add shortcode to Page, setting the Form setting to the value specified in the .env file.
		$I->addVisualEditorShortcode(
			$I,
			'ConvertKit Form',
			[
				'form' => [ 'select', $_ENV['CONVERTKIT_API_FORM_NAME'] ],
			],
			'[convertkit_form form="' . $_ENV['CONVERTKIT_API_FORM_ID'] . '"]'
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewClassicEditorPage($I);

		// Confirm that the ConvertKit Form is displayed.
		$I->seeElementInDOM('form[data-sv-form]');
	}

	/**
	 * Test the [convertkit_form] shortcode works when a valid Form ID is specified,
	 * using the Text Editor.
	 *
	 * @since   1.9.7.5
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testFormShortcodeInTextEditorWithValidFormParameter(AcceptanceTester $I)
	{
		// Add a Page using the Classic Editor.
		$I->addClassicEditorPage($I, 'page', 'ConvertKit: Page: Form: Shortcode: Text Editor');

		// Configure metabox's Form setting = None, ensuring we only test the shortcode in the Classic Editor.
		$I->configureMetaboxSettings(
			$I,
			'wp-convertkit-meta-box',
			[
				'form' => [ 'select2', 'None' ],
			]
		);

		// Add shortcode to Page, setting the Form setting to the value specified in the .env file.
		$I->addTextEditorShortcode(
			$I,
			'convertkit-form',
			[
				'form' => [ 'select', $_ENV['CONVERTKIT_API_FORM_NAME'] ],
			],
			'[convertkit_form form="' . $_ENV['CONVERTKIT_API_FORM_ID'] . '"]'
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewClassicEditorPage($I);

		// Confirm that the ConvertKit Form is displayed.
		$I->seeElementInDOM('form[data-sv-form]');
	}

	/**
	 * Test the [convertkit form] shortcode does not output errors when an invalid Form ID is specified.
	 *
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testFormShortcodeWithInvalidFormParameter(AcceptanceTester $I)
	{
		// Create Page with Shortcode.
		$I->havePageInDatabase(
			[
				'post_name'    => 'convertkit-form-shortcode-invalid-form-param',
				'post_content' => '[convertkit form=1]',
			]
		);

		// Load the Page on the frontend site.
		$I->amOnPage('/convertkit-form-shortcode-invalid-form-param');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the ConvertKit Form is not displayed.
		$I->dontSeeElementInDOM('form[data-sv-form]');
	}

	/**
	 * Test the [convertkit id] shortcode works when a valid Form ID is specified.
	 *
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testFormShortcodeWithValidIDParameter(AcceptanceTester $I)
	{
		// Create Page with Shortcode.
		$I->havePageInDatabase(
			[
				'post_name'    => 'convertkit-form-shortcode-valid-id-param',
				'post_content' => '[convertkit id=' . $_ENV['CONVERTKIT_API_FORM_ID'] . ']',
			]
		);

		// Load the Page on the frontend site.
		$I->amOnPage('/convertkit-form-shortcode-valid-id-param');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the ConvertKit Form is displayed.
		$I->seeElementInDOM('form[data-sv-form]');
	}

	/**
	 * Test the [convertkit form] shortcode does not output errors when an invalid Form ID is specified.
	 *
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testFormShortcodeWithInvalidIDParameter(AcceptanceTester $I)
	{
		// Create Page with Shortcode.
		$I->havePageInDatabase(
			[
				'post_name'    => 'convertkit-form-shortcode-invalid-id-param',
				'post_content' => '[convertkit id=1]',
			]
		);

		// Load the Page on the frontend site.
		$I->amOnPage('/convertkit-form-shortcode-invalid-id-param');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the ConvertKit Form is not displayed.
		$I->dontSeeElementInDOM('form[data-sv-form]');
	}

	/**
	 * Test the [convertkit form] shortcode works when a valid Form ID is specified,
	 * but the Form ID does not exist in the options table.
	 *
	 * This emulates when a ConvertKit User has:
	 * - added a new ConvertKit Form to their account at https://app.convertkit.com/
	 * - copied the ConvertKit Form Shortcode at https://app.convertkit.com/
	 * - pasted the ConvertKit Form Shortcode into a new WordPress Page
	 * - not navigated to Settings > ConvertKit to refresh the Plugin's Form Resources.
	 *
	 * @since   1.9.6.2
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testFormShortcodeWhenFormDoesNotExistInPluginFormResources(AcceptanceTester $I)
	{
		// Update the Form Resource option table value to only contain a dummy Form with an ID
		// that does not match the shortcode Form's ID.
		$I->haveOptionInDatabase(
			'convertkit_forms',
			[
				1234 => [
					'id'       => 1234,
					'uid'      => 1234,
					'embed_js' => 'fake',
				],
			]
		);

		// Create Page with Shortcode.
		$I->havePageInDatabase(
			[
				'post_name'    => 'convertkit-form-shortcode-no-form-resources',
				'post_content' => '[convertkit form=' . $_ENV['CONVERTKIT_API_FORM_ID'] . ']',
			]
		);

		// Load the Page on the frontend site.
		$I->amOnPage('/convertkit-form-shortcode-no-form-resources');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the ConvertKit Form is displayed.
		$I->seeElementInDOM('form[data-sv-form]');
	}

	/**
	 * Test the [convertkit form] shortcode works when a valid Legacy Form ID is specified.
	 *
	 * @since   1.9.6.4
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testFormShortcodeWithValidLegacyFormParameter(AcceptanceTester $I)
	{
		// Create Page with Shortcode.
		$I->havePageInDatabase(
			[
				'post_name'    => 'convertkit-form-shortcode-valid-legacy-form-param',
				'post_content' => '[convertkit form=' . $_ENV['CONVERTKIT_API_LEGACY_FORM_ID'] . ']',
			]
		);

		// Load the Page on the frontend site.
		$I->amOnPage('/convertkit-form-shortcode-valid-legacy-form-param');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the ConvertKit Default Legacy Form displays.
		$I->seeInSource('<form id="ck_subscribe_form" class="ck_subscribe_form" action="https://api.convertkit.com/landing_pages/' . $_ENV['CONVERTKIT_API_LEGACY_FORM_ID'] . '/subscribe" data-remote="true">');
	}

	/**
	 * Test the [convertkit id] shortcode works when a valid Legacy Form ID is specified.
	 *
	 * @since   1.9.6.4
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testFormShortcodeWithValidLegacyIDParameter(AcceptanceTester $I)
	{
		// Create Page with Shortcode.
		$I->havePageInDatabase(
			[
				'post_name'    => 'convertkit-form-shortcode-valid-legacy-id-param',
				'post_content' => '[convertkit id=' . $_ENV['CONVERTKIT_API_LEGACY_FORM_ID'] . ']',
			]
		);

		// Load the Page on the frontend site.
		$I->amOnPage('/convertkit-form-shortcode-valid-legacy-id-param');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the ConvertKit Default Legacy Form displays.
		$I->seeInSource('<form id="ck_subscribe_form" class="ck_subscribe_form" action="https://api.convertkit.com/landing_pages/' . $_ENV['CONVERTKIT_API_LEGACY_FORM_ID'] . '/subscribe" data-remote="true">');
	}

	/**
	 * Test the [convertkit form] shortcode, as supplied by app.convertkit.com, works when a valid Legacy Form ID is specified.
	 * The shortcode form's number / ID differs from the ID given to us in the API.
	 * For example, a Legacy Form ID might be 470099, but the ConvertKit app says to use the shortcode [convertkit form=5281783]).
	 *
	 * @since   1.9.6.4
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testFormShortcodeWithValidLegacyFormShortcodeFromConvertKitApp(AcceptanceTester $I)
	{
		// Create Page with Shortcode.
		$I->havePageInDatabase(
			[
				'post_name'    => 'convertkit-form-shortcode-valid-legacy-form-shortcode-from-convertkit-app',
				'post_content' => $_ENV['CONVERTKIT_API_LEGACY_FORM_SHORTCODE'],
			]
		);

		// Load the Page on the frontend site.
		$I->amOnPage('/convertkit-form-shortcode-valid-legacy-form-shortcode-from-convertkit-app');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the ConvertKit Default Legacy Form displays.
		$I->seeInSource('<form id="ck_subscribe_form" class="ck_subscribe_form" action="https://api.convertkit.com/landing_pages/' . $_ENV['CONVERTKIT_API_LEGACY_FORM_ID'] . '/subscribe" data-remote="true">');
	}

	/**
	 * Deactivate and reset Plugin(s) after each test, if the test passes.
	 * We don't use _after, as this would provide a screenshot of the Plugin
	 * deactivation and not the true test error.
	 *
	 * @since   1.9.6.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _passed(AcceptanceTester $I)
	{
		$I->deactivateThirdPartyPlugin($I, 'classic-editor');
		$I->deactivateConvertKitPlugin($I);
		$I->resetConvertKitPlugin($I);
	}
}
