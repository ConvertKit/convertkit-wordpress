<?php
/**
 * Tests for ConvertKit Forms integration with Forminator.
 *
 * @since   2.3.0
 */
class ForminatorCest
{
	/**
	 * Run common actions before running the test functions in this class.
	 *
	 * @since   2.3.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _before(AcceptanceTester $I)
	{
		$I->activateConvertKitPlugin($I);
		$I->activateThirdPartyPlugin($I, 'forminator');
	}

	/**
	 * Tests that no Forminator settings display and a 'No Forms exist on ConvertKit'
	 * notification displays when no API Key and Secret are defined in the Plugin's settings.
	 *
	 * @since   2.3.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testSettingsForminatorWhenNoAPIKeyAndSecret(AcceptanceTester $I)
	{
		// Load Forminator Plugin Settings.
		$I->amOnAdminPage('options-general.php?page=_wp_convertkit_settings&tab=forminator');

		// Confirm notice is displayed.
		$I->see('No Forms exist on ConvertKit.');

		// Confirm no settings table is displayed.
		$I->dontSeeElementInDOM('table.wp-list-table');
	}

	/**
	 * Tests that no Forminator settings display and a 'No Forms exist on ConvertKit'
	 * notification displays when no Forms exist.
	 *
	 * @since   2.3.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testSettingsForminatorWhenNoForms(AcceptanceTester $I)
	{
		// Setup Plugin.
		$I->setupConvertKitPlugin($I, $_ENV['CONVERTKIT_API_KEY_NO_DATA'], $_ENV['CONVERTKIT_API_SECRET_NO_DATA'], '', '', '');
		$I->setupConvertKitPluginResourcesNoData($I);

		// Load Forminator Plugin Settings.
		$I->amOnAdminPage('options-general.php?page=_wp_convertkit_settings&tab=forminator');

		// Confirm notice is displayed.
		$I->see('No Forms exist on ConvertKit.');

		// Confirm no settings table is displayed.
		$I->dontSeeElementInDOM('table.wp-list-table');
	}

	/**
	 * Test that saving a Forminator to ConvertKit Form Mapping works.
	 *
	 * @since   2.3.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testSettingsForminatorToConvertKitFormMapping(AcceptanceTester $I)
	{
		// Setup ConvertKit Plugin.
		$I->setupConvertKitPlugin($I);
		$I->setupConvertKitPluginResources($I);

		// Create Forminator Form.
		$forminatorFormID = $this->_createForminatorForm($I);

		// Load Forminator Plugin Settings.
		$I->amOnAdminPage('options-general.php?page=_wp_convertkit_settings&tab=forminator');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check that a Form Mapping option is displayed.
		$I->seeElementInDOM('#_wp_convertkit_integration_forminator_settings_' . $forminatorFormID);

		// Change Form to value specified in the .env file.
		$I->selectOption('#_wp_convertkit_integration_forminator_settings_' . $forminatorFormID, $_ENV['CONVERTKIT_API_THIRD_PARTY_INTEGRATIONS_FORM_NAME']);

		$I->click('Save Changes');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check the value of the Form field matches the input provided.
		$I->seeOptionIsSelected('#_wp_convertkit_integration_forminator_settings_' . $forminatorFormID, $_ENV['CONVERTKIT_API_THIRD_PARTY_INTEGRATIONS_FORM_NAME']);

		// Create Page with Forminator Shortcode.
		$I->havePageInDatabase(
			[
				'post_title'   => 'ConvertKit: Forminator Shortcode',
				'post_name'    => 'convertkit-forminator-form-shortcode',
				'post_content' => 'Form:
[forminator_form id="' . $forminatorFormID . '"]',
			]
		);

		// Load the Page on the frontend site.
		$I->amOnPage('/convertkit-forminator-form-shortcode');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Define email address for this test.
		$emailAddress = $I->generateEmailAddress();

		// Complete Name and Email.
		$I->fillField('input[name=name-1]', 'ConvertKit Name');
		$I->fillField('input[name=email-1]', $emailAddress);

		// Submit Form.
		$I->click('button.forminator-button-submit');

		// Wait for response message.
		$I->waitForElementVisible('.forminator-response-message');

		// Confirm the form submitted without errors.
		$I->performOn(
			'.forminator-response-message',
			function($I) {
				$I->see('Form entry saved');
			}
		);

		// Confirm that the email address was added to ConvertKit.
		$I->apiCheckSubscriberExists($I, $emailAddress);
	}

	/**
	 * Creates a Forminator Form
	 *
	 * @since   2.3.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 * @return  int                     Form ID
	 */
	private function _createForminatorForm(AcceptanceTester $I)
	{
		return $I->havePostInDatabase(
			[
				'post_name'   => 'forminator-form',
				'post_title'  => 'Forminator Form',
				'post_type'   => 'forminator_forms',
				'post_status' => 'publish',
				'meta_input'  => [
					'forminator_form_meta' => [
						'fields' => [
							[
								'id'          => 'name-1',
								'element_id'  => 'name-1',
								'type'        => 'name',
								'field_label' => 'Name',
							],
							[
								'id'          => 'email-1',
								'element_id'  => 'email-1',
								'type'        => 'email',
								'field_label' => 'email',
							],
						],
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
	 * @since   2.3.0.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _passed(AcceptanceTester $I)
	{
		$I->deactivateConvertKitPlugin($I);
		$I->deactivateThirdPartyPlugin($I, 'forminator');
		$I->resetConvertKitPlugin($I);
	}
}
