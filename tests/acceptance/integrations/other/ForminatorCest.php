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
	 * notification displays when no credentials are defined in the Plugin's settings.
	 *
	 * @since   2.3.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testSettingsForminatorWhenNoCredentials(AcceptanceTester $I)
	{
		// Load Forminator Plugin Settings.
		$I->amOnAdminPage('options-general.php?page=_wp_convertkit_settings&tab=forminator');

		// Confirm no settings table is displayed.
		$I->dontSeeElementInDOM('table.wp-list-table');
	}

	/**
	 * Test that saving a Forminator Form to ConvertKit Form Mapping works.
	 *
	 * @since   2.3.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testSettingsForminatorFormToConvertKitFormMapping(AcceptanceTester $I)
	{
		// Setup ConvertKit Plugin.
		$I->setupConvertKitPluginNoDefaultForms($I);
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
	 * Test that saving a Forminator Form to ConvertKit Legacy Form Mapping works.
	 *
	 * @since   2.5.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testSettingsForminatorFormToConvertKitLegacyFormMapping(AcceptanceTester $I)
	{
		// Setup ConvertKit Plugin.
		$I->setupConvertKitPluginNoDefaultForms($I);
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
		$I->selectOption('#_wp_convertkit_integration_forminator_settings_' . $forminatorFormID, $_ENV['CONVERTKIT_API_LEGACY_FORM_NAME']);

		$I->click('Save Changes');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check the value of the Form field matches the input provided.
		$I->seeOptionIsSelected('#_wp_convertkit_integration_forminator_settings_' . $forminatorFormID, $_ENV['CONVERTKIT_API_LEGACY_FORM_NAME']);

		// Create Page with Forminator Shortcode.
		$I->havePageInDatabase(
			[
				'post_title'   => 'ConvertKit: Forminator Shortcode: Legacy Form',
				'post_name'    => 'convertkit-forminator-form-shortcode-legacy-form',
				'post_content' => 'Form:
[forminator_form id="' . $forminatorFormID . '"]',
			]
		);

		// Load the Page on the frontend site.
		$I->amOnPage('/convertkit-forminator-form-shortcode-legacy-form');

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
	 * Test that saving a Forminator Form to ConvertKit Tag Mapping works.
	 *
	 * @since   2.5.2
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testSettingsForminatorFormToConvertKitTagMapping(AcceptanceTester $I)
	{
	}

	/**
	 * Test that saving a Forminator Form to ConvertKit Sequence Mapping works.
	 *
	 * @since   2.5.2
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testSettingsForminatorFormToConvertKitSequenceMapping(AcceptanceTester $I)
	{
	}

	/**
	 * Test that setting a Forminator Form Form to the '(Do not subscribe)' option works.
	 *
	 * @since   2.5.2
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testSettingsForminatorFormDoNotSubscribeOption(AcceptanceTester $I)
	{
	}

	/**
	 * Test that setting a Forminator Form to the 'Subscribe' option works.
	 *
	 * @since   2.5.2
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testSettingsForminatorFormSubscribeOption(AcceptanceTester $I)
	{
	}

	/**
	 * Test that saving a Forminator Quiz to ConvertKit Form Mapping works.
	 *
	 * @since   2.4.3
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testSettingsForminatorQuizToConvertKitFormMapping(AcceptanceTester $I)
	{
		// Setup ConvertKit Plugin.
		$I->setupConvertKitPluginNoDefaultForms($I);
		$I->setupConvertKitPluginResources($I);

		// Create Forminator Quiz.
		$forminatorFormID = $this->_createForminatorQuiz($I);

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
				'post_title'   => 'ConvertKit: Forminator Quiz Shortcode',
				'post_name'    => 'convertkit-forminator-quiz-shortcode',
				'post_content' => 'Form:
[forminator_quiz id="' . $forminatorFormID . '"]',
			]
		);

		// Load the Page on the frontend site.
		$I->amOnPage('/convertkit-forminator-quiz-shortcode');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Define email address for this test.
		$emailAddress = $I->generateEmailAddress();

		// Complete quiz.
		$I->checkOption('answers[question-1-0]', '0');
		$I->click('View Results');

		// Complete Name and Email.
		$I->waitForElementVisible('input[name=name-1]');
		$I->fillField('input[name=name-1]', 'ConvertKit Name');
		$I->fillField('input[name=email-1]', $emailAddress);

		// Submit Form.
		$I->click('Submit');

		// Wait for submission to complete.
		$I->waitForElementVisible('.forminator-icon-check');

		// Confirm that the email address was added to ConvertKit.
		$I->apiCheckSubscriberExists($I, $emailAddress);
	}

	/**
	 * Test that saving a Forminator Quiz to ConvertKit Tag Mapping works.
	 *
	 * @since   2.5.2
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testSettingsForminatorQuizToConvertKitTagMapping(AcceptanceTester $I)
	{
	}

	/**
	 * Test that saving a Forminator Quiz to ConvertKit Sequence Mapping works.
	 *
	 * @since   2.5.2
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testSettingsForminatorQuizToConvertKitSequenceMapping(AcceptanceTester $I)
	{
	}

	/**
	 * Test that setting a Forminator Quiz Form to the '(Do not subscribe)' option works.
	 *
	 * @since   2.5.2
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testSettingsForminatorQuizDoNotSubscribeOption(AcceptanceTester $I)
	{
	}

	/**
	 * Test that setting a Forminator Quiz to the 'Subscribe' option works.
	 *
	 * @since   2.5.2
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testSettingsForminatorQuizSubscribeOption(AcceptanceTester $I)
	{
	}

	/**
	 * Tests that the 'Enable Creator Network Recommendations' is not displayed when connected
	 * to a ConvertKit account that does not have Creator Network Recommendations enabled.
	 *
	 * @since   2.3.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testSettingsForminatorCreatorNetworkRecommendationsWhenDisabledOnConvertKitAccount(AcceptanceTester $I)
	{
		// Setup ConvertKit Plugin.
		$I->setupConvertKitPluginCredentialsNoData($I);
		$I->setupConvertKitPluginResources($I);

		// Create Forminator Form.
		$forminatorFormID = $this->_createForminatorForm($I);

		// Load Forminator Plugin Settings.
		$I->amOnAdminPage('options-general.php?page=_wp_convertkit_settings&tab=forminator');

		// Confirm a message is displayed telling the user a paid plan is required.
		$I->seeInSource('Creator Network Recommendations requires a <a href="https://app.convertkit.com/account_settings/billing/?utm_source=wordpress&amp;utm_term=en_US&amp;utm_content=convertkit" target="_blank">paid ConvertKit Plan</a>');

		// Create Page with Forminator Shortcode.
		$pageID = $I->havePageInDatabase(
			[
				'post_title'   => 'ConvertKit: Forminator: Creator Network Recommendations Disabled on ConvertKit',
				'post_name'    => 'convertkit-forminator-creator-network-recommendations-disabled-convertkit',
				'post_content' => 'Form:
[forminator_form id="' . $forminatorFormID . '"]',
			]
		);

		// Confirm the recommendations script was not loaded, as the credentials are invalid.
		$I->dontSeeCreatorNetworkRecommendationsScript($I, $pageID);
	}

	/**
	 * Tests that the 'Enable Creator Network Recommendations' option is displayed and saves correctly when
	 * valid credentials are specified, and the ConvertKit account has the Creator Network enabled.
	 * Viewing and submitting the Form then correctly displays the Creator Network Recommendations modal.
	 *
	 * @since   2.3.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testSettingsForminatorCreatorNetworkRecommendationsWhenEnabledOnConvertKitAccount(AcceptanceTester $I)
	{
		// Setup ConvertKit Plugin.
		$I->setupConvertKitPluginNoDefaultForms($I);
		$I->setupConvertKitPluginResources($I);

		// Create Forminator Form.
		$forminatorFormID = $this->_createForminatorForm($I);

		// Load Forminator Plugin Settings.
		$I->amOnAdminPage('options-general.php?page=_wp_convertkit_settings&tab=forminator');

		// Enable Creator Network Recommendations on the Forminator Form.
		$I->checkOption('#creator_network_recommendations_' . $forminatorFormID);

		// Save.
		$I->click('Save Changes');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm checkbox is checked after saving.
		$I->seeCheckboxIsChecked('#creator_network_recommendations_' . $forminatorFormID);

		// Create Page with Forminator Shortcode.
		$pageID = $I->havePageInDatabase(
			[
				'post_title'   => 'ConvertKit: Forminator: Creator Network Recommendations',
				'post_name'    => 'convertkit-forminator-creator-network-recommendations',
				'post_content' => 'Form:
[forminator_form id="' . $forminatorFormID . '"]',
			]
		);

		// Confirm the recommendations script was loaded.
		$I->seeCreatorNetworkRecommendationsScript($I, $pageID);

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

		// Wait for Creator Network Recommendations modal to display.
		$I->waitForElementVisible('.formkit-modal');
		$I->switchToIFrame('.formkit-modal iframe');
		$I->waitForElementVisible('div[data-component="Page"]');
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
						'fields'   => [
							[
								'id'          => 'name-1',
								'element_id'  => 'name-1',
								'type'        => 'name',
								'required'    => 'true',
								'field_label' => 'First Name',
							],
							[
								'id'          => 'email-1',
								'element_id'  => 'email-1',
								'type'        => 'email',
								'required'    => 'true',
								'field_label' => 'Email Address',
							],
						],
						'settings' => [
							'enable-ajax' => 'true',
						],
					],
				],
			]
		);
	}

	/**
	 * Creates a Forminator Quiz
	 *
	 * @since   2.4.3
	 *
	 * @param   AcceptanceTester $I  Tester.
	 * @return  int                     Form ID
	 */
	private function _createForminatorQuiz(AcceptanceTester $I)
	{
		// Create Form for Leads.
		$formLeadsID = $I->havePostInDatabase(
			[
				'post_name'   => 'forminator-form-leads',
				'post_title'  => 'Forminator Form Leads',
				'post_type'   => 'forminator_forms',
				'post_status' => 'publish',
				'meta_input'  => [
					'forminator_form_meta' => [
						'fields'   => [
							[
								'id'          => 'name-1',
								'element_id'  => 'name-1',
								'type'        => 'name',
								'required'    => 'true',
								'field_label' => 'First Name',
							],
							[
								'id'          => 'email-1',
								'element_id'  => 'email-1',
								'type'        => 'email',
								'required'    => 'true',
								'field_label' => 'Email Address',
							],
						],
						'settings' => [
							'form-type'            => 'leads',
							'submission-behaviour' => 'behaviour-thankyou',
							'thankyou-message'     => 'Thank you for contacting us, we will be in touch shortly.',
							'submitData'           => [
								'custom-submit-text' => 'Submit',
								'custom-invalid-form-message' => 'Error: Your form is not valid, please fix the errors!',
							],
							'enable-ajax'          => 'true',
							'validation-inline'    => true,
							'formName'             => 'Forminator Quiz - Leads Form',
						],
					],
				],
			]
		);

		// Create and return Quiz Form.
		return $I->havePostInDatabase(
			[
				'post_name'   => 'forminator-quiz',
				'post_title'  => 'Forminator Quiz',
				'post_type'   => 'forminator_quizzes',
				'post_status' => 'publish',
				'meta_input'  => [
					'forminator_form_meta' => [
						'fields'    => [],
						'settings'  => [
							'hasLeads'          => '1',
							'formName'          => 'Forminator Quiz',
							'results_behav'     => 'end',
							'leadsId'           => $formLeadsID,
							'store_submissions' => '1',
							'quiz_title'        => 'Forminator Quiz',
							'wrappers'          => [
								[
									'wrapper_id' => '1',
									'fields'     => [
										[
											'id'          => 'name-1',
											'element_id'  => 'name-1',
											'type'        => 'name',
											'required'    => 'true',
											'field_label' => 'First Name',
										],
										[
											'id'          => 'email-1',
											'element_id'  => 'email-1',
											'type'        => 'email',
											'required'    => 'true',
											'field_label' => 'Email Address',
										],
									],
								],
							],
							'lead_settings'     => [
								'form-type'            => 'leads',
								'submission-behaviour' => 'behaviour-thankyou',
								'thankyou-message'     => 'Thank you for contacting us, we will be in touch shortly.',
								'submitData'           => [
									'custom-submit-text' => 'Submit',
									'custom-invalid-form-message' => 'Error: Your form is not valid, please fix the errors!',
								],
								'enable-ajax'          => 'true',
								'validation-inline'    => '1',
								'formName'             => 'Forminator Quiz - Leads form',
								'form_id'              => $formLeadsID,
								'store_submissions'    => '1',
							],
							'quiz_name'         => 'Forminator Quiz',
							'form-placement'    => 'end',
						],
						'questions' => [
							[
								'slug'    => 'question-1',
								'answers' => [
									[
										'title'  => 'Correct Answer',
										'toggle' => '1',
									],
									[
										'title' => 'Incorrect Answer',
									],
								],
								'type'    => 'knowledge',
								'title'   => 'Question',
							],
						],
						'quiz_type' => 'knowledge',
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
	 * @since   2.3.0
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
