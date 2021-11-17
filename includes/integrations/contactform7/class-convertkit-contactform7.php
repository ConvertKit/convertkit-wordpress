<?php
/**
 * ConvertKit Contact Form 7 class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Contact Form 7 Integration
 *
 * @package ConvertKit
 * @author ConvertKit
 */
class ConvertKit_ContactForm7 {

	/**
	 * Constructor. Registers required hooks with Contact Form 7.
	 *
	 * @since   1.9.6
	 */
	public function __construct() {

		add_action( 'wpcf7_submit', array( $this, 'handle_wpcf7_submit' ), 10, 2 );

	}

	/**
	 * Sends a Contact Form 7's Form Name and Email values through the ConvertKit API
	 * if a ConvertKit Form is mapped to this Contact Form 7 Form in the ConvertKit
	 * Settings.
	 *
	 * @since   1.9.6
	 *
	 * @param   WPCF7_ContactForm $contact_form   Contact Form 7 Form.
	 * @param   array             $result         Submission Result.
	 */
	public function handle_wpcf7_submit( $contact_form, $result ) {

		// If Demo Mode is enabled on the Contact Form 7 Form, don't send anything to ConvertKit.
		// @see https://contactform7.com/additional-settings/.
		if ( isset( $result['demo_mode'] ) ) {
			return;
		}

		// If the form submission failed, don't send anything to ConvertKit.
		if ( $result['status'] !== 'mail_sent' ) {
			return;
		}

		// Get ConvertKit Form ID mapped to this Contact Form 7 Form.
		$contact_form_7_settings = new ConvertKit_ContactForm7_Settings();
		$convertkit_form_id      = $contact_form_7_settings->get_convertkit_form_id_by_cf7_form_id( $contact_form->id() );

		// If no ConvertKit Form is mapped to this Contact Form 7 Form, bail.
		if ( ! $convertkit_form_id ) {
			return;
		}

		// Bail if the API hasn't been configured.
		$settings = new ConvertKit_Settings();
		if ( ! $settings->has_api_key_and_secret() ) {
			return;
		}

		// Get Contact Form 7 Submission.
		$form_data = WPCF7_Submission::get_instance()->get_posted_data();

		// Bail if the expected email form field does not exist.
		if ( ! isset( $form_data['your-email'] ) || empty( $form_data['your-email'] ) ) {
			return;
		}

		// Get email and first name.
		$email      = $form_data['your-email'];
		$first_name = '';
		if ( isset( $form_data['your-name'] ) && ! empty( $form_data['your-name'] ) ) {
			$name       = explode( ' ', $form_data['your-name'] );
			$first_name = $name[0];
		}

		// If here, subscribe the user to the ConvertKit Form.
		// Initialize the API.
		$api = new ConvertKit_API( $settings->get_api_key(), $settings->get_api_secret(), $settings->debug_enabled() );

		// Send request.
		$api->form_subscribe( $convertkit_form_id, $email, $first_name );

	}

}

// Bootstrap.
add_action(
	'convertkit_initialize_global',
	function() {

		new ConvertKit_ContactForm7();

	}
);
