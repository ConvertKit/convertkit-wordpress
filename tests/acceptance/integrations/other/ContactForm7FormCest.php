<?php
/**
 * Tests for ConvertKit Forms integration with Contact Form 7.
 *
 * @since   1.9.6
 */
class ContactForm7FormCest
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
		$I->activateThirdPartyPlugin($I, 'contact-form-7');
	}

	/**
	 * Tests that no Contact Form 7 settings display and a 'No Forms exist on ConvertKit'
	 * notification displays when no credentials are defined in the Plugin's settings.
	 *
	 * @since   2.2.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testSettingsContactForm7WhenNoAPIKeyAndSecret(AcceptanceTester $I)
	{
		// Load Contact Form 7 Plugin Settings.
		$I->amOnAdminPage('options-general.php?page=_wp_convertkit_settings&tab=contactform7');

		// Confirm no settings table is displayed.
		$I->dontSeeElementInDOM('table.wp-list-table');
	}

	/**
	 * Tests that no Contact Form 7 settings display and a 'No Forms exist on ConvertKit'
	 * notification displays when no Forms exist.
	 *
	 * @since   2.2.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testSettingsContactForm7WhenNoForms(AcceptanceTester $I)
	{
		// Setup Plugin.
		$I->setupConvertKitPluginCredentialsNoData($I);
		$I->setupConvertKitPluginResourcesNoData($I);

		// Load Contact Form 7 Plugin Settings.
		$I->amOnAdminPage('options-general.php?page=_wp_convertkit_settings&tab=contactform7');

		// Confirm notice is displayed.
		$I->see('No Forms exist on ConvertKit.');

		// Confirm no settings table is displayed.
		$I->dontSeeElementInDOM('table.wp-list-table');
	}

	/**
	 * Test that saving a Contact Form 7 to ConvertKit Form Mapping works.
	 *
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testSettingsContactForm7ToConvertKitFormMapping(AcceptanceTester $I)
	{
		$I->markTestIncomplete();

		// Setup ConvertKit Plugin.
		$I->setupConvertKitPlugin($I);
		$I->setupConvertKitPluginResources($I);

		// Create Contact Form 7 Form.
		$contactForm7ID = $this->_createContactForm7Form($I);

		// Load Contact Form 7 Plugin Settings.
		$I->amOnAdminPage('options-general.php?page=_wp_convertkit_settings&tab=contactform7');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check that a Form Mapping option is displayed.
		$I->seeElementInDOM('#_wp_convertkit_integration_contactform7_settings_' . $contactForm7ID);

		// Change Form to value specified in the .env file.
		$I->selectOption('#_wp_convertkit_integration_contactform7_settings_' . $contactForm7ID, $_ENV['CONVERTKIT_API_THIRD_PARTY_INTEGRATIONS_FORM_NAME']);

		$I->click('Save Changes');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check the value of the Form field matches the input provided.
		$I->seeOptionIsSelected('#_wp_convertkit_integration_contactform7_settings_' . $contactForm7ID, $_ENV['CONVERTKIT_API_THIRD_PARTY_INTEGRATIONS_FORM_NAME']);

		// Create Page with Contact Form 7 Shortcode.
		$I->havePageInDatabase(
			[
				'post_title'   => 'ConvertKit: Contact Form 7 Shortcode',
				'post_name'    => 'convertkit-contact-form-7-shortcode',
				'post_content' => 'Form:
[contact-form-7 id="' . $contactForm7ID . '"]',
			]
		);

		// Load the Page on the frontend site.
		$I->amOnPage('/convertkit-contact-form-7-shortcode');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Define email address for this test.
		$emailAddress = $I->generateEmailAddress();

		// Complete Name and Email.
		$I->fillField('input[name=your-name]', 'ConvertKit Name');
		$I->fillField('input[name=your-email]', $emailAddress);
		$I->fillField('input[name=your-subject]', 'ConvertKit Subject');

		// Submit Form.
		$I->click('Submit');

		// Confirm the form submitted without errors.
		$I->performOn(
			'form.sent',
			function($I) {
				$I->see('Thank you for your message. It has been sent.');
			}
		);

		// Confirm that the email address was added to ConvertKit.
		$I->apiCheckSubscriberExists($I, $emailAddress);
	}

	/**
	 * Tests that the 'Enable Creator Network Recommendations' option on a Form's settings
	 * is not displayed when invalid credentials are specified at WPForms > Settings > Integrations > ConvertKit.
	 *
	 * @since   2.2.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testSettingsContactForm7CreatorNetworkRecommendationsOptionWhenDisabledOnConvertKitAccount(AcceptanceTester $I)
	{
		// Setup ConvertKit Plugin.
		$I->setupConvertKitPluginCredentialsNoData($I);
		$I->setupConvertKitPluginResources($I);

		// Create Contact Form 7 Form.
		$contactForm7ID = $this->_createContactForm7Form($I);

		// Load Contact Form 7 Plugin Settings.
		$I->amOnAdminPage('options-general.php?page=_wp_convertkit_settings&tab=contactform7');

		// Confirm a message is displayed telling the user a paid plan is required.
		$I->seeInSource('Creator Network Recommendations requires a <a href="https://app.convertkit.com/account_settings/billing/?utm_source=wordpress&amp;utm_term=en_US&amp;utm_content=convertkit" target="_blank">paid ConvertKit Plan</a>');

		// Create Page with Contact Form 7 Shortcode.
		$pageID = $I->havePageInDatabase(
			[
				'post_title'   => 'ConvertKit: Contact Form 7: Creator Network Recommendations Disabled on ConvertKit',
				'post_name'    => 'convertkit-contact-form-7-creator-network-recommendations-disabled-convertkit',
				'post_content' => 'Form:
[contact-form-7 id="' . $contactForm7ID . '"]',
			]
		);

		// Confirm the recommendations script was not loaded, as the credentials are invalid.
		$I->dontSeeCreatorNetworkRecommendationsScript($I, $pageID);
	}

	/**
	 * Tests that the 'Enable Creator Network Recommendations' option on a Form's settings
	 * is displayed and saves correctly when valid credentials are specified at WPForms > Settings > Integrations > ConvertKit,
	 * and the ConvertKit account has the Creator Network enabled.  Viewing and submitting the Form then correctly
	 * displays the Creator Network Recommendations modal.
	 *
	 * @since   2.2.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testSettingsContactForm7CreatorNetworkRecommendationsWhenEnabledOnConvertKitAccount(AcceptanceTester $I)
	{
		// Setup ConvertKit Plugin.
		$I->setupConvertKitPlugin($I);
		$I->setupConvertKitPluginResources($I);

		// Create Contact Form 7 Form.
		$contactForm7ID = $this->_createContactForm7Form($I);

		// Load Contact Form 7 Plugin Settings.
		$I->amOnAdminPage('options-general.php?page=_wp_convertkit_settings&tab=contactform7');

		// Enable Creator Network Recommendations on the Contact Form 7.
		$I->checkOption('#creator_network_recommendations_' . $contactForm7ID);

		// Save.
		$I->click('Save Changes');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm checkbox is checked after saving.
		$I->seeCheckboxIsChecked('#creator_network_recommendations_' . $contactForm7ID);

		// Create Page with Contact Form 7 Shortcode.
		$pageID = $I->havePageInDatabase(
			[
				'post_title'   => 'ConvertKit: Contact Form 7: Creator Network Recommendations',
				'post_name'    => 'convertkit-contact-form-7-creator-network-recommendations',
				'post_content' => 'Form:
[contact-form-7 id="' . $contactForm7ID . '"]',
			]
		);

		// Confirm the recommendations script was loaded.
		$I->seeCreatorNetworkRecommendationsScript($I, $pageID);

		// Define email address for this test.
		$emailAddress = $I->generateEmailAddress();

		// Complete Name and Email.
		$I->fillField('input[name=your-name]', 'ConvertKit Name');
		$I->fillField('input[name=your-email]', $emailAddress);
		$I->fillField('input[name=your-subject]', 'ConvertKit Subject');

		// Submit Form.
		$I->click('Submit');

		// Confirm the form submitted without errors.
		$I->performOn(
			'form.sent',
			function($I) {
				$I->see('Thank you for your message. It has been sent.');
			}
		);

		// Wait for Creator Network Recommendations modal to display.
		$I->waitForElementVisible('.formkit-modal');
		$I->switchToIFrame('.formkit-modal iframe');
		$I->waitForElementVisible('div[data-component="Page"]');
	}

	/**
	 * Creates a Contact Form 7 Form
	 *
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I  Tester.
	 * @return  int                     Form ID
	 */
	private function _createContactForm7Form(AcceptanceTester $I)
	{
		return $I->havePostInDatabase(
			[
				'post_name'   => 'contact-form-7-form',
				'post_title'  => 'Contact Form 7 Form',
				'post_type'   => 'wpcf7_contact_form',
				'post_status' => 'publish',
				'meta_input'  => [
					// Don't attempt to send mail, as this will fail when run through a GitHub Action.
					// @see https://contactform7.com/additional-settings/#skipping-mail.
					'_form'                => '[text* your-name] [email* your-email] [text* your-subject] [textarea your-message] [submit "Submit"]',
					'_additional_settings' => 'skip_mail: on',
				],
			]
		);
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
		$I->deactivateConvertKitPlugin($I);
		$I->resetConvertKitPlugin($I);

		// We don't use deactivateThirdPartyPlugin(), as this checks for PHP warnings/errors.
		// Contact Form 7 throws a warning on deactivation related to WordPress capabilities,
		// which is outside of our control and would result in the test not completing.
		$I->amOnPluginsPage();

		// Deactivate the Plugin.
		$I->deactivatePlugin('contact-form-7');
	}
}
