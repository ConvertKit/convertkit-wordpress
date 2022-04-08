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
	 * @var     bool|string
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
	 * @return  string
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
	 * @return  bool
	 */
	public function has_form() {

		// Backward compat. for Terms created/edited prior to 1.9.6, where
		// 'default' was used instead of zero to denote the Post / Post Type
		// setting should be used for determining the Form to display.
		// Using a comparison operator on 'default' will return different results in:
		// PHP < 8.0: 'default' > 0 is false
		// PHP 8.0+: 'default' > 0 is true
		// See https://www.php.net/manual/en/language.operators.comparison.php.
		if ( $this->settings === 'default' ) {
			return false;
		}

		// If the setting is greater than zero, it's a specific Form ID that
		// should be used for Posts assigned to this Category.
		return ( $this->settings > 0 );

	}

	/**
	 * Saves Term settings to the Term.
	 *
	 * @since   1.9.6
	 *
	 * @param   string $meta   Settings.
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
	 * @return  string
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
