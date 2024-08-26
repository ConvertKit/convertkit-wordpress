<?php
/**
 * Tests for the ConvertKit Form's Divi Module.
 *
 * @since   2.5.6
 */
class ElementorFormCest
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

		// Setup Plugin, without defining default Forms.
		$I->setupConvertKitPluginNoDefaultForms($I);
		$I->setupConvertKitPluginResources($I);
	}

	/**
	 * 
	 */
	public function testFormModuleInBackendEditor(AcceptanceTester $I)
	{
		// Activate Classic Editor Plugin.
		$I->activateThirdPartyPlugin($I, 'classic-editor');

		// Add a Page using the Classic Editor.
		$I->addClassicEditorPage($I, 'page', 'ConvertKit: Page: Form: Divi: Backend Editor');

		// Publish.
		$I->publishClassicEditorPage($I);

		// Click Divi Builder button.
		$I->click('#et_pb_toggle_builder');

		// Close tutorial modal.
		$I->waitForElementVisible('.et-fb-tooltip-modal');
		$I->click('Start Building');

		// Click 'Build from scratch'.
		$I->waitForElementVisible('.et-fb-page-creation-card-content');
		$I->click('Start Building', '.et-fb-page-creation-card-content');

		// Insert row.
		$I->waitForElementVisible('li[data-layout="4_4"]');
		$I->click('li[data-layout="4_4"]');

		// Search for module.
		$I->waitForElementVisible('input[name="filterByTitle"]');
		$I->fillField('filterByTitle', 'ConvertKit Form');

		// Insert module.
		$I->waitForElementVisible('li.convertkit_form');
		$I->click('li.convertkit_form');

		// Select Form.
		$I->waitForElementVisible('#et-fb-form');
		$I->click('#et-fb-form');
		$I->click('li[data-value="' . $_ENV['CONVERTKIT_API_FORM_ID'] . '"]', '#et-fb-form');

		// Save module.
		$I->click('button[data-tip="Save Changes"]');

		// Update page.
		$I->click('Update');

		// Load the Page on the frontend site.
		$I->click('.notice-success a');

		// Wait for frontend web site to load.
		$I->waitForElementVisible('body');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that one ConvertKit Form is output in the DOM.
		// This confirms that there is only one script on the page for this form, which renders the form.
		$I->seeNumberOfElementsInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_ID'] . '"]', 1);
	}

	public function testFormModuleInFrontendEditor(AcceptanceTester $I)
	{

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
				'post_title'  => $title,
				'post_type'   => 'page',
				'post_status' => 'publish',
				'meta_input'  => [
					// Elementor.
					'_elementor_data'          => [
						0 => [
							'id'       => '39bb59d',
							'elType'   => 'section',
							'settings' => [],
							'elements' => [
								[
									'id'       => 'b7e0e57',
									'elType'   => 'column',
									'settings' => [
										'_column_size' => 100,
										'_inline_size' => null,
									],
									'elements' => [
										[
											'id'         => 'a73a905',
											'elType'     => 'widget',
											'settings'   => [
												'form' => (string) $formID,
											],
											'widgetType' => 'convertkit-elementor-form',
										],
									],
								],
							],
						],
					],
					'_elementor_version'       => '3.6.1',
					'_elementor_edit_mode'     => 'builder',
					'_elementor_template_type' => 'wp-page',

					// Configure ConvertKit Plugin to not display a default Form,
					// as we are testing for the Form in Elementor.
					'_wp_convertkit_post_meta' => [
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
