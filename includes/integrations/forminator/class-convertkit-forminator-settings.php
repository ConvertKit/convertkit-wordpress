<?php
/**
 * ConvertKit Forminator Settings class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Class to read ConvertKit Forminator Integration Settings.
 *
 * @since   2.3.0
 */
class ConvertKit_Forminator_Settings {

	/**
	 * Holds the Settings Key that stores this integration's settings.
	 *
	 * @var     string
	 *
	 * @since   2.3.0
	 */
	const SETTINGS_NAME = '_wp_convertkit_integration_forminator_settings';

	/**
	 * Holds the Settings
	 *
	 * @var     array
	 *
	 * @since   2.3.0
	 */
	private $settings = array();

	/**
	 * Constructor. Reads settings from options table, falling back to defaults
	 * if no settings exist.
	 *
	 * @since   2.3.0
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
	 * @since   2.3.0
	 *
	 * @return  array
	 */
	public function get() {

		return $this->settings;

	}

	/**
	 * Checks if any settings are defined.
	 *
	 * @since   2.3.0
	 *
	 * @return  bool
	 */
	public function has_settings() {

		if ( empty( $this->get() ) ) {
			return false;
		}

		return true;

	}

	/**
	 * Returns the ConvertKit Form ID that is mapped against the given Forminator Form ID.
	 *
	 * @since   2.3.0
	 *
	 * @param   int $forminator_form_id    Forminator Form ID.
	 * @return  bool|int
	 */
	public function get_convertkit_form_id_by_forminator_form_id( $forminator_form_id ) {

		// Bail if no settings exist.
		if ( ! $this->has_settings() ) {
			return false;
		}

		// Bail if no mapping exists.
		if ( ! array_key_exists( $forminator_form_id, $this->get() ) ) {
			return false;
		}

		return $this->get()[ $forminator_form_id ];

	}

	/**
	 * Returns whether Creator Network Recommendations are enabled for the given Forminator Form ID.
	 *
	 * @since   2.3.0
	 *
	 * @param   int $forminator_form_id    Forminator Form ID.
	 * @return  bool
	 */
	public function get_creator_network_recommendations_enabled_by_forminator_form_id( $forminator_form_id ) {

		// Bail if no settings exist for any Forminator Forms.
		if ( ! $this->has_settings() ) {
			return false;
		}

		// Bail if no setting exists for the given Forminator Form.
		if ( ! array_key_exists( 'creator_network_recommendations_' . $forminator_form_id, $this->get() ) ) {
			return false;
		}

		return (bool) $this->get()[ 'creator_network_recommendations_' . $forminator_form_id ];

	}

	/**
	 * The default settings, used when this integration's Settings haven't been saved
	 * e.g. on a new installation or when the integration's Plugin has just been activated
	 * for the first time.
	 *
	 * @since   2.3.0
	 *
	 * @return  array
	 */
	public function get_defaults() {

		$defaults = array();

		/**
		 * The default settings, used when Forminator's Settings haven't been saved
		 * e.g. on a new installation or when the Forminator Plugin has just been activated
		 * for the first time.
		 *
		 * @since   2.3.0
		 *
		 * @param   array   $defaults   Default Settings.
		 */
		$defaults = apply_filters( 'convertkit_forminator_settings_get_defaults', $defaults );

		return $defaults;

	}

}
