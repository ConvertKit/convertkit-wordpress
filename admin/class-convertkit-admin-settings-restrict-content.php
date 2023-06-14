<?php
/**
 * ConvertKit Settings Restrict Content class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Registers Restrict Content Settings that can be edited at Settings > ConvertKit > Member's Content.
 *
 * @package ConvertKit
 * @author ConvertKit
 */
class ConvertKit_Admin_Settings_Restrict_Content extends ConvertKit_Settings_Base {

	/**
	 * Constructor.
	 *
	 * @since   2.1.0
	 */
	public function __construct() {

		// Define the class that reads/writes settings.
		$this->settings = new ConvertKit_Settings_Restrict_Content();

		// Define the settings key.
		$this->settings_key = $this->settings::SETTINGS_NAME;

		// Define the programmatic name, Title and Tab Text.
		$this->name     = 'restrict-content';
		$this->title    = __( 'Member Content', 'convertkit' );
		$this->tab_text = __( 'Member Content', 'convertkit' );

		// Identify that this is beta functionality.
		$this->is_beta = true;

		// Enqueue scripts.
		add_action( 'convertkit_admin_settings_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		parent::__construct();

	}

	/**
	 * Enqueues scripts for the Settings > Member's Content screen.
	 *
	 * @since   2.2.4
	 *
	 * @param   string $section    Settings section / tab (general|tools|restrict-content).
	 */
	public function enqueue_scripts( $section ) {

		// Bail if we're not on the Member's Content section.
		if ( $section !== $this->name ) {
			return;
		}

		// Enqueue JS.
		wp_enqueue_script( 'convertkit-admin-settings-conditional-display', CONVERTKIT_PLUGIN_URL . 'resources/backend/js/settings-conditional-display.js', array( 'jquery' ), CONVERTKIT_PLUGIN_VERSION, true );

	}

	/**
	 * Registers settings fields for this section.
	 *
	 * @since   2.1.0
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
				'description' => __( 'Enables the Member Content functionality, displaying configuration options on pages to require a subscription to a ConvertKit product', 'convertkit' ),
			)
		);

		add_settings_field(
			'subscribe_text',
			__( 'Subscribe Text', 'convertkit' ),
			array( $this, 'text_callback' ),
			$this->settings_key,
			$this->name,
			array(
				'name'        => 'subscribe_text',
				'description' => array(
					__( 'The text to display above the subscribe button, explaining why the content is only available to subscribers.', 'convertkit' ),
				),
			)
		);

		add_settings_field(
			'subscribe_button_label',
			__( 'Subscribe Button Label', 'convertkit' ),
			array( $this, 'text_callback' ),
			$this->settings_key,
			$this->name,
			array(
				'name'        => 'subscribe_button_label',
				'description' => array(
					__( 'The text to display for the call to action button to subscribe to the ConvertKit product.', 'convertkit' ),
				),
			)
		);

		add_settings_field(
			'email_text',
			__( 'Email Text', 'convertkit' ),
			array( $this, 'text_callback' ),
			$this->settings_key,
			$this->name,
			array(
				'name'        => 'email_text',
				'description' => array(
					__( 'The text to display above the email form, instructing the subscriber to enter their email address to receive a login link to access the member\'s only content.', 'convertkit' ),
				),
			)
		);

		add_settings_field(
			'email_button_label',
			__( 'Email Button Label', 'convertkit' ),
			array( $this, 'text_callback' ),
			$this->settings_key,
			$this->name,
			array(
				'name'        => 'email_button_label',
				'description' => array(
					__( 'The text to display for the button to submit the subscriber\'s email address and receive a login link to access the member only content.', 'convertkit' ),
				),
			)
		);

		add_settings_field(
			'email_check_text',
			__( 'Email Check Text', 'convertkit' ),
			array( $this, 'text_callback' ),
			$this->settings_key,
			$this->name,
			array(
				'name'        => 'email_check_text',
				'description' => array(
					__( 'The text to display instructing the subscriber to check their email for the login link that was sent.', 'convertkit' ),
				),
			)
		);

		add_settings_field(
			'no_access_text',
			__( 'No Access Text', 'convertkit' ),
			array( $this, 'text_callback' ),
			$this->settings_key,
			$this->name,
			array(
				'name'        => 'no_access_text',
				'description' => array(
					__( 'The text to display for a subscriber who authenticates via the login link, but does not have access to the product.', 'convertkit' ),
				),
			)
		);

	}

	/**
	 * Prints help info for this section
	 *
	 * @since   2.1.0
	 */
	public function print_section_info() {

		?>
		<span class="convertkit-beta-label"><?php esc_html_e( 'Beta', 'convertkit' ); ?></span>
		<p class="description"><?php esc_html_e( 'Defines the text and button labels to display when a Page, Post or Custom Post has its Member Content setting set to a Product, and the visitor has not authenticated/subscribed.', 'convertkit' ); ?></p>
		<div class="notice notice-warning">
			<p>
				<?php
				echo sprintf(
					'%s %s %s',
					esc_html__( 'If your web host has caching configured (or you are using a caching plugin), you must configure it to disable caching when the', 'convertkit' ),
					'<code>ck_subscriber_id</code>',
					esc_html__( 'cookie is present. Failing to do so will result in errors.', 'convertkit' )
				);
				?>
			</p>
		</div>
		<?php

	}


	/**
	 * Returns the URL for the ConvertKit documentation for this setting section.
	 *
	 * @since   2.1.0
	 *
	 * @return  string  Documentation URL.
	 */
	public function documentation_url() {

		return 'https://help.convertkit.com/en/articles/2502591-the-convertkit-wordpress-plugin';

	}

	/**
	 * Renders the input for the Enable setting.
	 *
	 * @since   2.1.0
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
	 * Renders the input for the text setting.
	 *
	 * @since   2.1.0
	 *
	 * @param   array $args   Setting field arguments (name,description).
	 */
	public function text_callback( $args ) {

		// Output field.
		echo $this->get_text_field( // phpcs:ignore WordPress.Security.EscapeOutput
			$args['name'],
			esc_attr( $this->settings->get_by_key( $args['name'] ) ),
			$args['description'], // phpcs:ignore WordPress.Security.EscapeOutput
			array(
				'widefat',
				'enabled',
			)
		);

	}

}

// Bootstrap.
add_action(
	'convertkit_admin_settings_register_sections',
	function( $sections ) {

		$sections['restrict-content'] = new ConvertKit_Admin_Settings_Restrict_Content();
		return $sections;

	}
);
