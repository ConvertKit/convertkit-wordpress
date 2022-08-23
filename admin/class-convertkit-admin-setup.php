<?php
/**
 * ConvertKit Admin Setup class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Provides a UI for setting up the ConvertKit Plugin when activated for the
 * first time.
 * 
 * If the Plugin has previously been configured (i.e. settings exist in the database),
 * this UI isn't triggered on activation.
 *
 * @package ConvertKit
 * @author ConvertKit
 */
class ConvertKit_Admin_Setup {

	/**
	 * Holds the ConvertKit Forms resource class.
	 *
	 * @since   1.9.8.5
	 *
	 * @var     bool|ConvertKit_Resource_Forms
	 */
	public $forms = false;

	/**
	 * Holds an error message to display on screen.
	 *
	 * @since   1.9.8.5
	 *
	 * @var     bool|string
	 */
	public $error = false;

	/**
	 * Registers action and filter hooks.
	 *
	 * @since   1.9.8.5
	 */
	public function __construct() {

		add_action( 'admin_menu', array( $this, 'register_screen' ) );
		add_action( 'admin_head', array( $this, 'hide_screen_from_menu' ) );
		add_action( 'admin_init', array( $this, 'maybe_redirect_to_setup_screen' ), 9999 );
		add_action( 'admin_init', array( $this, 'maybe_load_setup_screen' ) );
		
	}

	/**
	 * Register the setup screen in WordPress' Dashboard, so that index.php?page=convertkit-setup
	 * does not 404 when in the WordPress Admin interface.
	 *
	 * @since   1.9.8.5
	 */
	public function register_screen() {

		add_dashboard_page( '', '', 'edit_posts', 'convertkit-setup', '' );

	}

	/**
	 * Hides the menu registered when register_screen() above is called, otherwise
	 * we would have a blank submenu entry below the Dashboard menu.
	 * 
	 * @since 	1.9.8.5
	 */ 
	public function hide_screen_from_menu() {

		remove_submenu_page( 'index.php', 'convertkit-setup' );

	}

	/**
	 * Redirects to the setup screen if a transient was created on Plugin activation,
	 * and the Plugin has no API Key and Secret configured.
	 * 
	 * @since 	1.9.8.5
	 */
	public function maybe_redirect_to_setup_screen() {

		// If no transient was set by the Plugin's activation routine, don't redirect to the setup screen.
		// This transient will only exist for 30 seconds by design, so we don't hijack a later WordPress
		// Admin screen request.
		if ( ! get_transient( 'convertkit_setup' ) ) {
			return;
		}

		// Delete the transient, so we don't redirect again.
		delete_transient( 'convertkit_setup' );

		// Check if any settings exist.
		// If they do, the Plugin has already been setup, so no need to show the setup screen.
		$settings = new ConvertKit_Settings();
		if ( $settings->has_api_key_and_secret() ) {
			return;
		}

		// Show the setup screen.
		wp_safe_redirect( admin_url( 'index.php?page=convertkit-setup' ) );
		exit;

	}

	/**
	 * Loads the setup screen if the request URL is for this class
	 *
	 * @since   1.9.8.5
	 */
	public function maybe_load_setup_screen() {

		// Bail if this isn't a request for the setup screen.
		if ( ! $this->is_setup_request() ) {
			return;
		}

		// Define current screen, so that calls to get_current_screen() tell Plugins which screen is loaded.
		set_current_screen( 'convertkit-setup' );

		// Load scripts and styles.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );

		// Output custom HTML for the setup screen.
		$this->output_header();
		$this->output_content();
		$this->output_footer();
		exit;

	}

	/**
	 * Enqueue CSS when viewing the Setup screen.
	 *
	 * @since   1.9.8.5
	 */
	public function enqueue_scripts() {

		// Enqueue Select2 JS.
		convertkit_select2_enqueue_scripts();

	}

	/**
	 * Enqueue CSS when viewing the setup screen.
	 *
	 * @since   1.9.8.5
	 */
	public function enqueue_styles() {

		// Enqueue WordPress default styles.
		wp_enqueue_style( 'common' );
		wp_enqueue_style( 'buttons' );
		wp_enqueue_style( 'forms' );

		// Enqueue Select2 CSS.
		convertkit_select2_enqueue_styles();

		// Enqueue styles for the setup wizard.
		wp_enqueue_style( 'convertkit-admin-setup', CONVERTKIT_PLUGIN_URL . 'resources/backend/css/setup.css', array(), CONVERTKIT_PLUGIN_VERSION );

	}

	/**
	 * Process posted form data, if any exists
	 *
	 * @since   1.9.8.5
	 *
	 * @return  mixed   WP_Error | bool
	 */
	private function process_form() {

		// Run security checks.
		if ( ! isset( $_POST['_wpnonce'] ) ) {
			return;
		}
		if ( ! wp_verify_nonce( sanitize_key( $_POST['_wpnonce'] ), 'convertkit-setup' ) ) {
			return new WP_Error( 'convertkit_setup_error', __( 'Invalid nonce specified.', 'convertkit' ) );
		}

		// Sanitize configuration.
		$configuration = array(
			'type'             => sanitize_text_field( stripslashes( $_POST['type'] ) ),
			'title'            => sanitize_text_field( stripslashes( $_POST['title'] ) ),
			'description'      => sanitize_textarea_field( stripslashes( $_POST['description'] ) ),
			'number_of_pages'  => absint( $_POST['number_of_pages'] ),
			'post_type'        => $this->post_type,
		);

	}

	/**
	 * Outputs the <head> and opening <body> tag for the standalone setup screen
	 *
	 * @since   1.9.8.5
	 */
	private function output_header() {

		// Remove scripts.
		remove_all_actions( 'admin_notices' );
		remove_all_actions( 'all_admin_notices' );

		// Enqueue scripts.
		do_action( 'admin_enqueue_scripts' );

		// Load header view.
		include_once CONVERTKIT_PLUGIN_PATH . '/views/backend/setup/header.php';

	}

	/**
	 * Outputs the HTML for the <body> section for the standalone setup screen
	 * and defines any form option data that might be needed.
	 *
	 * @since   1.9.8.5
	 */
	private function output_content() {

		// Load content view.
		include_once CONVERTKIT_PLUGIN_PATH . '/views/backend/setup/content.php';

	}

	/**
	 * Outputs the closing </body> and </html> tags, and runs some WordPress actions, for the standalone setup screen
	 *
	 * @since   1.9.8.5
	 */
	private function output_footer() {

		do_action( 'admin_footer', '' );
		do_action( 'admin_print_footer_scripts' );

		// Define variables for the content.php now.
		$back_button_url   = 'index.php';
		$back_button_label = __( 'Cancel', 'convertkit' );
		$next_button_label = __( 'Create members only content', 'convertkit' );

		// Load footer view.
		include_once CONVERTKIT_PLUGIN_PATH . '/views/backend/setup/footer.php';

	}

	/**
	 * Determines if the request is for the setup screen
	 *
	 * @since   1.9.8.5
	 *
	 * @return  bool    Is setup screen request
	 */
	private function is_setup_request() {

		// Don't load if this is an AJAX call.
		if ( wp_doing_ajax() || wp_doing_cron() ) {
			return false;
		}

		// Bail if we're not on the setup screen.
		if ( ! isset( $_GET['page'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return false;
		}
		if ( sanitize_text_field( $_GET['page'] ) !== 'convertkit-setup' ) { // phpcs:ignore WordPress.Security.NonceVerification
			return false;
		}

		return true;

	}

}
