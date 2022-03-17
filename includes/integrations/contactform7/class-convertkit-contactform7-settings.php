<?php
/**
 * ConvertKit Contact Form 7 Settings class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Class to read ConvertKit Contact Form 7 Integration Settings.
 *
 * @since   1.9.6
 */
class ConvertKit_ContactForm7_Settings {

	/**
	 * Holds the Settings Key that stores this integration's settings.
	 *
	 * @var     string
	 */
	const SETTINGS_NAME = '_wp_convertkit_integration_contactform7_settings';

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
			$this->settings = $settings;
		}

	}

	/**
	 * Returns Integration settings.
	 *
	 * @since   1.9.6
	 *
	 * @return  array
	 */
	public function get() {

		return $this->settings;

	}

	/**
	 * Checks if any settings are defined.
	 *
	 * @since   1.9.6
	 *
	 * @return  bool
	 */
	public function has_settings() {

		if ( empty( $this->get() ) ) {
			return false;
		}
		if ( count( $this->get() ) === 0 ) { // @phpstan-ignore-line.
			return false;
		}

		return true;

	}

	/**
	 * Returns the ConvertKit Form ID that is mapped against the given Contact Form 7 Form ID.
	 *
	 * @since   1.9.6
	 *
	 * @param   int $cf7_form_id    Contact Form 7 Form ID.
	 * @return  bool|int
	 */
	public function get_convertkit_form_id_by_cf7_form_id( $cf7_form_id ) {

		// Bail if no settings exist.
		if ( ! $this->has_settings() ) {
			return false;
		}

		// Bail if no mapping exists.
		if ( ! array_key_exists( $cf7_form_id, $this->get() ) ) {
			return false;
		}

		return $this->get()[ $cf7_form_id ];

	}

	/**
	 * The default settings, used when this integration's Settings haven't been saved
	 * e.g. on a new installation or when the integration's Plugin has just been activated
	 * for the first time.
	 *
	 * @since   1.9.6
	 *
	 * @return  array
	 */
	public function get_defaults() {

		$defaults = array();

		/**
		 * The default settings, used when Contact Form 7's Settings haven't been saved
		 * e.g. on a new installation or when the Contact Form 7 Plugin has just been activated
		 * for the first time.
		 *
		 * @since   1.9.6
		 *
		 * @param   array   $defaults   Default Settings.
		 */
		$defaults = apply_filters( 'convertkit_contactform7_settings_get_defaults', $defaults );

		return $defaults;

	}

}
