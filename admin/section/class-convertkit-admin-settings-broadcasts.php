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
	 * @since   2.2.8
	 */
	public function __construct() {

		// Define the class that reads/writes settings.
		$this->settings = new ConvertKit_Settings_Broadcasts();

		// Define the settings key.
		$this->settings_key = $this->settings::SETTINGS_NAME;

		// Define the programmatic name, Title and Tab Text.
		$this->name     = 'broadcasts';
		$this->title    = __( 'Broadcasts to Posts', 'convertkit' );
		$this->tab_text = __( 'Broadcasts', 'convertkit' );

		// Identify that this is beta functionality.
		$this->is_beta = true;

		// Enable or disable the scheduled task when settings are saved.
		add_action( 'convertkit_settings_base_sanitize_settings', array( $this, 'schedule_or_unschedule_cron_event' ), 10, 2 );

		parent::__construct();

	}

	/**
	 * Schedules or unschedules the WordPress Cron event, based on whether
	 * the Broadcast to Post functionality's is enabled or disabled.
	 * 
	 * @since 	2.2.8
	 * 
	 * @param 	string 	$section 	Settings section.
	 * @param 	array 	$settings 	Settings.
	 */
	public function schedule_or_unschedule_cron_event( $section, $settings ) {

		// Bail if we're not on the Broadcasts section.
		if ( $section !== $this->name ) {
			return;
		}

		// Initialize resource class.
		$broadcasts = new ConvertKit_Resource_Broadcasts( 'cron' );

		// If the functionality is not enabled, unschedule the cron event.
		if ( $settings['enabled'] !== 'on' ) {
			$broadcasts->unschedule_cron_event();
			return;
		}

		// Schedule the cron event, which will import Broadcasts to WordPress Posts.
		// ConvertKit_Broadcasts_Importer::refresh() will then run when Broadcasts
		// are refreshed by the cron event.
		$broadcasts->schedule_cron_event();

	}

	/**
	 * Registers settings fields for this section.
	 *
	 * @since   2.2.8
	 */
	public function register_fields() {

		add_settings_field(
			'enabled',
			__( 'Enable', 'convertkit' ),
			array( $this, 'enable_callback' ),
			$this->settings_key,
			$this->name,
			array(
				'name'        => 'enabled',
				'description' => __( 'Enables automatic publication of ConvertKit Broadcasts to WordPress Posts.', 'convertkit' ),
			)
		);

	}

	/**
	 * Prints help info for this section
	 *
	 * @since   2.2.8
	 */
	public function print_section_info() {

		?>
		<span class="convertkit-beta-label"><?php esc_html_e( 'Beta', 'convertkit' ); ?></span>
		<p class="description"><?php esc_html_e( 'Defines whether broadcasts created in ConvertKit should automatically be published on this site as WordPress Posts.', 'convertkit' ); ?></p>
		<?php

	}


	/**
	 * Returns the URL for the ConvertKit documentation for this setting section.
	 *
	 * @since   2.2.8
	 *
	 * @return  string  Documentation URL.
	 */
	public function documentation_url() {

		return 'https://help.convertkit.com/en/articles/2502591-the-convertkit-wordpress-plugin';

	}

	/**
	 * Renders the input for the Enable setting.
	 *
	 * @since   2.2.8
	 *
	 * @param   array $args   Setting field arguments (name,description).
	 */
	public function enable_callback( $args ) {

		// Output field.
		echo $this->get_checkbox_field( // phpcs:ignore WordPress.Security.EscapeOutput
			$args['name'],
			'on',
			$this->settings->enabled(), // phpcs:ignore WordPress.Security.EscapeOutput
			$args['description'] // phpcs:ignore WordPress.Security.EscapeOutput
		);

	}

}

// Bootstrap.
add_action(
	'convertkit_admin_settings_register_sections',
	function( $sections ) {

		$sections['broadcasts'] = new ConvertKit_Admin_Settings_Broadcasts();
		return $sections;

	}
);
