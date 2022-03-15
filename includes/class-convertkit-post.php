<?php
/**
 * ConvertKit Post class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Class to read ConvertKit Settings for the given Post, Page or Custom Post.
 *
 * @since   1.9.6
 */
class ConvertKit_Post {

	/**
	 * Holds the Post Meta Key that stores ConvertKit settings on a per-Post basis
	 *
	 * @var     string
	 */
	const POST_META_KEY = '_wp_convertkit_post_meta';

	/**
	 * Holds the Post ID
	 *
	 * @since   1.9.6
	 *
	 * @var     int
	 */
	public $post_id = 0;

	/**
	 * Holds the Post's Settings
	 *
	 * @var     bool|array
	 */
	private $settings = false;

	/**
	 * Constructor. Populates the settings based on the given Post ID.
	 *
	 * @since   1.9.6
	 *
	 * @param   int $post_id    Post ID.
	 */
	public function __construct( $post_id ) {

		// Assign Post's ID to the object.
		$this->post_id = $post_id;

		// Get Post Meta.
		$meta = get_post_meta( $post_id, self::POST_META_KEY, true );
		if ( ! $meta ) {
			// Fallback to default settings.
			$meta = $this->get_default_settings();

			// Backward compat check for older Plugin version settings.
			$old_value = intval( get_post_meta( $post_id, '_convertkit_convertkit_form', true ) );
			if ( 0 !== $old_value ) {
				$meta['form'] = $old_value;
			}
		}

		// Iterate through default settings, assigning them to the Post Meta array if any keys are missing
		// to guarantee that the Post Meta array has all expected key/value pairs.
		// This covers upgrades from 1.4.6 and earlier that would not set e.g. landing_page and tag keys
		// if no values existed.
		foreach ( $this->get_default_settings() as $key => $value ) {
			if ( ! array_key_exists( $key, $meta ) ) {
				$meta[ $key ] = $value;
			}
		}

		// Assign Post's Settings to the object.
		$this->settings = $meta;

	}

	/**
	 * Returns settings for the Post.
	 *
	 * @since   1.9.6
	 *
	 * @return  array
	 */
	public function get() {

		return $this->settings;

	}

	/**
	 * Returns the form setting for the Post.
	 *
	 * @since   1.9.6
	 *
	 * @return  int
	 */
	public function get_form() {

		return $this->settings['form'];

	}

	/**
	 * Returns the landing page setting for the Post.
	 *
	 * @since   1.9.6
	 *
	 * @return  int
	 */
	public function get_landing_page() {

		return $this->settings['landing_page'];

	}

	/**
	 * Returns the tag setting for the Post.
	 *
	 * @since   1.9.6
	 *
	 * @return  int
	 */
	public function get_tag() {

		return $this->settings['tag'];

	}

	/**
	 * Whether the Post has a ConvertKit Form defined.
	 *
	 * @since   1.9.6
	 *
	 * @return  bool
	 */
	public function has_form() {

		return ( $this->settings['form'] > 0 );

	}

	/**
	 * Whether the Post is set to use the Plugin's Default Form Setting.
	 *
	 * @since   1.9.6
	 *
	 * @return  bool
	 */
	public function uses_default_form() {

		return ( $this->settings['form'] === '-1' );

	}

	/**
	 * Whether the Post's Form setting is set to 'None'
	 *
	 * @since   1.9.6
	 *
	 * @return  bool
	 */
	public function uses_no_form() {

		return ( $this->settings['form'] === '0' );

	}

	/**
	 * Whether the Post has a ConvertKit Landing Page defined.
	 *
	 * @since   1.9.6
	 *
	 * @return  bool
	 */
	public function has_landing_page() {

		return ! empty( $this->settings['landing_page'] );

	}

	/**
	 * Whether the Post has a ConvertKit Tag defined.
	 *
	 * @since   1.9.6
	 *
	 * @return  bool
	 */
	public function has_tag() {

		return ! empty( $this->settings['tag'] );

	}

	/**
	 * Saves Post settings to the Post.
	 *
	 * @since   1.9.6
	 *
	 * @param   array $meta   Settings.
	 * @return  bool          Post Meta was updated
	 */
	public function save( $meta ) {

		return update_post_meta( $this->post_id, self::POST_META_KEY, $meta );

	}

	/**
	 * The default settings, used to populate the Post's Settings when a Post
	 * has no Settings.
	 *
	 * @since   1.9.6
	 *
	 * @return  array
	 */
	public function get_default_settings() {

		$defaults = array(
			'form'         => '-1', // -1: Plugin Default Form, 0: No Form, 1+: Specific Form ID on ConvertKit.
			'landing_page' => '',
			'tag'          => '',
		);

		/**
		 * The default settings, used to populate the Post's Settings when a Post has no Settings.
		 *
		 * @since   1.9.6
		 *
		 * @param   array   $defaults   Default Settings.
		 */
		$defaults = apply_filters( 'convertkit_post_get_default_settings', $defaults );

		return $defaults;

	}

}
