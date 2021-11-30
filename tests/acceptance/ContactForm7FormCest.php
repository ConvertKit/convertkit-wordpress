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
		$I->setupConvertKitPlugin($I);

		// Go to the Plugins screen in the WordPress Administration interface.
		$I->amOnPluginsPage();

		// Activate the Contact Form 7 Plugin.
		$I->activatePlugin('contact-form-7');

		// Check that the Plugin activated successfully.
		$I->seePluginActivated('contact-form-7');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);
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
		// Get Contact Form 7 Form ID.
		$contactForm7ID = $this->_getContactForm7ID($I);

		// Load Contact Form 7 Plugin Settings
		$I->amOnAdminPage('options-general.php?page=_wp_convertkit_settings&tab=contactform7');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check that a Form Mapping option is displayed.
		$I->seeElementInDOM('#_wp_convertkit_integration_contactform7_settings_' . $contactForm7ID);

		// Change Form to value specified in the .env file.
		$I->selectOption('#_wp_convertkit_integration_contactform7_settings_' . $contactForm7ID, $_ENV['CONVERTKIT_API_FORM_NAME']);

		$I->click('Save Changes');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check the value of the Form field matches the input provided.
		$I->seeOptionIsSelected('#_wp_convertkit_integration_contactform7_settings_' . $contactForm7ID, $_ENV['CONVERTKIT_API_FORM_NAME']);
		
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

		// Complete Name and Email
		$I->fillField('input[name=your-name]', 'ConvertKit Name');
		$I->fillField('input[name=your-email]', $_ENV['CONVERTKIT_API_SUBSCRIBER_EMAIL']);
		$I->fillField('input[name=your-subject]', 'ConvertKit Subject');

		// Submit Form.
		$I->click('Submit');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);
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
		$contactForm7ID = $I->havePostInDatabase([
			'post_name' 	=> 'contact-form-7-form',
			'post_title'	=> 'Contact Form 7 Form',
			'post_content'	=> '[text* your-name] [email* your-email] [text* your-subject] [textarea your-message] [submit "Submit"]',
			'post_type'		=> 'wpcf7_contact_form',
			'post_status'	=> 'publish',
			'meta_input' => [
				// Don't attempt to send mail, as this will fail when run through a GitHub Action.
				// @see https://contactform7.com/additional-settings/#skipping-mail
				'_additional_settings' => 'skip_mail: on',
			],
		]);

		// meta_input doesn't always work, so we have to manually apply the skip_mail setting.
		$I->amOnAdminPage('/admin.php?page=wpcf7&post=' . $contactForm7ID . '&active-tab=3');
		$I->fillField('textarea#wpcf7-additional-settings', 'skip_mail: on');
		$I->click('Save');

		// Confirm settings saved.
		$I->seeInField('textarea#wpcf7-additional-settings', 'skip_mail: on');
		
		return $contactForm7ID;
	}

	/**
	 * Gets the first(default) Contact Form 7 Form
	 * 
	 * @since 	1.9.6
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 * @return 	int 					Form ID
	 */
	private function _getContactForm7ID(AcceptanceTester $I)
	{
		$result = $I->grabAllFromDatabase($I->grabPrefixedTableNameFor('posts'), 'ID', [
			'post_type' => 'wpcf7_contact_form',
		]);

		return $result[0]['ID'];
	}
}