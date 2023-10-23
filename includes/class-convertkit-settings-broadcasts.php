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
 * @since   2.2.9
 */
class ConvertKit_Settings_Broadcasts {

	/**
	 * Holds the Settings Key that stores site wide ConvertKit settings
	 *
	 * @var     string
	 *
	 * @since   2.2.9
	 */
	const SETTINGS_NAME = '_wp_convertkit_settings_broadcasts';

	/**
	 * Holds the Settings
	 *
	 * @var     array
	 *
	 * @since   2.2.9
	 */
	private $settings = array();

	/**
	 * Constructor. Reads settings from options table, falling back to defaults
	 * if no settings exist.
	 *
	 * @since   2.2.9
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
	 * @since   2.2.9
	 *
	 * @return  array
	 */
	public function get() {

		return $this->settings;

	}

	/**
	 * Returns whether Broadcasts are enabled in the Plugin settings.
	 *
	 * @since   2.2.9
	 *
	 * @return  bool
	 */
	public function enabled() {

		return ( $this->settings['enabled'] === 'on' ? true : false );

	}

	/**
	 * Returns the WordPress Author ID to assign imported Broadcasts to.
	 *
	 * @since   2.2.9
	 *
	 * @return  int
	 */
	public function author_id() {

		return $this->settings['author_id'];

	}

	/**
	 * Returns the WordPress Post Status to assign to Posts created from imported Broadcasts.
	 *
	 * @since   2.3.4
	 *
	 * @return  string
	 */
	public function post_status() {

		return $this->settings['post_status'];

	}

	/**
	 * Returns the WordPress Category ID to assign imported Broadcasts to.
	 *
	 * @since   2.2.9
	 *
	 * @return  int
	 */
	public function category_id() {

		return $this->settings['category_id'];

	}

	/**
	 * Returns the earliest date that Broadcasts should be imported,
	 * based on their published_at date.
	 *
	 * @since   2.2.9
	 *
	 * @return  string  Date (yyyy-mm-dd)
	 */
	public function published_at_min_date() {

		return $this->settings['published_at_min_date'];

	}

	/**
	 * Returns whether exporting Posts to Broadcasts is enabled in the Plugin settings.
	 *
	 * @since   2.4.0
	 *
	 * @return  bool
	 */
	public function enabled_export() {

		return ( $this->settings['enabled_export'] === 'on' ? true : false );

	}

	/**
	 * Returns whether Broadcasts should have their styles imported.
	 *
	 * @since   2.2.9
	 *
	 * @return  bool
	 */
	public function no_styles() {

		return ( $this->settings['no_styles'] === 'on' ? true : false );

	}

	/**
	 * Returns whether imported Broadcasts should have their Restrict Content
	 * setting defined, if the Broadcast is marked as paid.
	 *
	 * @since   2.2.9
	 *
	 * @return  bool
	 */
	public function restrict_content_enabled() {

		return ! empty( $this->settings['restrict_content'] );

	}

	/**
	 * Returns the Restrict Content setting to assign to imported Broadcasts
	 *
	 * @since   2.2.9
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
	 * @since   2.2.9
	 *
	 * @return  array
	 */
	public function get_defaults() {

		$defaults = array(
			'enabled'               => '',
			'author_id'             => get_current_user_id(),
			'post_status'           => 'publish',
			'category_id'           => '',

			// By default, only import Broadcasts as Posts for the last 30 days.
			'published_at_min_date' => gmdate( 'Y-m-d', strtotime( '-30 days' ) ),

			'enabled_export'        => '',
			'no_styles'             => '',
		);

		/**
		 * The default settings, used when the ConvertKit Broadcasts Settings haven't been saved
		 * e.g. on a new installation.
		 *
		 * @since   2.2.9
		 *
		 * @param   array   $defaults
		 */
		$defaults = apply_filters( 'convertkit_settings_broadcasts_get_defaults', $defaults );

		return $defaults;

	}

	/**
	 * Saves the given array of settings to the WordPress options table.
	 *
	 * @since   2.2.9
	 *
	 * @param   array $settings   Settings.
	 */
	public function save( $settings ) {

		update_option( self::SETTINGS_NAME, array_merge( $this->get(), $settings ) );

	}

}
