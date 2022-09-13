<?php
/**
 * ConvertKit Preview Output class.
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
class ConvertKit_Preview_Output {

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
		if ( ! isset( $_REQUEST['convertkit-preview-form-nonce'] ) ) {
			return $form_id;
		}

		// Bail if the nonce verification fails.
		if ( ! wp_verify_nonce( sanitize_key( wp_unslash( $_REQUEST['convertkit-preview-form-nonce'] ) ), 'convertkit-preview-form' ) ) {
			return $form_id;
		}

		// Determine the form to preview.
		$preview_form_id = (int) ( isset( $_REQUEST['convertkit_form_id'] ) ? sanitize_text_field( $_REQUEST['convertkit_form_id'] ) : 0 );

		// Return.
		return $preview_form_id;

	}

	/**
	 * Returns the URL for the most recent published Post based on the supplied Post Type,
	 * with a preview form nonce included in the URL.
	 *
	 * @since   1.9.8.5
	 *
	 * @param   string $post_type  Post Type.
	 * @return  bool|string         false | URL
	 */
	public function get_preview_form_url( $post_type = 'post' ) {

		// Get most recently published Post/Page ID.
		$post_id = $this->get_most_recent( $post_type );

		// Bail if no Post/Page exists.
		if ( ! $post_id ) {
			return false;
		}

		// Return preview URL.
		return add_query_arg(
			array(
				'convertkit-preview-form-nonce' => $this->get_preview_form_nonce(),
			),
			get_permalink( $post_id )
		);

	}

	/**
	 * Returns the nonce to preview a form on a Page, Post or Custom Post Type.
	 *
	 * @since   1.9.8.5
	 *
	 * @return  string  Nonce
	 */
	private function get_preview_form_nonce() {

		return wp_create_nonce( 'convertkit-preview-form' );

	}

	/**
	 * Returns the most recent published Post ID for the given Post Type.
	 *
	 * @since   1.9.8.5
	 *
	 * @param   string $post_type  Post Type.
	 * @return  false|int           Post ID
	 */
	private function get_most_recent( $post_type = 'post' ) {

		// Run query.
		$query = new WP_Query(
			array(
				'post_type'      => $post_type,
				'post_status'    => 'publish',
				'posts_per_page' => 1,
				'orderby'        => 'date',
				'order'          => 'DESC',
				'fields'         => 'ids',
			)
		);

		// Return false if no Posts exist for the given type.
		if ( empty( $query->posts ) ) {
			return false;
		}

		// Return the Post ID.
		return $query->posts[0];

	}

}
