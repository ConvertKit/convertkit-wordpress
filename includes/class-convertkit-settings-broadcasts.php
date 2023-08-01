<?php
/**
 * ConvertKit Broadcasts Settings class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Class to read ConvertKit Broadcasts Settings.
 *
 * @since   2.2.8
 */
class ConvertKit_Settings_Broadcasts {

	/**
	 * Holds the Settings Key that stores site wide ConvertKit settings
	 *
	 * @var     string
	 *
	 * @since   2.2.8
	 */
	const SETTINGS_NAME = '_wp_convertkit_settings_broadcasts';

	/**
	 * Holds the Settings
	 *
	 * @var     array
	 *
	 * @since   2.2.8
	 */
	private $settings = array();

	/**
	 * Constructor. Reads settings from options table, falling back to defaults
	 * if no settings exist.
	 *
	 * @since   2.2.8
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
	 * @since   2.2.8
	 *
	 * @return  array
	 */
	public function get() {

		return $this->settings;

	}

	/**
	 * Returns Broadcasts settings value for the given key.
	 *
	 * @since   2.2.8
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
	 * Returns whether Broadcasts are enabled in the Plugin settings.
	 *
	 * @since   2.2.8
	 *
	 * @return  bool
	 */
	public function enabled() {

		return ( $this->settings['enabled'] === 'on' ? true : false );

	}

	/**
	 * Returns the WordPress Category to assign imported Broadcasts to.
	 *
	 * @since   2.2.8
	 *
	 * @return  bool
	 */
	public function category() {

		return $this->settings['category'];

	}

	/**
	 * Returns the earliest date that Broadcasts should be imported,
	 * based on their send_at date.
	 *
	 * @since   2.2.8
	 *
	 * @return  string  Date (yyyy-mm-dd)
	 */
	public function send_at_min_date() {

		return $this->settings['send_at_min_date'];

	}

	/**
	 * Returns whether imported Broadcasts should have their Restrict Content
	 * setting defined, if the Broadcast is marked as paid.
	 *
	 * @since   2.2.8
	 *
	 * @return  bool
	 */
	public function restrict_content_enabled() {

		return ! empty( $this->settings['restrict_content'] );

	}

	/**
	 * Returns the Restrict Content setting to assign to imported Broadcasts
	 *
	 * @since   2.2.8
	 *
	 * @return  string
	 */
	public function restrict_content() {

		return $this->settings['restrict_content'];

	}

	/**
	 * The default settings, used when the ConvertKit Broadcasts Settings haven't been saved
	 * e.g. on a new installation.
	 *
	 * @since   2.2.8
	 *
	 * @return  array
	 */
	public function get_defaults() {

		$defaults = array(
			'enabled'          => '',
			'category'         => '',

			// By default, only import Broadcasts as Posts for the last 30 days.
			'send_at_min_date' => gmdate( 'Y-m-d', strtotime( '-30 days' ) ),

			'restrict_content' => '',
		);

		/**
		 * The default settings, used when the ConvertKit Broadcasts Settings haven't been saved
		 * e.g. on a new installation.
		 *
		 * @since   2.2.8
		 *
		 * @param   array   $defaults
		 */
		$defaults = apply_filters( 'convertkit_settings_broadcasts_get_defaults', $defaults );

		return $defaults;

	}

	/**
	 * Saves the given array of settings to the WordPress options table.
	 *
	 * @since   2.2.8
	 *
	 * @param   array $settings   Settings.
	 */
	public function save( $settings ) {

		update_option( self::SETTINGS_NAME, array_merge( $this->get(), $settings ) );

	}

}