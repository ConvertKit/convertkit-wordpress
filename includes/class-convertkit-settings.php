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
			'api_key'         => '', // string.
			'api_secret'      => '', // string.
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
	 * Saves the given array of settings to the WordPress options table.
	 *
	 * @since   1.9.8.4
	 *
	 * @param   array $settings   Settings.
	 */
	public function save( $settings ) {

		update_option( self::SETTINGS_NAME, array_merge( $this->get(), $settings ) );

	}

}
