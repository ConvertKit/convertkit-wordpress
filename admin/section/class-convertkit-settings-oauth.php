<?php
/**
 * ConvertKit Settings oAuth class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Registers oAuth integration that is be accessed at Settings > ConvertKit, when the Plugin
 * has no API Key, Secret or Access Token.
 *
 * @package ConvertKit
 * @author ConvertKit
 */
class ConvertKit_Settings_oAuth extends ConvertKit_Settings_Base {

	/**
	 * Constructor
	 * 
	 * @since 	2.2.0
	 */
	public function __construct() {

		$this->settings_key = '_wp_convertkit_oauth'; // Required for ConvertKit_Settings_Base, but we don't save settings on this screen.
		$this->name         = 'oauth';
		$this->title        = __( 'oAuth', 'convertkit' );
		$this->tab_text     = __( 'oAuth', 'convertkit' );

		// Output notices.
		add_action( 'convertkit_settings_base_render_before', array( $this, 'maybe_output_notices' ) );

		parent::__construct();

		$this->maybe_get_and_store_access_token();
	}

	/**
	 * Requests an access token via oAuth, if an authorization code and verifier are included in the request.
	 *
	 * @since   2.2.0
	 */
	private function maybe_get_and_store_access_token() {

		// @TODO Refine this to have a pre-check that we are on the settings screen for the Plugin, so it doesn't
		// greedily attempt to take over the setup wizard submission.

		// Bail if we're not on the settings screen.
		if ( ! array_key_exists( 'page', $_REQUEST ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return;
		}
		$page = sanitize_text_field( $_REQUEST['page'] );
		if ( $page !== '_wp_convertkit_settings' ) {
			return;
		}

		// Bail if no authorization code is included in the request.
		if ( ! array_key_exists( 'code', $_REQUEST ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return;
		}
		if ( ! array_key_exists( 'code_verifier', $_REQUEST ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return;
		}

		// Sanitize token.
		$authorization_code = sanitize_text_field( $_REQUEST['code'] ); // phpcs:ignore WordPress.Security.NonceVerification
		$code_verifier = sanitize_text_field( $_REQUEST['code_verifier'] ); // phpcs:ignore WordPress.Security.NonceVerification

		// @TODO Exchange the authorization code and verifier for a long lived access token.
		$result = 'example-access-token';
		//$result = new WP_Error( 'example_error', 'You did not authorize this application. Please try again.' );
		/*
		$api = new ConvertKit_API();
		$api->set_client_id( CONVERTKIT_OAUTH_CLIENT_ID );
		$access_token = $api->get_access_token( $code_verifier, $authorization_code );
		*/

		// Redirect with an error if we could not fetch the access token.
		if ( is_wp_error( $result ) ) {
			wp_safe_redirect( add_query_arg( array(
				'page' 		=> '_wp_convertkit_settings',
				'error' 	=> 'oauth2_error',
			), 'options-general.php' ) );
			exit();
		}

		// Store Access Token.
		$settings = new ConvertKit_Settings;
		$settings->save( array(
			'access_token' => $result,
		) );

		// Redirect to General screen, which will now show the Plugin's settings, because the Plugin
		// is now authenticated.
		wp_safe_redirect( add_query_arg( array(
			'page' 		=> '_wp_convertkit_settings',
			'success' 	=> 'oauth2_success',
		), 'options-general.php' ) );
		exit();

	}

	/**
	 * Register fields for this section
	 * 
	 * @since 	2.2.0
	 */
	public function register_fields() {

		// No fields are registered for the Debug Log.
		// This function is deliberately blank.
	}

	/**
	 * Outputs the oAuth screen.
	 *
	 * @since   2.2.0
	 */
	public function render() {

		// Determine the oAuth URL to begin the authorization process.
		$api = new ConvertKit_API();
		$api->set_client_id( CONVERTKIT_OAUTH_CLIENT_ID );
		//$oauth_url = $this->api->get_oauth_url( admin_url( 'options-general.php?page=_wp_convertkit_settings' ) );
		$oauth_url = admin_url( 'options-general.php?page=_wp_convertkit_settings&code=authcode&code_verifier=codeverifier' );

		/**
		 * Performs actions prior to rendering the settings form.
		 *
		 * @since   2.0.0
		 */
		do_action( 'convertkit_settings_base_render_before' );

		// Output view.
		require_once CONVERTKIT_PLUGIN_PATH . '/views/backend/settings/oauth.php';

		/**
		 * Performs actions after rendering of the settings form.
		 *
		 * @since   2.0.0
		 */
		do_action( 'convertkit_settings_base_render_after' );

	}

	/**
	 * Prints help info for this section
	 * 
	 * @since 	2.2.0
	 */
	public function print_section_info() {
	}

	/**
	 * Returns the URL for the ConvertKit documentation for this setting section.
	 *
	 * @since   2.2.0
	 *
	 * @return  string  Documentation URL.
	 */
	public function documentation_url() {

		return 'https://help.convertkit.com/en/articles/2502591-the-convertkit-wordpress-plugin';

	}

}
