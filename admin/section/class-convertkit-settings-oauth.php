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

		$this->maybe_store_access_token();
	}

	/**
	 * Tests and stores the access token, if it is included in the request.
	 *
	 * @since   2.2.0
	 */
	private function maybe_store_access_token() {

		// Bail if no access token is included in the request.
		if ( ! array_key_exists( 'access_token', $_REQUEST ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return;
		}

		// Sanitize token.
		$access_token = sanitize_text_field( $_REQUEST['access_token'] ); // phpcs:ignore WordPress.Security.NonceVerification

		// Test Access Token by making an API request.
		// @TODO, as this won't work yet.
		// If something fails, redirect using this:
		/*
		wp_safe_redirect( add_query_arg( array(
			'page' 		=> '_wp_convertkit_settings',
			'error' 	=> 'oauth2_error',
		), 'options-general.php' ) );
		exit();
		*/

		// Store Access Token.
		$settings = new ConvertKit_Settings;
		$settings->save( array(
			'access_token' => $access_token,
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
		$oauth_url = $api->get_oauth_url( CONVERTKIT_OAUTH_CALLBACK_URL, admin_url( 'optins-general.php?page=_wp_convertkit_settings' ) );

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
