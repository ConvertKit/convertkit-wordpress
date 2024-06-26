<?php
/**
 * ConvertKit Plugin Settings class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Class to read ConvertKit Plugin Settings.
 *
 * @since   1.9.6
 */
class ConvertKit_Settings {

	/**
	 * Holds the Settings Key that stores site wide ConvertKit settings
	 *
	 * @var     string
	 */
	const SETTINGS_NAME = '_wp_convertkit_settings';

	/**
	 * Holds the Settings
	 *
	 * @var     array
	 */
	private $settings = array();

	/**
	 * Constructor. Reads settings from options table, falling back to defaults
	 * if no settings exist.
	 *
	 * @since   1.9.6
	 */
	public function __construct() {

		// Get Settings.
		$settings = get_option( self::SETTINGS_NAME );

		// If no Settings exist, falback to default settings.
		if ( ! $settings ) {
			$this->settings = $this->get_defaults();
		} else {
			$this->settings = array_merge( $this->get_defaults(), $settings );
		}

		// Update Access Token when refreshed by the API class.
		add_action( 'convertkit_api_refresh_token', array( $this, 'update_credentials' ), 10, 2 );

	}

	/**
	 * Returns Plugin settings.
	 *
	 * @since   1.9.6
	 *
	 * @return  array
	 */
	public function get() {

		return $this->settings;

	}

	/**
	 * Returns the API Key Plugin setting.
	 *
	 * @since   1.9.6
	 *
	 * @return  string
	 */
	public function get_api_key() {

		// Return API Key from constant, if defined.
		if ( defined( 'CONVERTKIT_API_KEY' ) ) {
			return CONVERTKIT_API_KEY;
		}

		// Return API Key from settings.
		return $this->settings['api_key'];

	}

	/**
	 * Returns whether the API Key has been set in the Plugin settings.
	 *
	 * @since   1.9.6
	 *
	 * @return  bool
	 */
	public function has_api_key() {

		return ( ! empty( $this->get_api_key() ) ? true : false );

	}

	/**
	 * Returns whether the API Key is stored as a constant in the wp-config.php file.
	 *
	 * @since   1.9.6
	 *
	 * @return  bool
	 */
	public function is_api_key_a_constant() {

		return defined( 'CONVERTKIT_API_KEY' );

	}

	/**
	 * Returns the API Secret Plugin setting.
	 *
	 * @since   1.9.6
	 *
	 * @return  string
	 */
	public function get_api_secret() {

		// Return API Secret from constant, if defined.
		if ( defined( 'CONVERTKIT_API_SECRET' ) ) {
			return CONVERTKIT_API_SECRET;
		}

		// Return API Secret from settings.
		return $this->settings['api_secret'];

	}

	/**
	 * Returns whether the API Secret has been set in the Plugin settings.
	 *
	 * @since   1.9.6
	 *
	 * @return  bool
	 */
	public function has_api_secret() {

		return ( ! empty( $this->get_api_secret() ) ? true : false );

	}

	/**
	 * Returns whether the API Secret is stored as a constant in the wp-config.php file.
	 *
	 * @since   1.9.6
	 *
	 * @return  bool
	 */
	public function is_api_secret_a_constant() {

		return defined( 'CONVERTKIT_API_SECRET' );

	}

	/**
	 * Returns whether the API Key and Secret have been set in the Plugin settings.
	 *
	 * @since   1.9.6
	 *
	 * @return  bool
	 */
	public function has_api_key_and_secret() {

		return $this->has_api_key() && $this->has_api_secret();

	}

	/**
	 * Returns the Access Token Plugin setting.
	 *
	 * @since   2.5.0
	 *
	 * @return  string
	 */
	public function get_access_token() {

		// Return Access Token from settings.
		return $this->settings['access_token'];

	}

	/**
	 * Returns whether the Access Token has been set in the Plugin settings.
	 *
	 * @since   2.5.0
	 *
	 * @return  bool
	 */
	public function has_access_token() {

		return ( ! empty( $this->get_access_token() ) ? true : false );

	}

	/**
	 * Returns the Refresh Token Plugin setting.
	 *
	 * @since   2.5.0
	 *
	 * @return  string
	 */
	public function get_refresh_token() {

		// Return Refresh Token from settings.
		return $this->settings['refresh_token'];

	}

	/**
	 * Returns whether the Refresh Token has been set in the Plugin settings.
	 *
	 * @since   2.5.0
	 *
	 * @return  bool
	 */
	public function has_refresh_token() {

		return ( ! empty( $this->get_refresh_token() ) ? true : false );

	}

	/**
	 * Returns whether to use Access and Refresh Tokens for API requests,
	 * based on whether an Access Token and Refresh Token have been saved
	 * in the Plugin settings.
	 *
	 * @since   2.5.0
	 *
	 * @return  bool
	 */
	public function has_access_and_refresh_token() {

		return $this->has_access_token() && $this->has_refresh_token();

	}

	/**
	 * Returns the Access Token expiry timestamp.
	 *
	 * @since   2.5.0
	 *
	 * @return  int
	 */
	public function get_token_expiry() {

		// Return Token Expiry from settings.
		return $this->settings['token_expires'];

	}

	/**
	 * Returns the Default Form Plugin setting.
	 *
	 * @since   1.9.6
	 *
	 * @param   string $post_type  Post Type.
	 * @return  string|int          Default Form (default|form id)
	 */
	public function get_default_form( $post_type ) {

		// Return default if this Post Type doesn't exist as a setting.
		if ( ! array_key_exists( $post_type . '_form', $this->settings ) ) {
			return 'default';
		}

		// Backward compat. where older Plugin versions would store API errors in the option value
		// with id = -2 and name = 'Error contacting API'.
		if ( is_array( $this->settings[ $post_type . '_form' ] ) ) {
			return 'default';
		}

		return $this->settings[ $post_type . '_form' ];

	}

	/**
	 * Returns whether the Default Form has been set in the Plugin settings.
	 *
	 * @since   1.9.6
	 *
	 * @param   string $post_type  Post Type.
	 * @return  bool                Post Type has a Default Form setting specified in Plugin Settings.
	 */
	public function has_default_form( $post_type ) {

		return ( ! empty( $this->settings[ $post_type . '_form' ] ) ? true : false );

	}

	/**
	 * Returns the Global non-inline Form Plugin setting.
	 *
	 * @since   2.3.3
	 *
	 * @return  string|int       Non-inline Form (blank string|form id)
	 */
	public function get_non_inline_form() {

		// Return blank string if no inline form is specified.
		if ( ! $this->has_non_inline_form() ) {
			return '';
		}

		return $this->settings['non_inline_form'];

	}

	/**
	 * Returns whether the Global non-inline Form has been set in the Plugin settings.
	 *
	 * @since   2.3.3
	 *
	 * @return  bool    Global non-inline Form setting specified in Plugin Settings.
	 */
	public function has_non_inline_form() {

		return ( ! empty( $this->settings['non_inline_form'] ) ? true : false );

	}

	/**
	 * Returns whether debugging is enabled in the Plugin settings.
	 *
	 * @since   1.9.6
	 *
	 * @return  bool
	 */
	public function debug_enabled() {

		return ( $this->settings['debug'] === 'on' ? true : false );

	}

	/**
	 * Returns whether scripts are disabled in the Plugin settings.
	 *
	 * @since   1.9.6
	 *
	 * @return  bool
	 */
	public function scripts_disabled() {

		return ( $this->settings['no_scripts'] === 'on' ? true : false );

	}

	/**
	 * Returns whether stylesheets are disabled in the Plugin settings.
	 *
	 * @since   1.9.6.9
	 *
	 * @return  bool
	 */
	public function css_disabled() {

		return ( $this->settings['no_css'] === 'on' ? true : false );

	}

	/**
	 * The default settings, used when the ConvertKit Plugin Settings haven't been saved
	 * e.g. on a new installation.
	 *
	 * @since   1.9.6
	 *
	 * @return  array
	 */
	public function get_defaults() {

		$defaults = array(
			// OAuth.
			'access_token'    => '', // string.
			'refresh_token'   => '', // string.
			'token_expires'   => '', // integer.

			// API Key. Retained if needed for backward compat.
			'api_key'         => '', // string.
			'api_secret'      => '', // string.

			// Settings.
			'non_inline_form' => '', // string.
			'debug'           => '', // blank|on.
			'no_scripts'      => '', // blank|on.
			'no_css'          => '', // blank|on.
		);

		// Add Post Type Default Forms.
		foreach ( convertkit_get_supported_post_types() as $post_type ) {
			$defaults[ $post_type . '_form' ] = 0; // -1, 0 or Form ID.
		}

		/**
		 * The default settings, used when the ConvertKit Plugin Settings haven't been saved
		 * e.g. on a new installation.
		 *
		 * @since   1.9.6
		 *
		 * @param   array   $defaults   Default Settings.
		 */
		$defaults = apply_filters( 'convertkit_settings_get_defaults', $defaults );

		return $defaults;

	}

	/**
	 * Saves the new access token, refresh token and its expiry when the API
	 * class automatically refreshes an outdated access token.
	 *
	 * @since   2.5.0
	 *
	 * @param   array  $result      New Access Token, Refresh Token and Expiry.
	 * @param   string $client_id   OAuth Client ID used for the Access and Refresh Tokens.
	 */
	public function update_credentials( $result, $client_id ) {

		// Don't save these credentials if they're not for this Client ID.
		// They're for another ConvertKit Plugin that uses OAuth.
		if ( $client_id !== CONVERTKIT_OAUTH_CLIENT_ID ) {
			return;
		}

		$this->save(
			array(
				'access_token'  => $result['access_token'],
				'refresh_token' => $result['refresh_token'],
				'token_expires' => ( $result['created_at'] + $result['expires_in'] ),
			)
		);

	}

	/**
	 * Deletes any existing access token, refresh token and its expiry from the Plugin settings.
	 *
	 * @since   2.5.0
	 */
	public function delete_credentials() {

		$this->save(
			array(
				'access_token'  => '',
				'refresh_token' => '',
				'token_expires' => '',
			)
		);

	}

	/**
	 * Saves the given array of settings to the WordPress options table.
	 *
	 * @since   1.9.8.4
	 *
	 * @param   array $settings   Settings.
	 */
	public function save( $settings ) {

		update_option( self::SETTINGS_NAME, array_merge( $this->get(), $settings ) );

		// Reload settings in class, to reflect changes.
		$this->settings = get_option( self::SETTINGS_NAME );

	}

}
