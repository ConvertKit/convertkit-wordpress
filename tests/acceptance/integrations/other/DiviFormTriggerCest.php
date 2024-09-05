<?php
/**
 * Tests for the ConvertKit Form's Divi Module.
 *
 * @since   2.5.7
 */
class DiviFormTriggerCest
{
	/**
	 * Run common actions before running the test functions in this class.
	 *
	 * @since   2.5.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _before(AcceptanceTester $I)
	{
		$I->activateConvertKitPlugin($I);
		$I->activateThirdPartyPlugin($I, 'divi-builder');
	}

	/**
	 * Test the Form module works when a valid Form is selected
	 * using Divi's backend editor.
	 *
	 * @since   2.5.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testFormTriggerModuleInBackendEditor(AcceptanceTester $I)
	{
		// Setup Plugin, without defining default Forms.
		$I->setupConvertKitPluginNoDefaultForms($I);
		$I->setupConvertKitPluginResources($I);

		// Activate Classic Editor Plugin.
		$I->activateThirdPartyPlugin($I, 'classic-editor');

		// Add a Page using the Classic Editor.
		$I->addClassicEditorPage($I, 'page', 'ConvertKit: Page: Form Trigger: Divi: Backend Editor');

		// Configure metabox's Form setting = None, ensuring we only test the block in Gutenberg.
		$I->configureMetaboxSettings(
			$I,
			'wp-convertkit-meta-box',
			[
				'form' => [ 'select2', 'None' ],
			]
		);

		// Scroll to Publish meta box, so its buttons are not hidden.
		$I->scrollTo('#submitdiv');

		// Wait for the Publish button to change its state from disabled (WordPress disables it for a moment when auto-saving).
		$I->waitForElementVisible('input#publish:not(:disabled)');

		// Click the Publish button twice, because Divi is flaky at best.
		$I->click('input#publish');
		$I->wait(2);
		$I->click('input#publish');

		// Wait for notice to display.
		$I->waitForElementNotVisible('.et-fb-preloader');
		$I->waitForElementVisible('.notice-success');

		// Remove transient set by Divi that would show the welcome modal.
		$I->dontHaveTransientInDatabase('et_builder_show_bfb_welcome_modal');

		// Click Divi Builder button.
		$I->click('#et_pb_toggle_builder');

		// Dismiss modal if displayed.
		// May have been dismissed by other tests in the suite e.g. DiviFormCest.
		try {
			$I->waitForElementVisible('.et-core-modal-action-dont-restore');
			$I->click('.et-core-modal-action-dont-restore');
		} catch ( \Facebook\WebDriver\Exception\NoSuchElementException $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
			// No modal exists, so nothing to dismiss.
		}

		// Click Build from scratch button.
		$I->waitForElementVisible('.et-fb-page-creation-card-build_from_scratch');
		$I->click('Start Building', '.et-fb-page-creation-card-build_from_scratch');

		// Insert row.
		$I->waitForElementVisible('li[data-layout="4_4"]');
		$I->click('li[data-layout="4_4"]');

		// Search for module.
		$I->waitForElementVisible('input[name="filterByTitle"]');
		$I->fillField('filterByTitle', 'ConvertKit Form');

		// Insert module.
		$I->waitForElementVisible('li.convertkit_formtrigger');
		$I->click('li.convertkit_formtrigger');

		// Select Form.
		$I->waitForElementVisible('#et-fb-form');
		$I->click('#et-fb-form');
		$I->click('li[data-value="' . $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_ID'] . '"]', '#et-fb-form');

		// Save module.
		$I->click('button[data-tip="Save Changes"]');

		// Update page.
		$I->click('Update');

		// Load the Page on the frontend site.
		$I->waitForElementNotVisible('.et-fb-preloader');
		$I->waitForElementVisible('.notice-success');
		$I->click('.notice-success a');

		// Wait for frontend web site to load.
		$I->waitForElementVisible('body');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the block displays.
		$I->seeFormTriggerOutput($I, $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_URL'], 'Subscribe');

		// Confirm that one ConvertKit Form is output in the DOM.
		// This confirms that there is only one script on the page for this form, which renders the form.
		$I->seeNumberOfElementsInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_ID'] . '"]', 1);

		// Deactivate Classic Editor.
		$I->deactivateThirdPartyPlugin($I, 'classic-editor');
	}

	/**
	 * Test the Form module works when a valid Form is selected
	 * using Divi's backend editor.
	 *
	 * @since   2.5.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testFormTriggerModuleInFrontendEditor(AcceptanceTester $I)
	{
		// Setup Plugin, without defining default Forms.
		$I->setupConvertKitPluginNoDefaultForms($I);
		$I->setupConvertKitPluginResources($I);

		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Form Trigger: Divi: Frontend');

		// Configure metabox's Form setting = None, ensuring we only test the block in Gutenberg.
		$I->configureMetaboxSettings(
			$I,
			'wp-convertkit-meta-box',
			[
				'form' => [ 'select2', 'None' ],
			]
		);

		// Publish Page.
		$url = $I->publishGutenbergPage($I);

		// Click Divi Builder button.
		$I->click('Use Divi Builder');

		// Reload page to dismiss modal.
		$I->wait(5);
		$I->amOnUrl($url . '?et_fb=1&PageSpeed=off');

		// Click Build from scratch button.
		$I->waitForElementVisible('.et-fb-page-creation-card-build_from_scratch', 30);
		$I->click('Start Building', '.et-fb-page-creation-card-build_from_scratch');

		// Insert row.
		$I->waitForElementVisible('li[data-layout="4_4"]');
		$I->click('li[data-layout="4_4"]');

		// Search for module.
		$I->waitForElementVisible('input[name="filterByTitle"]');
		$I->fillField('filterByTitle', 'ConvertKit Form Trigger');

		// Insert module.
		$I->waitForElementVisible('li.convertkit_formtrigger');
		$I->click('li.convertkit_formtrigger');

		// Select Form.
		$I->waitForElementVisible('#et-fb-form');
		$I->click('#et-fb-form');
		$I->click('li[data-value="' . $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_ID'] . '"]', '#et-fb-form');

		// Save module.
		$I->click('button[data-tip="Save Changes"]');

		// Save page.
		$I->click('.et-fb-page-settings-bar__toggle-button');
		$I->waitForElementVisible('button.et-fb-button--publish');
		$I->click('button.et-fb-button--publish');
		$I->wait(3);

		// Load page without Divi frontend builder.
		$I->amOnUrl($url);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the block displays.
		$I->seeFormTriggerOutput($I, $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_URL'], 'Subscribe');

		// Confirm that one ConvertKit Form is output in the DOM.
		// This confirms that there is only one script on the page for this form, which renders the form.
		$I->seeNumberOfElementsInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_ID'] . '"]', 1);
	}

	/**
	 * Test the Form module displays the expected message when the Plugin has no credentials
	 *
	 * @since   2.5.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testFormTriggerModuleInFrontendEditorWhenNoCredentials(AcceptanceTester $I)
	{
		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Form Trigger: Divi: Frontend: No Credentials');

		// Publish Page.
		$url = $I->publishGutenbergPage($I);

		// Click Divi Builder button.
		$I->click('Use Divi Builder');

		// Reload page to dismiss modal.
		$I->wait(5);
		$I->amOnUrl($url . '?et_fb=1&PageSpeed=off');

		// Click Build from scratch button.
		$I->waitForElementVisible('.et-fb-page-creation-card-build_from_scratch', 30);
		$I->click('Start Building', '.et-fb-page-creation-card-build_from_scratch');

		// Insert row.
		$I->waitForElementVisible('li[data-layout="4_4"]');
		$I->click('li[data-layout="4_4"]');

		// Search for module.
		$I->waitForElementVisible('input[name="filterByTitle"]');
		$I->fillField('filterByTitle', 'ConvertKit Form Trigger');

		// Insert module.
		$I->waitForElementVisible('li.convertkit_formtrigger');
		$I->click('li.convertkit_formtrigger');

		// Confirm the on screen message displays.
		$I->seeInSource('Not connected to ConvertKit');
		$I->seeInSource('Connect your ConvertKit account at Settings > ConvertKit, and then refresh this page to select a form.');
	}

	/**
	 * Test the Form module displays the expected message when the ConvertKit account
	 * has no forms.
	 *
	 * @since   2.5.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testFormTriggerModuleInFrontendEditorWhenNoForms(AcceptanceTester $I)
	{
		// Setup Plugin.
		$I->setupConvertKitPluginCredentialsNoData($I);
		$I->setupConvertKitPluginResourcesNoData($I);

		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Form Trigger: Divi: Frontend: No Forms');

		// Publish Page.
		$url = $I->publishGutenbergPage($I);

		// Click Divi Builder button.
		$I->click('Use Divi Builder');

		// Reload page to dismiss modal.
		$I->wait(5);
		$I->amOnUrl($url . '?et_fb=1&PageSpeed=off');

		// Click Build from scratch button.
		$I->waitForElementVisible('.et-fb-page-creation-card-build_from_scratch', 30);
		$I->click('Start Building', '.et-fb-page-creation-card-build_from_scratch');

		// Insert row.
		$I->waitForElementVisible('li[data-layout="4_4"]');
		$I->click('li[data-layout="4_4"]');

		// Search for module.
		$I->waitForElementVisible('input[name="filterByTitle"]');
		$I->fillField('filterByTitle', 'ConvertKit Form Trigger');

		// Insert module.
		$I->waitForElementVisible('li.convertkit_formtrigger');
		$I->click('li.convertkit_formtrigger');

		// Confirm the on screen message displays.
		$I->seeInSource('No modal, sticky bar or slide in forms exist in ConvertKit');
		$I->seeInSource('Add a non-inline form to your ConvertKit account, and then refresh this page to select a form.');
	}

	/**
	 * Test the Form module works when no Form is selected.
	 *
	 * @since   2.5.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testFormTriggerModuleWithNoFormParameter(AcceptanceTester $I)
	{
		// Setup Plugin, without defining default Forms.
		$I->setupConvertKitPluginNoDefaultForms($I);
		$I->setupConvertKitPluginResources($I);

		// Create Page with Form module in Divi.
		$pageID = $this->_createPageWithFormTriggerModule($I, 'ConvertKit: Page: Form Trigger: Divi Module: No Form Param', '');

		// Load Page.
		$I->amOnPage('?p=' . $pageID);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that no ConvertKit Form trigger button is displayed.
		$I->dontSeeFormTriggerOutput($I);
	}

	/**
	 * Create a Page in the database comprising of Divi Page Builder data
	 * containing a ConvertKit Form module.
	 *
	 * @since   2.5.7
	 *
	 * @param   AcceptanceTester $I      Tester.
	 * @param   string           $title  Page Title.
	 * @param   int              $formID ConvertKit Form ID.
	 * @return  int                         Page ID
	 */
	private function _createPageWithFormTriggerModule(AcceptanceTester $I, $title, $formID)
	{
		return $I->havePostInDatabase(
			[
				'post_title'   => $title,
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_content' => '[et_pb_section fb_built="1" _builder_version="4.27.0" _module_preset="default" global_colors_info="{}"]
					[et_pb_row _builder_version="4.27.0" _module_preset="default"]
						[et_pb_column _builder_version="4.27.0" _module_preset="default" type="4_4"]
							[convertkit_formtrigger _builder_version="4.27.0" _module_preset="default" form="' . $formID . '" hover_enabled="0" sticky_enabled="0"][/convertkit_formtrigger]
						[/et_pb_column]
					[/et_pb_row]
				[/et_pb_section]',
				'meta_input'   => [
					// Enable Divi Builder.
					'_et_pb_use_builder'         => 'on',
					'_et_pb_built_for_post_type' => 'page',

					// Configure ConvertKit Plugin to not display a default Form,
					// as we are testing for the Form in Elementor.
					'_wp_convertkit_post_meta'   => [
						'form'         => '0',
						'landing_page' => '',
						'tag'          => '',
					],
				],
			]
		);
	}

	/**
	 * Deactivate and reset Plugin(s) after each test, if the test passes.
	 * We don't use _after, as this would provide a screenshot of the Plugin
	 * deactivation and not the true test error.
	 *
	 * @since   2.5.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _passed(AcceptanceTester $I)
	{
		$I->deactivateThirdPartyPlugin($I, 'divi-builder');
		$I->deactivateConvertKitPlugin($I);
		$I->resetConvertKitPlugin($I);
	}
}
