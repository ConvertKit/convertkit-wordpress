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

		$this->maybe_perform_actions();
	}

	/**
	 * Possibly perform some actions, such as storing the access token.
	 *
	 * @since   2.2.0
	 */
	private function maybe_perform_actions() {

		$this->maybe_store_access_token();
		$this->maybe_delete_access_token();

	}

	/**
	 * Tests and stores the access token, if it is included in the request.
	 *
	 * @since   2.2.0
	 */
	private function maybe_store_access_token() {

		// Bail if no access token is included in the request.
		if ( ! array_key_exists( '?', $_REQUEST ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return;
		}

		// Test Access Token.
		// @TODO

		// Store Access Token.
		// @TODO

		// Redirect to General screen, which will now show the Plugin's settings, because the Plugin
		// is now authenticated.
		wp_safe_redirect( add_query_arg( array(
			'' => '',
		), 'options-general.php' ) );
		exit();

	}

	/**
	 * Deletes the access token from the Plugin settings, if the request is to deauthorize the Plugin.
	 *
	 * @since   2.2.0
	 */
	private function maybe_delete_access_token() {

		// Bail if no access token is included in the request.
		if ( ! array_key_exists( '?', $_REQUEST ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return;
		}

		// Test Access Token.
		// @TODO

		// Store Access Token.
		// @TODO

		// Redirect to General screen, which will now show the Plugin's settings, because the Plugin
		// is now authenticated.
		wp_safe_redirect( add_query_arg( array(
			'' => '',
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
