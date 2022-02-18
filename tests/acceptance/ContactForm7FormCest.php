<?php
/**
 * Tests for ConvertKit Forms integration with Contact Form 7.
 * 
 * @since 	1.9.6
 */
class ContactForm7FormCest
{
	/**
	 * Run common actions before running the test functions in this class.
	 * 
	 * @since 	1.9.6
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function _before(AcceptanceTester $I)
	{
		$I->activateConvertKitPlugin($I);
		$I->activateThirdPartyPlugin($I, 'contact-form-7');
		$I->setupConvertKitPlugin($I);
		$I->enableDebugLog($I);
	}

	/**
	 * Test that saving a Contact Form 7 to ConvertKit Form Mapping works.
	 * 
	 * @since 	1.9.6
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testSettingsContactForm7ToConvertKitFormMapping(AcceptanceTester $I)
	{
		// Create Contact Form 7 Form.
		$contactForm7ID = $this->_createContactForm7Form($I);

		// Load Contact Form 7 Plugin Settings
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
		$I->havePageInDatabase([
			'post_title'	=> 'ConvertKit: Contact Form 7 Shortcode',
			'post_name' 	=> 'convertkit-contact-form-7-shortcode',
			'post_content' 	=> 'Form:
[contact-form-7 id="' . $contactForm7ID . '"]',
		]);

		// Load the Page on the frontend site.
		$I->amOnPage('/convertkit-contact-form-7-shortcode');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Define email address for this test.
		$emailAddress = $I->generateEmailAddress();

		// Complete Name and Email
		$I->fillField('input[name=your-name]', 'ConvertKit Name');
		$I->fillField('input[name=your-email]', $emailAddress);
		$I->fillField('input[name=your-subject]', 'ConvertKit Subject');

		// Submit Form.
		$I->click('Submit');

		// Confirm the form submitted without errors.
		$I->performOn('form.sent', function($I) {
			$I->seeInSource('Thank you for your message. It has been sent.');
		});

		// Confirm that the email address was added to ConvertKit.
		$I->apiCheckSubscriberExists($I, $emailAddress);
	}

	/**
	 * Creates a Contact Form 7 Form
	 * 
	 * @since 	1.9.6
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 * @return 	int 					Form ID
	 */
	private function _createContactForm7Form(AcceptanceTester $I)
	{
		return $I->havePostInDatabase([
			'post_name' 	=> 'contact-form-7-form',
			'post_title'	=> 'Contact Form 7 Form',
			'post_type'		=> 'wpcf7_contact_form',
			'post_status'	=> 'publish',
			'meta_input' => [
				// Don't attempt to send mail, as this will fail when run through a GitHub Action.
				// @see https://contactform7.com/additional-settings/#skipping-mail
				'_form' => '[text* your-name] [email* your-email] [text* your-subject] [textarea your-message] [submit "Submit"]',
				'_additional_settings' => 'skip_mail: on',
			],
		]);
	}

	/**
	 * Deactivate and reset Plugin(s) after each test, if the test passes.
	 * We don't use _after, as this would provide a screenshot of the Plugin
	 * deactivation and not the true test error.
	 * 
	 * @since 	1.9.6.7
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function _passed(AcceptanceTester $I)
	{
		$I->deactivateConvertKitPlugin($I);
		$I->deactivateThirdPartyPlugin($I, 'contact-form-7');
		$I->resetConvertKitPlugin($I);
	}
}