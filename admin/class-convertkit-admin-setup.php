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
	 * The current step in the setup process the user is on.
	 * 
	 * @since 	1.9.8.5
	 * 
	 * @var 	int
	 */
	public $step = 1;

	/**
	 * Registers action and filter hooks.
	 *
	 * @since   1.9.8.5
	 */
	public function __construct() {

		// Define the step, if provided.
		if ( isset( $_REQUEST['step'] ) ) {
			$this->step = absint( $_REQUEST['step'] );
		}

		// Define details for each step in the setup process.
		$this->steps = array(
			1 => array(
				'name' 			=> __( 'Setup', 'convertkit' ),
				'back_button'   => array(
					'url' 	=> 'index.php',
					'label' => __( 'Exit Wizard', 'convertkit' ),
				),
			),
			2 => array(
				'name' 				=> __( 'Connect Account', 'convertkit' ),
				'back_button'   => array(
					'url' 	=> 'index.php',
					'label' => __( 'Exit Wizard', 'convertkit' ),
				),
				'next_button'   => array(
					'label' => __( 'Connect', 'convertkit' ),
				),
			),
			3 => array(
				'name' 				=> __( 'Form Configuration', 'convertkit' ),
				
				'next_button_label' => __( 'Exit Wizard', 'convertkit' ),
			),
			4 => array(
				'name' 				=> __( 'Done', 'convertkit' ),
				'next_button_label' => __( 'Exit Wizard', 'convertkit' ),
			),
		);

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

		// Process any posted form data.
		$this->process_form();

		// Load any data for the step.
		switch ( $this->step ) {
			case 3:
				// Fetch Forms.
				$this->forms = new ConvertKit_Resource_Forms();
				$this->forms->refresh();
				break;
		}

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
	 * Process posted data from the setup form, if any exists
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
			$this->error = __( 'Invalid nonce specified.', 'convertkit' );
			return;
		}

		// Depending on the step, process the form data.
		switch ( $this->step ) {
			case 3:
				// Check that the API Key and Secret work.
				$api_key = sanitize_text_field( wp_unslash( $_POST['api_key'] ) );
				$api_secret = sanitize_text_field( wp_unslash( $_POST['api_secret'] ) );
				
				$api = new ConvertKit_API( $api_key, $api_secret );
				$result = $api->account();

				// Show an error message if Account Details could not be fetched e.g. API credentials supplied are invalid.
				if ( is_wp_error( $result ) ) {
					// Decrement the step.
					$this->step = ( $this->step - 1 );
					$this->error = $result->get_error_message();
					return;
				}

				// If here, API credentials are valid.
				// Save them.
				$settings = new ConvertKit_Settings;
				$settings->save( array(
					'api_key' => $api_key,
					'api_secret' => $api_secret,
				) );
				break;
		}

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
		include_once CONVERTKIT_PLUGIN_PATH . '/views/backend/setup/content-' . $this->step . '.php';

	}

	/**
	 * Outputs the closing </body> and </html> tags, and runs some WordPress actions, for the standalone setup screen
	 *
	 * @since   1.9.8.5
	 */
	private function output_footer() {

		do_action( 'admin_footer', '' );
		do_action( 'admin_print_footer_scripts' );

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
