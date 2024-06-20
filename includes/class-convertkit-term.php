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
	const TERM_META_KEY = '_wp_convertkit_term_meta';

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
	 * @var     bool|array
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
		$meta = get_term_meta( $term_id, self::TERM_META_KEY, true );

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

		return $this->settings['form'];

	}

	/**
	 * Whether the Term has a ConvertKit Form defined.
	 *
	 * @since   1.9.6
	 *
	 * @return  bool
	 */
	public function has_form() {

		return ( $this->settings['form'] > 0 );

	}

	/**
	 * Returns the form position setting for the Term
	 * on the Term archive.
	 *
	 * @since   2.4.9.1
	 *
	 * @return  string
	 */
	public function get_form_position() {

		return $this->settings['form_position'];

	}

	/**
	 * Whether the Term has a ConvertKit Form Position defined
	 * for the Term archive.
	 *
	 * @since   2.4.9.1
	 *
	 * @return  bool
	 */
	public function has_form_position() {

		return ( $this->settings['form_position'] !== '' );

	}

	/**
	 * Saves Term settings to the Term.
	 *
	 * @since   1.9.6
	 *
	 * @param   array $meta   Settings.
	 * @return  bool          Term Meta was updated
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

		$defaults = array(
			'form'          => '',
			'form_position' => '',
		);

		/**
		 * The default settings, used to populate the Term's Settings when a Term
		 * has no Settings.
		 *
		 * @since   1.9.6
		 *
		 * @param   array  $defaults   Default Form
		 */
		$defaults = apply_filters( 'convertkit_term_get_default_settings', $defaults );

		return $defaults;

	}

}
