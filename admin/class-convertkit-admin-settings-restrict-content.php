<?php
/**
 * ConvertKit Settings Restrict Content class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Registers Restrict Content Settings that can be edited at Settings > Kit > Member's Content.
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

		// Permit Crawlers.
		add_settings_field(
			'permit_crawlers',
			__( 'Permit Search Engine Crawlers', 'convertkit' ),
			array( $this, 'permit_crawlers_callback' ),
			$this->settings_key,
			$this->name,
			array(
				'name'        => 'permit_crawlers',
				'label_for'   => 'permit_crawlers',
				'label'       => __( 'When enabled, search engine crawlers (such as Google and Bing) are able to access Member Content for indexing.', 'convertkit' ),
				'description' => '',
			)
		);

		// Restrict by Product.
		add_settings_field(
			'subscribe_heading',
			__( 'Product: Subscribe Heading', 'convertkit' ),
			array( $this, 'text_callback' ),
			$this->settings_key,
			$this->name,
			array(
				'name'        => 'subscribe_heading',
				'label_for'   => 'subscribe_heading',
				'description' => array(
					__( 'When a Page, Post or Custom Post\'s Member Content setting is set to a Kit Product, displays text in a heading explaining why the content is only available to subscribers.', 'convertkit' ),
				),
			)
		);

		add_settings_field(
			'subscribe_text',
			__( 'Product: Subscribe Text', 'convertkit' ),
			array( $this, 'textarea_callback' ),
			$this->settings_key,
			$this->name,
			array(
				'name'        => 'subscribe_text',
				'label_for'   => 'subscribe_text',
				'description' => array(
					__( 'When a Page, Post or Custom Post\'s Member Content setting is set to a Kit Product, displays text explaining why the content is only available to subscribers.', 'convertkit' ),
				),
			)
		);

		// Restrict by Tag.
		add_settings_field(
			'subscribe_heading_tag',
			__( 'Tag: Subscribe Heading', 'convertkit' ),
			array( $this, 'text_callback' ),
			$this->settings_key,
			$this->name,
			array(
				'name'        => 'subscribe_heading_tag',
				'label_for'   => 'subscribe_heading_tag',
				'description' => array(
					__( 'When a Page, Post or Custom Post\'s Member Content setting is set to a Kit Tag, displays text in a heading explaining why the content is only available to subscribers.', 'convertkit' ),
				),
			)
		);

		add_settings_field(
			'subscribe_text_tag',
			__( 'Tag: Subscribe Text', 'convertkit' ),
			array( $this, 'textarea_callback' ),
			$this->settings_key,
			$this->name,
			array(
				'name'        => 'subscribe_text_tag',
				'label_for'   => 'subscribe_text_tag',
				'description' => array(
					__( 'When a Page, Post or Custom Post\'s Member Content setting is set to a Kit Tag, displays text explaining why the content is only available to subscribers.', 'convertkit' ),
				),
			)
		);

		// All.
		add_settings_field(
			'subscribe_button_label',
			__( 'Subscribe Button Label', 'convertkit' ),
			array( $this, 'text_callback' ),
			$this->settings_key,
			$this->name,
			array(
				'name'        => 'subscribe_button_label',
				'label_for'   => 'subscribe_button_label',
				'description' => array(
					__( 'The text to display for the call to action button to subscribe.', 'convertkit' ),
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
				'label_for'   => 'email_text',
				'description' => array(
					__( 'The text to display asking if the subscriber has already subscribed.', 'convertkit' ),
				),
			)
		);

		add_settings_field(
			'email_heading',
			__( 'Email Heading', 'convertkit' ),
			array( $this, 'text_callback' ),
			$this->settings_key,
			$this->name,
			array(
				'name'        => 'email_heading',
				'label_for'   => 'email_heading',
				'description' => array(
					__( 'The heading to display above the email field, directing the subscriber to log in.', 'convertkit' ),
				),
			)
		);

		add_settings_field(
			'email_description_text',
			__( 'Email Field Description', 'convertkit' ),
			array( $this, 'text_callback' ),
			$this->settings_key,
			$this->name,
			array(
				'name'        => 'email_description_text',
				'label_for'   => 'email_description_text',
				'description' => array(
					__( 'The text to display below the email field, explaining the subscriber will receive a code by email.', 'convertkit' ),
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
				'label_for'   => 'email_button_label',
				'description' => array(
					__( 'The text to display for the button to submit the subscriber\'s email address and receive a login link to access the member-only content.', 'convertkit' ),
				),
			)
		);

		add_settings_field(
			'email_check_heading',
			__( 'Email Check Heading', 'convertkit' ),
			array( $this, 'text_callback' ),
			$this->settings_key,
			$this->name,
			array(
				'name'        => 'email_check_heading',
				'label_for'   => 'email_check_heading',
				'description' => array(
					__( 'The heading to display telling the subscriber an email with a log in code was just sent.', 'convertkit' ),
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
				'label_for'   => 'email_check_text',
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
				'label_for'   => 'no_access_text',
				'description' => array(
					__( 'The text to display for a subscriber who authenticates via the login link, but is not subscribed.', 'convertkit' ),
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
		<p class="description"><?php esc_html_e( 'Defines the text and button labels to display when a Page, Post or Custom Post has its Member Content setting defined.', 'convertkit' ); ?></p>
		<div class="notice notice-warning">
			<p>
				<?php
				printf(
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

		return 'https://help.kit.com/en/articles/2502591-the-convertkit-wordpress-plugin';

	}

	/**
	 * Renders the input for the Enable setting.
	 *
	 * @since   2.4.1
	 *
	 * @param   array $args   Setting field arguments (name,description).
	 */
	public function permit_crawlers_callback( $args ) {

		// Output field.
		echo $this->get_checkbox_field( // phpcs:ignore WordPress.Security.EscapeOutput
			$args['name'],
			'on',
			$this->settings->permit_crawlers(), // phpcs:ignore WordPress.Security.EscapeOutput
			$args['label'],  // phpcs:ignore WordPress.Security.EscapeOutput
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
			)
		);

	}

	/**
	 * Renders the input for the textarea setting.
	 *
	 * @since   2.3.5
	 *
	 * @param   array $args   Setting field arguments (name,description).
	 */
	public function textarea_callback( $args ) {

		// Output field.
		echo $this->get_textarea_field( // phpcs:ignore WordPress.Security.EscapeOutput
			$args['name'],
			esc_attr( $this->settings->get_by_key( $args['name'] ) ),
			$args['description'], // phpcs:ignore WordPress.Security.EscapeOutput
			array(
				'widefat',
			)
		);

	}

}

// Bootstrap.
add_action(
	'convertkit_admin_settings_register_sections',
	function ( $sections ) {

		$sections['restrict-content'] = new ConvertKit_Admin_Settings_Restrict_Content();
		return $sections;

	}
);
