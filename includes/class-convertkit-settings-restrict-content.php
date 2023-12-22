<?php
/**
 * ConvertKit Restrict Content Settings class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Class to read ConvertKit Restrict Content Settings.
 *
 * @since   2.1.0
 */
class ConvertKit_Settings_Restrict_Content {

	/**
	 * Holds the Settings Key that stores site wide ConvertKit settings
	 *
	 * @var     string
	 *
	 * @since   2.1.0
	 */
	const SETTINGS_NAME = '_wp_convertkit_settings_restrict_content';

	/**
	 * Holds the Settings
	 *
	 * @var     array
	 *
	 * @since   2.1.0
	 */
	private $settings = array();

	/**
	 * Constructor. Reads settings from options table, falling back to defaults
	 * if no settings exist.
	 *
	 * @since   2.1.0
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
	 * @since   2.1.0
	 *
	 * @return  array
	 */
	public function get() {

		return $this->settings;

	}

	/**
	 * Returns Restrict Content settings value for the given key.
	 *
	 * @since   2.1.0
	 *
	 * @param   string $key    Setting Key.
	 * @return  string          Value
	 */
	public function get_by_key( $key ) {

		// If the setting doesn't exist, bail.
		if ( ! array_key_exists( $key, $this->settings ) ) {
			return '';
		}

		// If the setting is empty, fallback to the default.
		if ( empty( $this->settings[ $key ] ) ) {
			$defaults = $this->get_defaults();
			return $defaults[ $key ];
		}

		return $this->settings[ $key ];

	}

	/**
	 * The default settings, used when the ConvertKit Restrict Content Settings haven't been saved
	 * e.g. on a new installation.
	 *
	 * @since   2.1.0
	 *
	 * @return  array
	 */
	public function get_defaults() {

		$defaults = array(
			// Restrict by Product.
			'subscribe_heading'      => __( 'Read this post with a premium subscription', 'convertkit' ),
			'subscribe_text'         => __( 'This post is only available to premium subscribers. Join today to get access to all posts.', 'convertkit' ),

			// Restrict by Tag.
			'subscribe_heading_tag'  => __( 'Subscribe to keep reading', 'convertkit' ),
			'subscribe_text_tag'     => __( 'This post is free to read but only available to subscribers. Join today to get access to all posts.', 'convertkit' ),

			// All.
			'subscribe_button_label' => __( 'Subscribe', 'convertkit' ),
			'email_text'             => __( 'Already subscribed?', 'convertkit' ),
			'email_button_label'     => __( 'Log in', 'convertkit' ),
			'email_heading'          => __( 'Log in to read this post', 'convertkit' ),
			'email_description_text' => __( 'We\'ll email you a magic code to log you in without a password.', 'convertkit' ),
			'email_check_heading'    => __( 'We just emailed you a log in code', 'convertkit' ),
			'email_check_text'       => __( 'Enter the code below to finish logging in', 'convertkit' ),
			'no_access_text'         => __( 'Your account does not have access to this content. Please use the button above to purchase, or enter the email address you used to purchase the product.', 'convertkit' ),
		);

		/**
		 * The default settings, used when the ConvertKit Restrict Content Settings haven't been saved
		 * e.g. on a new installation.
		 *
		 * @since   2.1.0
		 *
		 * @param   array   $defaults
		 */
		$defaults = apply_filters( 'convertkit_settings_restrict_content_get_defaults', $defaults );

		return $defaults;

	}

	/**
	 * Saves the given array of settings to the WordPress options table.
	 *
	 * @since   2.1.0
	 *
	 * @param   array $settings   Settings.
	 */
	public function save( $settings ) {

		update_option( self::SETTINGS_NAME, array_merge( $this->get(), $settings ) );

	}

}
