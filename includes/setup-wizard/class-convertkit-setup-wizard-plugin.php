<?php
/**
 * ConvertKit Setup Wizard Plugin class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Handles previewing a supplied Form ID in the URL when viewing a Page or Post,
 * when the user clicks a preview link in the Plugin Setup Wizard.
 *
 * @package ConvertKit
 * @author ConvertKit
 */
class ConvertKit_Setup_Wizard_Plugin {

	/**
	 * Registers action and filter hooks.
	 *
	 * @since   1.9.8.5
	 */
	public function __construct() {

		add_filter( 'convertkit_output_append_form_to_content_form_id', array( $this, 'preview_form' ), 99999 );

	}

	/**
	 * Changes the form to display for the given Post ID if the request is
	 * from a logged in user who has clicked a preview link in the Plugin Setup Wizard.
	 *
	 * @since   1.9.8.5
	 *
	 * @param   int $form_id    ConvertKit Form ID.
	 * @return  int                 ConvertKit Form ID
	 */
	public function preview_form( $form_id ) {

		// Bail if the user isn't logged in.
		if ( ! is_user_logged_in() ) {
			return $form_id;
		}

		// Bail if no nonce field exists.
		if ( ! isset( $_REQUEST['convertkit-preview-nonce'] ) ) {
			return $form_id;
		}

		// Bail if the nonce verification fails.
		if ( ! wp_verify_nonce( sanitize_key( wp_unslash( $_REQUEST['convertkit-preview-nonce'] ) ), 'convertkit-preview-form' ) ) {
			return $form_id;
		}

		// Determine the form to preview.
		$preview_form_id = (int) ( isset( $_REQUEST['convertkit_form_id'] ) ? sanitize_text_field( $_REQUEST['convertkit_form_id'] ) : 0 );

		// Return.
		return $preview_form_id;

	}

}
