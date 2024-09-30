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

		// Create a Divi Page in the backend editor.
		$I->createDiviPageInBackendEditor($I, 'Kit: Page: Form Trigger: Divi: Backend Editor');

		// Insert the Form module.
		$I->insertDiviRowWithModule(
			$I,
			'Kit Form Trigger',
			'convertkit_formtrigger',
			'form',
			$_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_ID']
		);

		// Save Divi module and view the page on the frontend site.
		$I->saveDiviModuleInBackendEditorAndViewPage($I);

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

		// Create a Divi Page in the frontend editor.
		$url = $I->createDiviPageInFrontendEditor($I, 'Kit: Page: Form Trigger: Divi: Frontend Editor');

		// Insert the Form module.
		$I->insertDiviRowWithModule(
			$I,
			'Kit Form Trigger',
			'convertkit_formtrigger',
			'form',
			$_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_ID']
		);

		// Save Divi module and view the page on the frontend site.
		$I->saveDiviModuleInFrontendEditorAndViewPage($I, $url);

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
		// Create a Divi Page in the frontend editor.
		$I->createDiviPageInFrontendEditor($I, 'Kit: Page: Form Trigger: Divi: Frontend: No Credentials', false);

		// Insert the Form module.
		$I->insertDiviRowWithModule(
			$I,
			'Kit Form Trigger',
			'convertkit_formtrigger'
		);

		// Confirm the on screen message displays.
		$I->seeInSource('Not connected to ConvertKit');
		$I->seeInSource('Connect your ConvertKit account at Settings > Kit, and then refresh this page to select a form.');
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

		// Create a Divi Page in the frontend editor.
		$I->createDiviPageInFrontendEditor($I, 'Kit: Page: Form Trigger: Divi: Frontend: No Forms');

		// Insert the Form module.
		$I->insertDiviRowWithModule(
			$I,
			'Kit Form Trigger',
			'convertkit_formtrigger'
		);

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
		$pageID = $I->createPageWithDiviModuleProgrammatically(
			$I,
			'Kit: Legacy Form Trigger: Divi Module: No Form Param',
			'convertkit_formtrigger',
			'form',
			''
		);

		// Load Page.
		$I->amOnPage('?p=' . $pageID);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that no ConvertKit Form trigger button is displayed.
		$I->dontSeeFormTriggerOutput($I);
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
