<?php
/**
 * ConvertKit Admin Deactivation class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Displays a modal requesting an optional reason for the user deactivating
 * the Plugin.
 *
 * @package ConvertKit
 * @author ConvertKit
 */
class ConvertKit_Admin_Deactivation {

	/**
	 * Registers action and filter hooks.
	 *
	 * @since   2.0.2
	 */
	public function __construct() {

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_ajax_convertkit_deactivation_modal_submit', array( $this, 'deactivation_modal_submit' ) );

	}

	/**
	 * Enqueue JavaScript on the Plugins screen to handle displaying
	 * and submitting the deactivation modal.
	 *
	 * @since   2.0.2
	 *
	 * @param   string $hook   Hook.
	 */
	public function enqueue_scripts( $hook ) {

		// Bail if we are not on the Plugin screen.
		if ( $hook !== 'plugins.php' ) {
			return;
		}

		// Enqueue scripts.
		wp_enqueue_script( 'convertkit-admin-deactivation', CONVERTKIT_PLUGIN_URL . 'resources/backend/js/deactivation.js', array( 'jquery' ), CONVERTKIT_PLUGIN_VERSION, true );
		wp_localize_script(
			'convertkit-admin-deactivation',
			'convertkit_deactivation',
			array(
				'plugin' => array(
					'name'    => strtolower( CONVERTKIT_PLUGIN_NAME ),
					'version' => CONVERTKIT_PLUGIN_VERSION
				),
			)
		);

		// Output deactivation modal view in admin footer.
		add_action( 'admin_footer', array( $this, 'output_deactivation_modal' ) );

	}

	/**
	 * Enqueue CSS on the Plugins screen to handle displaying
	 * the deactivation modal.
	 *
	 * @since   2.0.2
	 *
	 * @param   string $hook   Hook.
	 */
	public function enqueue_styles( $hook ) {

		// Bail if we are not on the Plugin screen.
		if ( $hook !== 'plugins.php' ) {
			return;
		}

		// Enqueue styles.
		wp_enqueue_style( 'convertkit-admin-deactivation', CONVERTKIT_PLUGIN_URL . 'resources/backend/css/deactivation.css', array(), CONVERTKIT_PLUGIN_VERSION );

	}

	/**
	 * Outputs the Deactivation Modal HTML, which is displayed by Javascript.
	 *
	 * @since   2.0.2
	 */
	public function output_deactivation_modal() {

		// Define the deactivation reasons.
		$reasons = array(
			'not_working'        => __( 'The Plugin didn\'t work', 'convertkit' ),
			'better_alternative' => __( 'I found a better Plugin', 'convertkit' ),
			'other'              => __( 'Other', 'convertkit' ),
		);

		// Output modal, which will be displayed when the user clicks deactivate on this plugin.
		require_once CONVERTKIT_PLUGIN_PATH . '/views/backend/deactivation/modal.php';

	}

	/**
	 * Sends the deactivation reason.
	 *
	 * @since   2.0.2
	 */
	public function deactivation_modal_submit() {

		// Build args.
		// phpcs:disable WordPress.Security.NonceVerification
		$args = array(
			'product'      => sanitize_text_field( $_REQUEST['product'] ),
			'version'      => sanitize_text_field( $_REQUEST['version'] ),
			'reason'       => sanitize_text_field( $_REQUEST['reason'] ),
			'reason_text'  => sanitize_text_field( $_REQUEST['reason_text'] ),
			'reason_email' => sanitize_text_field( $_REQUEST['reason_email'] ),
			'site_url'     => str_replace( wp_parse_url( get_bloginfo( 'url' ), PHP_URL_SCHEME ) . '://', '', get_bloginfo( 'url' ) ),
		);
		// phpcs:enable

		// Send deactivation reason.
		/*
		// @TODO.
		$response = wp_remote_get( $this->endpoint . '/index.php?' . http_build_query( $args ) );

		// Return error or success, depending on the result.
		if ( is_wp_error( $response ) ) {
			wp_send_json_error( $response->get_error_message(), wp_remote_retrieve_response_code( $response ) );
		}

		wp_send_json_success( wp_remote_retrieve_body( $response ) );
		*/

	}

}
