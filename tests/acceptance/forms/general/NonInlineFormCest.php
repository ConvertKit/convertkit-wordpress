<?php
/**
 * Tests for non-inline ConvertKit Forms.
 *
 * @since   2.3.9
 */
class NonInlineFormCest
{
	/**
	 * Run common actions before running the test functions in this class.
	 *
	 * @since   2.3.9
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _before(AcceptanceTester $I)
	{
		// Activate ConvertKit plugin.
		$I->activateConvertKitPlugin($I);
	}

	/**
	 * Test that no forms are displayed for the Default Form (Site Wide) option
	 * when no non-inline forms exist.
	 *
	 * @since   2.3.9
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testSettingsWhenNoNonInlineForms(AcceptanceTester $I)
	{
		// Setup Plugin with API Keys for an account that has no non-inline forms.
		$I->setupConvertKitPlugin($I, $_ENV['CONVERTKIT_API_KEY_NO_DATA'], $_ENV['CONVERTKIT_API_SECRET_NO_DATA']);
	}

	/**
	 * Test that the defined default non-inline form displays site wide.
	 *
	 * @since   2.3.9
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testDefaultNonInlineForm(AcceptanceTester $I)
	{
		// Setup Plugin with a non-inline Default Form (Site Wide).
		$I->setupConvertKitPlugin(
			$I,
			false,
			false,
			false,
			false,
			false,
			false,
			false,
			$_ENV['CONVERTKIT_API_FORM_FORMAT_STICKY_BAR_ID']
		);
		$I->setupConvertKitPluginResources($I);

		// Create a Page in the database.
		$I->havePostInDatabase(
			[
				'post_title'  => 'ConvertKit: Default Non Inline Global',
				'post_type'   => 'page',
				'post_status' => 'publish',
			]
		);

		// View the home page.
		$I->amOnPage('/');

		// Confirm that one ConvertKit Form is output in the DOM.
		// This confirms that there is only one script on the page for this form, which renders the form.
		$I->seeNumberOfElementsInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_FORMAT_STICKY_BAR_ID'] . '"]', 1);

		// View Page.
		$I->amOnPage('/convertkit-default-non-inline-global');

		// Confirm that one ConvertKit Form is output in the DOM.
		// This confirms that there is only one script on the page for this form, which renders the form.
		$I->seeNumberOfElementsInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_FORMAT_STICKY_BAR_ID'] . '"]', 1);
	}

	/**
	 * Test that no non-inline form displays site wide when not selected in the Plugin's settings.
	 *
	 * @since   2.3.9
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testNoDefaultNonInlineForm(AcceptanceTester $I)
	{
		// Setup Plugin with a non-inline Default Form (Site Wide).
		$I->setupConvertKitPlugin($I);
		$I->setupConvertKitPluginResources($I);

		// View the home page.
		$I->amOnPage('/');

		// Confirm that no ConvertKit Form is output in the DOM.
		$I->dontSeeElementInDOM('form[data-sv-form]');
	}

	/**
	 * Test that the non-inline form defined as the Default Form for Pages overrides
	 * the non-inline form defined in the Default Non-Inline Form (Global) setting
	 * when a Page is viewed.
	 *
	 * @since   2.3.9
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testDefaultNonInlineFormIgnoredWhenDefaultPageNonInlineFormDefined(AcceptanceTester $I)
	{
		// Setup Plugin with a non-inline Default Form for both Pages and Site Wide.
		$I->setupConvertKitPlugin(
			$I,
			false,
			false,
			$_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_ID'], // Page Default.
			false,
			false,
			false,
			false,
			$_ENV['CONVERTKIT_API_FORM_FORMAT_STICKY_BAR_ID'] // Site Wide.
		);

		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Non-Inline Form: Default');

		// Configure metabox's Form setting = Default.
		$I->configureMetaboxSettings(
			$I,
			'wp-convertkit-meta-box',
			[
				'form' => [ 'select2', 'Default' ],
			]
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that the modal form displays, and no sticky bar form displays.
		$I->seeElementInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_ID'] . '"]');
		$I->dontSeeElementInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_FORMAT_STICKY_BAR_ID'] . '"]');
	}

	/**
	 * Test that the non-inline form defined on a Page overrides the non-inline form defined
	 * in the Default Non-Inline Form (Global) setting when a Page is viewed.
	 *
	 * @since   2.3.9
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testDefaultNonInlineFormIgnoredWhenPageNonInlineFormDefined(AcceptanceTester $I)
	{
		// Setup Plugin with a non-inline Default Form for Site Wide.
		$I->setupConvertKitPlugin(
			$I,
			false,
			false,
			false,
			false,
			false,
			false,
			false,
			$_ENV['CONVERTKIT_API_FORM_FORMAT_STICKY_BAR_ID'] // Site Wide.
		);

		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Non-Inline Form: Specific');

		// Configure metabox's Form setting = Modal Form.
		$I->configureMetaboxSettings(
			$I,
			'wp-convertkit-meta-box',
			[
				'form' => [ 'select2', $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_NAME'] ],
			]
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that the modal form displays, and no sticky bar form displays.
		$I->seeElementInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_ID'] . '"]');
		$I->dontSeeElementInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_FORMAT_STICKY_BAR_ID'] . '"]');
	}

	/**
	 * Test that the non-inline form output using the Form Block overrides the non-inline form defined
	 * in the Default Non-Inline Form (Global) setting when a Page is viewed.
	 *
	 * @since   2.3.9
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testDefaultNonInlineFormIgnoredWhenPageNonInlineFormDefinedInBlock(AcceptanceTester $I)
	{
		// Setup Plugin with a non-inline Default Form for Site Wide.
		$I->setupConvertKitPlugin(
			$I,
			false,
			false,
			false,
			false,
			false,
			false,
			false,
			$_ENV['CONVERTKIT_API_FORM_FORMAT_STICKY_BAR_ID'] // Site Wide.
		);

		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Non-Inline Form: Block');

		// Configure metabox's Form setting = None.
		$I->configureMetaboxSettings(
			$I,
			'wp-convertkit-meta-box',
			[
				'form' => [ 'select2', 'None' ],
			]
		);

		// Add Form block to the Page set to the Modal Form.
		$I->addGutenbergBlock(
			$I,
			'ConvertKit Form',
			'convertkit-form',
			[
				'form' => [ 'select', $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_NAME'] ],
			]
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that the modal form displays, and no sticky bar form displays.
		$I->seeElementInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_ID'] . '"]');
		$I->dontSeeElementInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_FORMAT_STICKY_BAR_ID'] . '"]');
	}

	/**
	 * Test that the non-inline form output using the Form shortcode overrides the non-inline form defined
	 * in the Default Non-Inline Form (Global) setting when a Page is viewed.
	 *
	 * @since   2.3.9
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testDefaultNonInlineFormIgnoredWhenPageNonInlineFormDefinedInShortcode(AcceptanceTester $I)
	{
		// Setup Plugin with a non-inline Default Form for Site Wide.
		$I->setupConvertKitPlugin(
			$I,
			false,
			false,
			false,
			false,
			false,
			false,
			false,
			$_ENV['CONVERTKIT_API_FORM_FORMAT_STICKY_BAR_ID'] // Site Wide.
		);

		// Add a Page using the Classic Editor.
		$I->addClassicEditorPage($I, 'page', 'ConvertKit: Page: Non-Inline Form: Shortcode');

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
				'form' => [ 'select', $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_NAME'] ],
			],
			'[convertkit_form form="' . $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_ID'] . '"]'
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewClassicEditorPage($I);

		// Confirm that the modal form displays, and no sticky bar form displays.
		$I->seeElementInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_ID'] . '"]');
		$I->dontSeeElementInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_FORMAT_STICKY_BAR_ID'] . '"]');
	}

	/**
	 * Test that the non-inline form defined as the Default Form for Posts overrides
	 * the non-inline form defined in the Default Non-Inline Form (Global) setting
	 * when a Post is viewed.
	 *
	 * @since   2.3.9
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testDefaultNonInlineFormIgnoredWhenDefaultPostNonInlineFormDefined(AcceptanceTester $I)
	{
		// Setup Plugin with a non-inline Default Form for both Posts and Site Wide.
		$I->setupConvertKitPlugin(
			$I,
			false,
			false,
			false,
			$_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_ID'], // Post Default.
			false,
			false,
			false,
			$_ENV['CONVERTKIT_API_FORM_FORMAT_STICKY_BAR_ID'] // Site Wide.
		);

		// Add a Post using the Gutenberg editor.
		$I->addGutenbergPage($I, 'post', 'ConvertKit: Post: Non-Inline Form: Default');

		// Configure metabox's Form setting = Default.
		$I->configureMetaboxSettings(
			$I,
			'wp-convertkit-meta-box',
			[
				'form' => [ 'select2', 'Default' ],
			]
		);

		// Publish and view the Post on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that the modal form displays, and no sticky bar form displays.
		$I->seeElementInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_ID'] . '"]');
		$I->dontSeeElementInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_FORMAT_STICKY_BAR_ID'] . '"]');
	}

	/**
	 * Test that the non-inline form defined on a Post overrides the non-inline form defined
	 * in the Default Non-Inline Form (Global) setting when a Post is viewed.
	 *
	 * @since   2.3.9
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testDefaultNonInlineFormIgnoredWhenPostNonInlineFormDefined(AcceptanceTester $I)
	{
		// Setup Plugin with a non-inline Default Form for both Posts and Site Wide.
		$I->setupConvertKitPlugin(
			$I,
			false,
			false,
			false,
			false,
			false,
			false,
			false,
			$_ENV['CONVERTKIT_API_FORM_FORMAT_STICKY_BAR_ID'] // Site Wide.
		);

		// Add a Post using the Gutenberg editor.
		$I->addGutenbergPage($I, 'post', 'ConvertKit: Post: Non-Inline Form: Specific');

		// Configure metabox's Form setting = Modal Form.
		$I->configureMetaboxSettings(
			$I,
			'wp-convertkit-meta-box',
			[
				'form' => [ 'select2', $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_NAME'] ],
			]
		);

		// Publish and view the Post on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that the modal form displays, and no sticky bar form displays.
		$I->seeElementInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_ID'] . '"]');
		$I->dontSeeElementInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_FORMAT_STICKY_BAR_ID'] . '"]');
	}

	/**
	 * Test that the non-inline form defined on a Category overrides the non-inline form defined
	 * in the Default Non-Inline Form (Global) setting when a Post assigned to the Category is viewed.
	 *
	 * @since   2.3.9
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testDefaultNonInlineFormIgnoredWhenPostCategoryNonInlineFormDefined(AcceptanceTester $I)
	{
		// Setup Plugin with a non-inline Default Form for both Posts and Site Wide.
		$I->setupConvertKitPlugin(
			$I,
			false,
			false,
			false,
			false,
			false,
			false,
			false,
			$_ENV['CONVERTKIT_API_FORM_FORMAT_STICKY_BAR_ID'] // Site Wide.
		);

		// Create Category.
		$termID = $I->haveTermInDatabase(
			'ConvertKit: Non Inline Form',
			'category',
			[
				'meta' => [
					'ck_default_form' => $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_ID'],
				],
			]
		);
		$termID = $termID[0];

		// Create Post, assigned to Category.
		$postID = $I->havePostInDatabase(
			[
				'post_type'  => 'post',
				'post_title' => 'ConvertKit: Non Inline Form: Category',
				'tax_input'  => [
					[ 'category' => $termID ],
				],
			]
		);

		// Load the Post on the frontend site.
		$I->amOnPage('/?p=' . $postID);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the modal form displays, and no sticky bar form displays.
		$I->seeElementInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_ID'] . '"]');
		$I->dontSeeElementInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_FORMAT_STICKY_BAR_ID'] . '"]');
	}

	/**
	 * Deactivate and reset Plugin(s) after each test, if the test passes.
	 * We don't use _after, as this would provide a screenshot of the Plugin
	 * deactivation and not the true test error.
	 *
	 * @since   2.3.9
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _passed(AcceptanceTester $I)
	{
		$I->deactivateConvertKitPlugin($I);
		$I->resetConvertKitPlugin($I);
	}
}
