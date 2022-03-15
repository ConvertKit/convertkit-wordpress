<?php
/**
 * ConvertKit User class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Class to read ConvertKit Settings for the given User.
 *
 * @since   1.9.6
 */
class ConvertKit_User {

	/**
	 * Holds the Meta Key that stores ConvertKit settings on a per-User basis
	 *
	 * @var     string
	 */
	const META_KEY = 'convertkit_tags';

	/**
	 * Holds the User ID
	 *
	 * @since   1.9.6
	 *
	 * @var     int
	 */
	public $user_id = 0;

	/**
	 * Holds the User's Settings
	 *
	 * @var     array
	 */
	private $settings = array();

	/**
	 * Constructor. Populates the settings based on the given User ID.
	 *
	 * @since   1.9.6
	 *
	 * @param   int $user_id    User ID.
	 */
	public function __construct( $user_id ) {

		// Assign User's ID to the object.
		$this->user_id = $user_id;

		// Get User Meta.
		$meta = get_user_meta( $user_id, self::META_KEY, true );
		if ( ! $meta ) {
			// Fallback to default settings.
			$meta = $this->get_default_settings();
		}

		// Assign User's Settings to the object.
		$this->settings = $meta;

	}

	/**
	 * Returns settings for the User.
	 *
	 * @since   1.9.6
	 *
	 * @return  array
	 */
	public function get() {

		return $this->settings;

	}

	/**
	 * Saves User settings to the User.
	 *
	 * @since   1.9.6
	 *
	 * @param   array $meta   Settings.
	 */
	public function save( $meta ) {

		return update_user_meta( $this->user_id, self::META_KEY, $meta );

	}

	/**
	 * The default settings, used to populate the User's Settings when a User
	 * has no Settings.
	 *
	 * @since   1.9.6
	 *
	 * @return  array
	 */
	public function get_default_settings() {

		$defaults = array();

		/**
		 * The default settings, used to populate the User's Settings when a User has no Settings.
		 *
		 * @since   1.9.6
		 *
		 * @param   array   $defaults   Default Settings.
		 */
		$defaults = apply_filters( 'convertkit_user_get_default_settings', $defaults );

		return $defaults;

	}

}
