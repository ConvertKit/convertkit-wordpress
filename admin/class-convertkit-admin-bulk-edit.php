<?php
/**
 * ConvertKit Admin Bulk Edit class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Registers settings fields for output when using WordPress' Bulk Edit functionality
 * in a Post, Page or Custom Post Type WP_List_Table.
 *
 * @package ConvertKit
 * @author ConvertKit
 */
class ConvertKit_Admin_Bulk_Edit {

	/**
	 * Registers action and filter hooks.
	 *
	 * @since   1.9.8.0
	 */
	public function __construct() {

		add_action( 'admin_init', array( $this, 'bulk_edit_save' ) );

	}

	public function bulk_edit_save() {

		// Bail if we cannot determine the screen.
		if ( ! function_exists( 'get_current_screen' ) ) {
			return;
		}

		// Bail if we're not on a Post Type Edit screen.
		$screen = get_current_screen();
		if ( $screen->base !== 'edit' ) {
			return;
		}

		$wp_list_table = _get_list_table( 'WP_Posts_List_Table' );
		$doaction = $wp_list_table->current_action();

				// Bail if the Post isn't a supported Post Type.
		if ( ! in_array( $screen->post_type, convertkit_get_supported_post_types(), true ) ) {
			return;
		}

		var_dump( $doaction );
		die();

	}

	/**
	 * Enqueues scripts and CSS for Bulk Edit functionality in the Post, Page and Custom Post WP_List_Tables
	 *
	 * @since   1.9.8.0
	 */
	public function enqueue_assets() {

		// Enqueue JS.
		wp_enqueue_script( 'convertkit-bulk-edit', CONVERTKIT_PLUGIN_URL . 'resources/backend/js/bulk-edit.js', array( 'jquery' ), CONVERTKIT_PLUGIN_VERSION, true );

		// Enqueue CSS.
		wp_enqueue_style( 'convertkit-bulk-edit', CONVERTKIT_PLUGIN_URL . 'resources/backend/css/bulk-edit.css', array(), CONVERTKIT_PLUGIN_VERSION );

	}

	/**
	 * Outputs Bulk Edit settings fields in the footer of the administration screen.
	 *
	 * The Bulk Edit JS will then move these hidden fields into the Bulk Edit row
	 * when the user clicks on a Bulk Edit action in the WP_List_Table.
	 *
	 * @since   1.9.8.0
	 */
	public function bulk_edit_fields() {

		// Don't output Bulk Edit fields if the API settings have not been defined.
		$settings = new ConvertKit_Settings();
		if ( ! $settings->has_api_key_and_secret() ) {
			return;
		}

		// Fetch Forms, Landing Pages and Tags.
		$convertkit_forms         = new ConvertKit_Resource_Forms();
		$convertkit_landing_pages = new ConvertKit_Resource_Landing_Pages();
		$convertkit_tags          = new ConvertKit_Resource_Tags();

		// Output view.
		require_once CONVERTKIT_PLUGIN_PATH . '/views/backend/post/bulk-edit.php';

	}

}
