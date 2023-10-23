<?php
/**
 * ConvertKit Settings Broadcasts class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Registers Broadcasts Settings that can be edited at Settings > ConvertKit > Broadcasts.
 *
 * @package ConvertKit
 * @author ConvertKit
 */
class ConvertKit_Admin_Settings_Broadcasts extends ConvertKit_Settings_Base {

	/**
	 * Constructor.
	 *
	 * @since   2.2.9
	 */
	public function __construct() {

		// Define the class that reads/writes settings.
		$this->settings = new ConvertKit_Settings_Broadcasts();

		// Define the settings key.
		$this->settings_key = $this->settings::SETTINGS_NAME;

		// Define the programmatic name, Title and Tab Text.
		$this->name     = 'broadcasts';
		$this->title    = __( 'Broadcasts', 'convertkit' );
		$this->tab_text = __( 'Broadcasts', 'convertkit' );

		// Identify that this is beta functionality.
		$this->is_beta = true;

		// Output notices.
		add_action( 'convertkit_settings_base_render_before', array( $this, 'maybe_output_notices' ) );

		// Enqueue scripts and CSS.
		add_action( 'convertkit_admin_settings_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'convertkit_admin_settings_enqueue_styles', array( $this, 'enqueue_styles' ) );

		parent::__construct();

		$this->maybe_import_now();

	}

	/**
	 * Import Broadcasts now, if requested through the UI.
	 *
	 * @since   2.2.9
	 */
	private function maybe_import_now() {

		// Bail if nonce verification fails.
		if ( ! isset( $_REQUEST['_convertkit_settings_broadcasts_nonce'] ) ) {
			return false;
		}
		if ( ! wp_verify_nonce( sanitize_key( $_REQUEST['_convertkit_settings_broadcasts_nonce'] ), 'convertkit-settings-broadcasts' ) ) {
			return false;
		}

		// Run the import task through WordPress' Cron system now.
		$cron   = new ConvertKit_Cron();
		$result = $cron->run( 'convertkit_resource_refresh_posts' );

		// If an error occured, show it now.
		if ( is_wp_error( $result ) ) {
			// Redirect to Broadcasts screen.
			$this->redirect( 'broadcast_import_error' );
			return;
		}

		// If here, the task scheduled.
		$this->redirect( false, 'broadcast_import_success' );

	}

	/**
	 * Enqueues scripts for the Settings > Broadcasts screen.
	 *
	 * @since   2.2.9
	 *
	 * @param   string $section    Settings section / tab (general|tools|restrict-content|broadcasts).
	 */
	public function enqueue_scripts( $section ) {

		// Bail if we're not on the Broadcasts section.
		if ( $section !== $this->name ) {
			return;
		}

		// Enqueue Select2 JS.
		convertkit_select2_enqueue_scripts();

		// Enqueue JS.
		wp_enqueue_script( 'convertkit-admin-settings-conditional-display', CONVERTKIT_PLUGIN_URL . 'resources/backend/js/settings-conditional-display.js', array( 'jquery' ), CONVERTKIT_PLUGIN_VERSION, true );

	}

	/**
	 * Enqueues styles for the Settings > General screen.
	 *
	 * @since   2.2.9
	 *
	 * @param   string $section    Settings section / tab (general|tools|restrict-content|broadcasts).
	 */
	public function enqueue_styles( $section ) {

		// Bail if we're not on the Broadcasts section.
		if ( $section !== $this->name ) {
			return;
		}

		// Enqueue Select2 CSS.
		convertkit_select2_enqueue_styles();

	}

	/**
	 * Outputs success and/or error notices if required.
	 *
	 * @since   2.2.9
	 */
	public function maybe_output_notices() {

		// Define messages that might be displayed as a notification.
		$messages = array(
			'broadcast_import_error'   => __( 'Broadcasts import failed. Please try again.', 'convertkit' ),
			'broadcast_import_success' => __( 'Broadcasts import started. Check the Posts screen shortly to confirm Broadcasts imported successfully.', 'convertkit' ),
		);

		// Output error notification if defined.
		if ( isset( $_REQUEST['error'] ) && array_key_exists( sanitize_text_field( $_REQUEST['error'] ), $messages ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			$this->output_error( $messages[ sanitize_text_field( $_REQUEST['error'] ) ] ); // phpcs:ignore WordPress.Security.NonceVerification
		}

		// Output success notification if defined.
		if ( isset( $_REQUEST['success'] ) && array_key_exists( sanitize_text_field( $_REQUEST['success'] ), $messages ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			$this->output_success( $messages[ sanitize_text_field( $_REQUEST['success'] ) ] ); // phpcs:ignore WordPress.Security.NonceVerification
		}

	}

	/**
	 * Registers settings fields for this section.
	 *
	 * @since   2.2.9
	 */
	public function register_fields() {

		// Initialize classes that will be used.
		$posts = new ConvertKit_Resource_Posts( 'cron' );

		// Define description for the 'Enabled' setting.
		// If enabled, include the next scheduled date and time the Plugin will import broadcasts.
		$enabled_description = '';
		if ( $this->settings->enabled() && $posts->get_cron_event_next_scheduled() ) {
			$enabled_description = sprintf(
				'%s %s',
				esc_html__( 'Broadcasts will next import at approximately ', 'convertkit' ),
				// The cron event's next scheduled timestamp is always in UTC.
				// Display it converted to the WordPress site's timezone.
				get_date_from_gmt(
					gmdate( 'Y-m-d H:i:s', $posts->get_cron_event_next_scheduled() ),
					get_option( 'date_format' ) . ' ' . get_option( 'time_format' )
				)
			);
		}

		add_settings_field(
			'enabled',
			__( 'Enable Automatic Import', 'convertkit' ),
			array( $this, 'enable_callback' ),
			$this->settings_key,
			$this->name,
			array(
				'name'        => 'enabled',
				'label_for'   => 'enabled',
				'label'       => __( 'Enables automatic publication of public ConvertKit Broadcasts as WordPress Posts.', 'convertkit' ),
				'description' => $enabled_description,
			)
		);

		// Render import button if the feature is enabled.
		if ( $this->settings->enabled() && $posts->get_cron_event_next_scheduled() ) {
			add_settings_field(
				'import_button',
				'',
				array( $this, 'import_button_callback' ),
				$this->settings_key,
				$this->name
			);
		}

		add_settings_field(
			'post_status',
			__( 'Status', 'convertkit' ),
			array( $this, 'post_status_callback' ),
			$this->settings_key,
			$this->name,
			array(
				'name'        => 'post_status',
				'label_for'   => 'post_status',
				'description' => __( 'The WordPress Post status to assign imported broadcasts to.', 'convertkit' ),
			)
		);

		add_settings_field(
			'category_id',
			__( 'Category', 'convertkit' ),
			array( $this, 'category_callback' ),
			$this->settings_key,
			$this->name,
			array(
				'name'        => 'category_id',
				'label_for'   => 'category_id',
				'description' => __( 'The category to assign imported broadcasts to.', 'convertkit' ),
			)
		);

		add_settings_field(
			'published_at_min_date',
			__( 'Earliest Date', 'convertkit' ),
			array( $this, 'date_callback' ),
			$this->settings_key,
			$this->name,
			array(
				'name'        => 'published_at_min_date',
				'label_for'   => 'published_at_min_date',
				'description' => __( 'The earliest date to import broadcasts from, based on the broadcast\'s published date and time.', 'convertkit' ),
			)
		);

		add_settings_field(
			'enabled_export',
			__( 'Enable Export Actions', 'convertkit' ),
			array( $this, 'enable_export_callback' ),
			$this->settings_key,
			$this->name,
			array(
				'name'      => 'enabled_export',
				'label_for' => 'enabled_export',
				'label'     => __( 'Displays actions in WordPress to create draft broadcasts from existing WordPress posts.', 'convertkit' ),
			)
		);

		add_settings_field(
			'no_styles',
			__( 'Disable Styles', 'convertkit' ),
			array( $this, 'no_styles_callback' ),
			$this->settings_key,
			$this->name,
			array(
				'name'        => 'no_styles',
				'label_for'   => 'no_styles',
				'description' => __( 'Removes inline styles and layout when importing broadcasts and exporting posts.', 'convertkit' ),
			)
		);

	}

	/**
	 * Prints help info for this section
	 *
	 * @since   2.2.9
	 */
	public function print_section_info() {

		?>
		<span class="convertkit-beta-label"><?php esc_html_e( 'Beta', 'convertkit' ); ?></span>
		<p class="description"><?php esc_html_e( 'Defines whether public broadcasts created in ConvertKit should automatically be published on this site as WordPress Posts, and whether to enable options to create draft ConvertKit Broadcasts from WordPress Posts.', 'convertkit' ); ?></p>
		<?php

	}


	/**
	 * Returns the URL for the ConvertKit documentation for this setting section.
	 *
	 * @since   2.2.9
	 *
	 * @return  string  Documentation URL.
	 */
	public function documentation_url() {

		return 'https://help.convertkit.com/en/articles/2502591-the-convertkit-wordpress-plugin';

	}

	/**
	 * Renders the input for the Enable setting.
	 *
	 * @since   2.2.9
	 *
	 * @param   array $args   Setting field arguments (name,description).
	 */
	public function enable_callback( $args ) {

		// Output field.
		echo $this->get_checkbox_field( // phpcs:ignore WordPress.Security.EscapeOutput
			$args['name'],
			'on',
			$this->settings->enabled(), // phpcs:ignore WordPress.Security.EscapeOutput
			$args['label'],  // phpcs:ignore WordPress.Security.EscapeOutput
			$args['description'] // phpcs:ignore WordPress.Security.EscapeOutput
		);

	}

	/**
	 * Renders the import button.
	 *
	 * @since   2.2.9
	 */
	public function import_button_callback() {

		// Define link to import Broadcasts now.
		$import_url = add_query_arg(
			array(
				'page'                                  => '_wp_convertkit_settings',
				'tab'                                   => 'broadcasts',
				'_convertkit_settings_broadcasts_nonce' => wp_create_nonce( 'convertkit-settings-broadcasts' ),
			),
			'options-general.php'
		);

		echo '<a href="' . esc_url( $import_url ) . '" class="button button-secondary enabled">' . esc_html__( 'Import now', 'convertkit' ) . '</a>';

	}

	/**
	 * Renders the input for the status setting.
	 *
	 * @since   2.3.4
	 *
	 * @param   array $args   Setting field arguments (name,description).
	 */
	public function post_status_callback( $args ) {

		// Build field.
		$select_field = $this->get_select_field(
			$args['name'],
			$this->settings->post_status(),
			get_post_statuses(),
			$args['description'],
			array(
				'enabled',
				'convertkit-select2',
			)
		);

		// Output field.
		echo '<div class="convertkit-select2-container">' . $select_field . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput

	}

	/**
	 * Renders the input for the category setting.
	 *
	 * @since   2.2.9
	 *
	 * @param   array $args   Setting field arguments (name,description).
	 */
	public function category_callback( $args ) {

		// Build field.
		$select_field = wp_dropdown_categories(
			array(
				'show_option_none' => __( 'None', 'convertkit' ),
				'echo'             => 0,
				'hierarhical'      => 1,
				'name'             => $this->settings_key . '[' . $args['name'] . ']',
				'id'               => $this->settings_key . '_' . $args['name'],
				'class'            => 'convertkit-select2 enabled',
				'selected'         => $this->settings->category_id(),
				'taxonomy'         => 'category',
				'hide_empty'       => false,
			)
		);

		// Output field.
		echo '<div class="convertkit-select2-container">' . $select_field . '</div>' . $this->get_description( $args['description'] ); // phpcs:ignore WordPress.Security.EscapeOutput

	}

	/**
	 * Renders the input for the date setting.
	 *
	 * @since   2.2.9
	 *
	 * @param   array $args   Setting field arguments (name,description).
	 */
	public function date_callback( $args ) {

		// Output field.
		echo $this->get_date_field( // phpcs:ignore WordPress.Security.EscapeOutput
			$args['name'],
			esc_attr( $this->settings->published_at_min_date() ),
			$args['description'], // phpcs:ignore WordPress.Security.EscapeOutput
			array(
				'enabled',
			)
		);

	}

	/**
	 * Renders the input for the Enable Export setting.
	 *
	 * @since   2.4.0
	 *
	 * @param   array $args   Setting field arguments (name,description).
	 */
	public function enable_export_callback( $args ) {

		// Output field.
		echo $this->get_checkbox_field( // phpcs:ignore WordPress.Security.EscapeOutput
			$args['name'],
			'on',
			$this->settings->enabled_export(), // phpcs:ignore WordPress.Security.EscapeOutput
			$args['label']  // phpcs:ignore WordPress.Security.EscapeOutput
		);

	}

	/**
	 * Renders the input for the No Styles setting.
	 *
	 * @since   2.2.9
	 *
	 * @param   array $args   Setting field arguments (name,description).
	 */
	public function no_styles_callback( $args ) {

		// Output field.
		echo $this->get_checkbox_field( // phpcs:ignore WordPress.Security.EscapeOutput
			$args['name'],
			'on',
			$this->settings->no_styles(), // phpcs:ignore WordPress.Security.EscapeOutput
			$args['description'] // phpcs:ignore WordPress.Security.EscapeOutput
		);

	}

}

// Bootstrap.
add_action(
	'convertkit_admin_settings_register_sections',
	function ( $sections ) {

		$sections['broadcasts'] = new ConvertKit_Admin_Settings_Broadcasts();
		return $sections;

	}
);
