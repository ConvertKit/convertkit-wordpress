<?php
/**
 * ConvertKit Admin Category class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Registers fields on Categories and saves its settings when the Category
 * is saved in the WordPress Administration interface.
 *
 * @package ConvertKit
 * @author ConvertKit
 */
class ConvertKit_Admin_Category {

	/**
	 * Registers action and filter hooks.
	 *
	 * @since   1.9.6
	 */
	public function __construct() {

		add_action( 'category_edit_form_fields', array( $this, 'category_form_fields' ), 20 );
		add_action( 'edited_category', array( $this, 'save_category_fields' ), 20 );

	}

	/**
	 * Display the ConvertKit Forms dropdown when editing a Category
	 *
	 * @since   1.9.6
	 *
	 * @param   WP_Term $term   Category.
	 */
	public function category_form_fields( $term ) {

		// Don't show the form fields if the API hasn't been configured.
		$settings = new ConvertKit_Settings();
		if ( ! $settings->has_api_key_and_secret() ) {
			return;
		}

		// Fetch Category Settings and Forms.
		$convertkit_term  = new ConvertKit_Term( $term->term_id );
		$convertkit_forms = new ConvertKit_Resource_Forms();

		// Load metabox view.
		include CONVERTKIT_PLUGIN_PATH . '/views/backend/term/fields.php';

	}

	/**
	 * Save Term Settings.
	 *
	 * @since   1.9.6
	 *
	 * @param   int $term_id    Term ID.
	 */
	public function save_category_fields( $term_id ) {

		// Bail if no nonce field exists.
		if ( ! isset( $_POST['wp-convertkit-save-meta-nonce'] ) ) {
			return;
		}

		// Bail if the nonce verification fails.
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wp-convertkit-save-meta-nonce'] ) ), 'wp-convertkit-save-meta' ) ) {
			return;
		}

		// Bail if no ConvertKit settings were posted.
		if ( ! isset( $_POST['wp-convertkit'] ) ) {
			return;
		}

		// Build metadata.
		$meta = ( isset( $_POST['wp-convertkit']['form'] ) ? intval( $_POST['wp-convertkit']['form'] ) : '' );

		// Save metadata.
		$convertkit_term = new ConvertKit_Term( $term_id );
		return $convertkit_term->save( $meta );

	}

}
