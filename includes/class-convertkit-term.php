<?php
/**
 * ConvertKit Term class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Class to read ConvertKit Settings for the given Taxonomy Term.
 *
 * @since   1.9.6
 */
class ConvertKit_Term {

	/**
	 * Holds the Term Meta Key that stores ConvertKit settings on a per-Taxonomy Term basis
	 *
	 * @var     string
	 */
	const TERM_META_KEY = 'ck_default_form';

	/**
	 * Holds the Term ID
	 *
	 * @since   1.9.6
	 *
	 * @var     int
	 */
	public $term_id = 0;

	/**
	 * Holds the Term's Settings
	 *
	 * @var     array
	 */
	private $settings = false;

	/**
	 * Constructor. Populates the settings based on the given Term ID.
	 *
	 * @since   1.9.6
	 *
	 * @param   int $term_id    Term ID.
	 */
	public function __construct( $term_id ) {

		// Assign Term's ID to the object.
		$this->term_id = $term_id;

		// Get Term Meta.
		$meta = get_term_meta( $term_id, 'ck_default_form', true );

		if ( ! $meta ) {
			// Fallback to default settings.
			$meta = $this->get_default_settings();
		}

		// Assign Term's Settings to the object.
		$this->settings = $meta;

	}

	/**
	 * Returns settings for the Term.
	 *
	 * @since   1.9.6
	 *
	 * @return  array
	 */
	public function get() {

		return $this->settings;

	}

	/**
	 * Returns the form setting for the Term.
	 *
	 * @since   1.9.6
	 *
	 * @return  string
	 */
	public function get_form() {

		return $this->settings;

	}

	/**
	 * Whether the Term has a ConvertKit Form defined.
	 *
	 * @since   1.9.6
	 *
	 * @return  string
	 */
	public function has_form() {

		return ( $this->settings > 0 );

	}

	/**
	 * Whether the Term is set to use the Plugin's Default Form Setting.
	 *
	 * @since   1.9.6
	 *
	 * @return  string
	 */
	public function uses_default_form() {

		return ( $this->settings === '-1' );

	}

	/**
	 * Whether the Term is set to use NO Form.
	 *
	 * @since   1.9.6
	 *
	 * @return  string
	 */
	public function uses_no_form() {

		return ( $this->settings === '0' );

	}

	/**
	 * Saves Term settings to the Term.
	 *
	 * @since   1.9.6
	 *
	 * @param   array $meta   Settings.
	 */
	public function save( $meta ) {

		return update_term_meta( $this->term_id, self::TERM_META_KEY, $meta );

	}

	/**
	 * The default settings, used to populate the Term's Settings when a Term
	 * has no Settings.
	 *
	 * @since   1.9.6
	 *
	 * @return  array
	 */
	public function get_default_settings() {

		$defaults = '';

		/**
		 * The default settings, used to populate the Term's Settings when a Term
		 * has no Settings.
		 *
		 * @since   1.9.6
		 *
		 * @param   string  $defaults   Default Form
		 */
		$defaults = apply_filters( 'convertkit_term_get_default_settings', $defaults );

		return $defaults;

	}

}
