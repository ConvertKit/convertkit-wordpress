<?php
/**
 * Tests for the ConvertKit Form's Divi Module.
 *
 * @since   2.5.6
 */
class DiviFormCest
{
	/**
	 * Run common actions before running the test functions in this class.
	 *
	 * @since   2.5.6
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
	 * @since   2.5.6
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testFormModuleInBackendEditor(AcceptanceTester $I)
	{
		// Setup Plugin, without defining default Forms.
		$I->setupConvertKitPluginNoDefaultForms($I);
		$I->setupConvertKitPluginResources($I);

		// Create a Divi Page in the backend editor.
		$I->createDiviPageInBackendEditor($I, 'ConvertKit: Page: Form: Divi: Backend Editor');

		// Insert the Form module.
		$I->insertDiviRowWithModule(
			$I,
			'ConvertKit Form',
			'convertkit_form',
			'form',
			$_ENV['CONVERTKIT_API_FORM_ID']
		);

		// Save Divi module and view the page on the frontend site.
		$I->saveDiviModuleInBackendEditorAndViewPage($I);

		// Confirm that one ConvertKit Form is output in the DOM.
		// This confirms that there is only one script on the page for this form, which renders the form.
		$I->seeNumberOfElementsInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_ID'] . '"]', 1);

		// Deactivate Classic Editor.
		$I->deactivateThirdPartyPlugin($I, 'classic-editor');
	}

	/**
	 * Test the Form module works when a valid Form is selected
	 * using Divi's backend editor.
	 *
	 * @since   2.5.6
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testFormModuleInFrontendEditor(AcceptanceTester $I)
	{
		// Setup Plugin, without defining default Forms.
		$I->setupConvertKitPluginNoDefaultForms($I);
		$I->setupConvertKitPluginResources($I);

		// Create a Divi Page in the frontend editor.
		$url = $I->createDiviPageInFrontendEditor($I, 'ConvertKit: Page: Form: Divi: Frontend Editor');

		// Insert the Form module.
		$I->insertDiviRowWithModule(
			$I,
			'ConvertKit Form',
			'convertkit_form',
			'form',
			$_ENV['CONVERTKIT_API_FORM_ID']
		);

		// Save Divi module and view the page on the frontend site.
		$I->saveDiviModuleInFrontendEditorAndViewPage($I);

		// Confirm that one ConvertKit Form is output in the DOM.
		// This confirms that there is only one script on the page for this form, which renders the form.
		$I->seeNumberOfElementsInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_ID'] . '"]', 1);
	}

	/**
	 * Test the Form module displays the expected message when the Plugin has no credentials
	 *
	 * @since   2.5.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testFormModuleInFrontendEditorWhenNoCredentials(AcceptanceTester $I)
	{
		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Divi: Frontend: No Credentials');

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
		$I->fillField('filterByTitle', 'ConvertKit Form');

		// Insert module.
		$I->waitForElementVisible('li.convertkit_form');
		$I->click('li.convertkit_form');

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
	public function testFormModuleInFrontendEditorWhenNoForms(AcceptanceTester $I)
	{
		// Setup Plugin.
		$I->setupConvertKitPluginCredentialsNoData($I);
		$I->setupConvertKitPluginResourcesNoData($I);

		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Divi: Frontend: No Forms');

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
		$I->fillField('filterByTitle', 'ConvertKit Form');

		// Insert module.
		$I->waitForElementVisible('li.convertkit_form');
		$I->click('li.convertkit_form');

		// Confirm the on screen message displays.
		$I->seeInSource('No forms exist in ConvertKit');
		$I->seeInSource('Add a form to your ConvertKit account, and then refresh this page to select a form.');
	}

	/**
	 * Test the Form module works when a valid Legacy Form is selected.
	 *
	 * @since   2.5.6
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testFormModuleWithValidLegacyFormParameter(AcceptanceTester $I)
	{
		// Setup Plugin, without defining default Forms.
		$I->setupConvertKitPluginNoDefaultForms($I);
		$I->setupConvertKitPluginResources($I);

		// Create Page with Form module in Divi.
		$pageID = $this->_createPageWithFormModule($I, 'ConvertKit: Legacy Form: Divi Module: Valid Form Param', $_ENV['CONVERTKIT_API_LEGACY_FORM_ID']);

		// Load Page.
		$I->amOnPage('?p=' . $pageID);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the ConvertKit Form is displayed.
		$I->seeInSource('<form id="ck_subscribe_form" class="ck_subscribe_form" action="https://api.convertkit.com/landing_pages/' . $_ENV['CONVERTKIT_API_LEGACY_FORM_ID'] . '/subscribe" data-remote="true">');
	}

	/**
	 * Test the Form module works when no Form is selected.
	 *
	 * @since   2.5.6
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testFormModuleWithNoFormParameter(AcceptanceTester $I)
	{
		// Setup Plugin, without defining default Forms.
		$I->setupConvertKitPluginNoDefaultForms($I);
		$I->setupConvertKitPluginResources($I);

		// Create Page with Form module in Divi.
		$pageID = $this->_createPageWithFormModule($I, 'ConvertKit: Page: Form: Divi Module: No Form Param', '');

		// Load Page.
		$I->amOnPage('?p=' . $pageID);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that no ConvertKit Form is displayed.
		$I->dontSeeElementInDOM('form[data-sv-form]');
	}

	/**
	 * Create a Page in the database comprising of Divi Page Builder data
	 * containing a ConvertKit Form module.
	 *
	 * @since   2.5.6
	 *
	 * @param   AcceptanceTester $I      Tester.
	 * @param   string           $title  Page Title.
	 * @param   int              $formID ConvertKit Form ID.
	 * @return  int                         Page ID
	 */
	private function _createPageWithFormModule(AcceptanceTester $I, $title, $formID)
	{
		return $I->havePostInDatabase(
			[
				'post_title'   => $title,
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_content' => '[et_pb_section fb_built="1" _builder_version="4.27.0" _module_preset="default" global_colors_info="{}"]
					[et_pb_row _builder_version="4.27.0" _module_preset="default"]
						[et_pb_column _builder_version="4.27.0" _module_preset="default" type="4_4"]
							[convertkit_form _builder_version="4.27.0" _module_preset="default" form="' . $formID . '" hover_enabled="0" sticky_enabled="0"][/convertkit_form]
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
	 * @since   2.5.6
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
