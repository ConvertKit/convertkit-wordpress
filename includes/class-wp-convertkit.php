<?php
/**
 * ConvertKit class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Class ConvertKit
 *
 * @package ConvertKit
 * @author ConvertKit
 */
class WP_ConvertKit {

	/**
	 * Holds the class object.
	 *
	 * @since   1.9.6
	 *
	 * @var     object
	 */
	public static $instance;

	/**
	 * Holds singleton initialized classes that include
	 * action and filter hooks.
	 *
	 * @since   1.9.6
	 *
	 * @var     array
	 */
	private $classes = array();

	/**
	 * Constructor. Acts as a bootstrap to load the rest of the plugin
	 *
	 * @since   1.9.6
	 */
	public function __construct() {

		// Initialize class(es) to register hooks.
		$this->initialize_admin();
		$this->initialize_admin_or_frontend_editor();
		$this->initialize_cli_cron();
		$this->initialize_frontend();
		$this->initialize_global();

		// Load language files.
		add_action( 'init', array( $this, 'load_language_files' ) );

	}

	/**
	 * Initialize classes for the WordPress Administration interface
	 *
	 * @since   1.9.6
	 */
	private function initialize_admin() {

		// Bail if this request isn't for the WordPress Administration interface.
		if ( ! is_admin() ) {
			return;
		}

		$this->classes['admin_category']  = new ConvertKit_Admin_Category();
		$this->classes['admin_gutenberg'] = new ConvertKit_Admin_Gutenberg();
		$this->classes['admin_post']      = new ConvertKit_Admin_Post();
		$this->classes['admin_settings']  = new ConvertKit_Admin_Settings();
		$this->classes['admin_tinymce']   = new ConvertKit_Admin_TinyMCE();
		$this->classes['admin_upgrade']   = new ConvertKit_Admin_Upgrade();
		$this->classes['admin_user']      = new ConvertKit_Admin_User();

		// Run upgrade routine.
		add_action( 'init', array( $this->classes['admin_upgrade'], 'run' ), 2 );

		/**
		 * Initialize integration classes for the WordPress Administration interface.
		 *
		 * @since   1.9.6
		 */
		do_action( 'convertkit_initialize_admin' );

	}

	/**
	 * Initialize classes for the WordPress Administration interface or a frontend Page Builder
	 *
	 * @since   1.9.6
	 */
	private function initialize_admin_or_frontend_editor() {

		// Bail if this request isn't for the WordPress Administration interface and isn't for a frontend Page Builder.
		if ( ! $this->is_admin_or_frontend_editor() ) {
			return;
		}

		/**
		 * Initialize integration classes for the WordPress Administration interface or a frontend Page Builder.
		 *
		 * @since   1.9.6
		 */
		do_action( 'convertkit_initialize_admin_or_frontend_editor' );

	}

	/**
	 * Initialize classes for WP-CLI and WP-Cron
	 *
	 * @since   2.5.2
	 */
	private function initialize_cli_cron() {

		// Bail if this isn't a CLI or CRON request.
		if ( ! $this->is_cli() && ! $this->is_cron() ) {
			return;
		}

		/**
		 * Initialize integration classes for WP-CLI and WP-Cron.
		 *
		 * @since   1.9.6
		 */
		do_action( 'convertkit_initialize_cli_cron' );

	}

	/**
	 * Initialize classes for the frontend web site
	 *
	 * @since   1.9.6
	 */
	private function initialize_frontend() {

		// Bail if this request isn't for the frontend web site.
		if ( is_admin() ) {
			return;
		}

		$this->classes['output'] = new ConvertKit_Output();

		/**
		 * Initialize integration classes for the frontend web site.
		 *
		 * @since   1.9.6
		 */
		do_action( 'convertkit_initialize_frontend' );

	}

	/**
	 * Initialize classes required globally, across the WordPress Administration, CLI, Cron and Frontend
	 * web site.
	 *
	 * @since   1.9.6
	 */
	private function initialize_global() {

		$this->classes['ajax']                      = new ConvertKit_AJAX();
		$this->classes['blocks_convertkit_content'] = new ConvertKit_Block_Content();
		$this->classes['blocks_convertkit_form']    = new ConvertKit_Block_Form();
		$this->classes['shortcodes']                = new ConvertKit_Shortcodes();
		$this->classes['widgets']                   = new ConvertKit_Widgets();

		/**
		 * Initialize integration classes for the frontend web site.
		 *
		 * @since   1.9.6
		 */
		do_action( 'convertkit_initialize_global' );

	}

	/**
	 * Improved version of WordPress' is_admin(), which includes whether we're
	 * editing on the frontend using a Page Builder.
	 *
	 * @since   1.9.6
	 *
	 * @return  bool    Is Admin or Frontend Editor Request
	 */
	public function is_admin_or_frontend_editor() {

		// If we're in the wp-admin, return true.
		if ( is_admin() ) {
			return true;
		}

		// Pro.
		if ( isset( $_SERVER ) ) {
			if ( strpos( sanitize_text_field( $_SERVER['REQUEST_URI'] ), '/pro/' ) !== false ) {
				return true;
			}
			if ( strpos( sanitize_text_field( $_SERVER['REQUEST_URI'] ), '/x/' ) !== false ) {
				return true;
			}
			if ( strpos( sanitize_text_field( $_SERVER['REQUEST_URI'] ), 'cornerstone-endpoint' ) !== false ) {
				return true;
			}
		}

		// If the request global exists, check for specific request keys which tell us
		// that we're using a frontend editor.
		if ( isset( $_REQUEST ) && ! empty( $_REQUEST ) ) { // phpcs:ignore
			// Avada Live.
			if ( array_key_exists( 'fb-edit', $_REQUEST ) ) { // phpcs:ignore
				return true;
			}

			// Beaver Builder.
			if ( array_key_exists( 'fl_builder', $_REQUEST ) ) { // phpcs:ignore
				return true;
			}

			// Brizy.
			if ( array_key_exists( 'brizy-edit', $_REQUEST ) ) { // phpcs:ignore
				return true;
			}

			// Cornerstone (AJAX).
			if ( array_key_exists( '_cs_nonce', $_REQUEST ) ) { // phpcs:ignore
				return true;
			}

			// Divi.
			if ( array_key_exists( 'et_fb', $_REQUEST ) ) { // phpcs:ignore
				return true;
			}

			// Elementor.
			if ( array_key_exists( 'action', $_REQUEST ) && sanitize_text_field( $_REQUEST['action'] ) === 'elementor' ) { // phpcs:ignore
				return true;
			}

			// Kallyas.
			if ( array_key_exists( 'zn_pb_edit', $_REQUEST ) ) { // phpcs:ignore
				return true;
			}

			// Oxygen.
			if ( array_key_exists( 'ct_builder', $_REQUEST ) ) { // phpcs:ignore
				return true;
			}

			// Thrive Architect.
			if ( array_key_exists( 'tve', $_REQUEST ) ) { // phpcs:ignore
				return true;
			}

			// Visual Composer.
			if ( array_key_exists( 'vcv-editable', $_REQUEST ) ) { // phpcs:ignore
				return true;
			}

			// WPBakery Page Builder.
			if ( array_key_exists( 'vc_editable', $_REQUEST ) ) { // phpcs:ignore
				return true;
			}

			// Zion Builder.
			if ( array_key_exists( 'action', $_REQUEST ) && sanitize_text_field( $_REQUEST['action'] ) === 'zion_builder_active' ) { // phpcs:ignore
				return true;
			}
		}

		// Assume we're not in the Administration interface.
		$is_admin_or_frontend_editor = false;

		/**
		 * Filters whether the current request is a WordPress Administration / Frontend Editor request or not.
		 *
		 * Page Builders can set this to true to allow ConvertKit to load its administration functionality.
		 *
		 * @since   1.9.6
		 *
		 * @param   bool    $is_admin_or_frontend_editor    Is WordPress Administration / Frontend Editor request.
		 * @param   array   $_REQUEST                       $_REQUEST data.
		 */
		$is_admin_or_frontend_editor = apply_filters( 'convertkit_is_admin_or_frontend_editor', $is_admin_or_frontend_editor, $_REQUEST );  // phpcs:ignore

		// Return filtered result.
		return $is_admin_or_frontend_editor;

	}

	/**
	 * Detects if the request is through the WP-CLI
	 *
	 * @since   1.9.6
	 *
	 * @return  bool    Is WP-CLI Request
	 */
	public function is_cli() {

		if ( ! defined( 'WP_CLI' ) ) {
			return false;
		}
		if ( ! WP_CLI ) {
			return false;
		}

		return true;

	}

	/**
	 * Detects if the request is through the WP CRON
	 *
	 * @since   1.9.6
	 *
	 * @return  bool    Is WP CRON Request
	 */
	public function is_cron() {

		if ( ! defined( 'DOING_CRON' ) ) {
			return false;
		}
		if ( ! DOING_CRON ) {
			return false;
		}

		return true;

	}

	/**
	 * Loads plugin textdomain
	 *
	 * @since   1.0.0
	 */
	public function load_language_files() {

		load_plugin_textdomain( 'convertkit', false, basename( dirname( __FILE__ ) ) . '/languages/' );

	}

	/**
	 * Returns the given class
	 *
	 * @since   1.9.6
	 *
	 * @param   string $name   Class Name.
	 * @return  object          Class Object
	 */
	public function get_class( $name ) {

		// If the class hasn't been loaded, throw a WordPress die screen
		// to avoid a PHP fatal error.
		if ( ! isset( $this->classes[ $name ] ) ) {
			// Define the error.
			$error = new WP_Error(
				'convertkit_get_class',
				sprintf(
					/* translators: %1$s: PHP class name */
					__( 'ConvertKit Error: Could not load Plugin class <strong>%1$s</strong>', 'convertkit' ),
					$name
				)
			);

			// Depending on the request, return or display an error.
			// Admin UI.
			if ( is_admin() ) {
				wp_die(
					esc_attr( $error ),
					esc_html__( 'ConvertKit Error', 'convertkit' ),
					array(
						'back_link' => true,
					)
				);
			}

			// Cron / CLI.
			return $error;
		}

		// Return the class object.
		return $this->classes[ $name ];

	}

	/**
	 * Returns the singleton instance of the class.
	 *
	 * @since   1.1.6
	 *
	 * @return  object Class.
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {
			self::$instance = new self();
		}

		return self::$instance;

	}

}
