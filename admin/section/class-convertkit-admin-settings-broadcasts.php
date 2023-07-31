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
	 * @since   2.2.8
	 *
	 * @param   string $section    Settings section.
	 * @param   array  $settings   Settings.
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

		add_settings_field(
			'category',
			__( 'Category', 'convertkit' ),
			array( $this, 'category_callback' ),
			$this->settings_key,
			$this->name,
			array(
				'name'        => 'category',
				'description' => __( 'The category to assign imported Broadcasts to.', 'convertkit' ),
			)
		);

		add_settings_field(
			'send_at_min_date',
			__( 'Earliest Date', 'convertkit' ),
			array( $this, 'date_callback' ),
			$this->settings_key,
			$this->name,
			array(
				'name'        => 'send_at_min_date',
				'description' => __( 'The earliest date to import Broadcasts from, based on the Broadcast\'s sent date and time.', 'convertkit' ),
			)
		);

		add_settings_field(
			'restrict_content',
			__( 'Member Content', 'convertkit' ),
			array( $this, 'restrict_content_callback' ),
			$this->settings_key,
			$this->name,
			array(
				'name'        => 'restrict_content',
				'description' => __( 'For Broadcasts marked as "paid subscribers only" in ConvertKit, select the ConvertKit product that the visitor must be subscribed to, permitting them access to view this members only content.', 'convertkit' ),
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

	/**
	 * Renders the input for the category setting.
	 *
	 * @since   2.2.8
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
				'name'             => $args['name'],
				'id'               => $args['name'],
				'class'            => 'convertkit-select2',
				'selected'         => $this->settings->get_by_key( $args['name'] ),
				'taxonomy'         => 'category',
			)
		);

		// Output field.
		echo '<div class="convertkit-select2-container">' . $select_field . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput

	}

	/**
	 * Renders the input for the date setting.
	 *
	 * @since   2.2.8
	 *
	 * @param   array $args   Setting field arguments (name,description).
	 */
	public function date_callback( $args ) {

		// Output field.
		echo $this->get_date_field( // phpcs:ignore WordPress.Security.EscapeOutput
			$args['name'],
			esc_attr( $this->settings->get_by_key( $args['name'] ) ),
			$args['description'], // phpcs:ignore WordPress.Security.EscapeOutput
			array(
				'enabled',
			)
		);

	}

	/**
	 * Renders the input for the Member Content setting.
	 *
	 * @since  2.2.8
	 *
	 * @param   array $args  Field arguments.
	 */
	public function restrict_content_callback( $args ) {

		// Refresh Products.
		$products = new ConvertKit_Resource_Products( 'settings' );
		$products->refresh();

		// Bail if no Forms exist.
		if ( ! $products->exist() ) {
			esc_html_e( 'No Products exist in ConvertKit.', 'convertkit' );
			echo '<br /><a href="' . esc_url( convertkit_get_new_form_url() ) . '" target="_blank">' . esc_html__( 'Click here to create your first Product.', 'convertkit' ) . '</a>';
			return;
		}

		// Build array of select options.
		$options = array(
			'0' => esc_html__( 'Don\'t restrict content to members only.', 'convertkit' ),
		);
		foreach ( $products->get() as $product ) {
			// Prefix of 'product_' is deliberate; we may support restricting content by tag in the future,
			// and therefore need to denote the resource ID's type (product, tag etc).
			$options[ 'product_' . esc_attr( $product['id'] ) ] = esc_html( $product['name'] );
		}

		// Build field.
		$select_field = $this->get_select_field(
			$args['name'],
			esc_attr( $this->settings->get_by_key( $args['name'] ) ),
			$options,
			$args['description'],
			array(
				'convertkit-select2',
			)
		);

		// Output field.
		echo '<div class="convertkit-select2-container">' . $select_field . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput

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
