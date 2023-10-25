<?php
/**
 * ConvertKit Settings General class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Registers General Settings that can be edited at Settings > ConvertKit > General.
 *
 * @package ConvertKit
 * @author ConvertKit
 */
class ConvertKit_Settings_General extends ConvertKit_Settings_Base {

	/**
	 * Holds the API instance.
	 *
	 * @since   1.9.6
	 *
	 * @var     ConvertKit_API
	 */
	private $api;

	/**
	 * Holds the ConvertKit Account Name.
	 *
	 * @since   1.9.6
	 *
	 * @var     bool|WP_Error|array
	 */
	private $account = false;

	/**
	 * Holds the ConvertKit Forms Resource.
	 *
	 * @since   1.9.6
	 *
	 * @var     bool|ConvertKit_Resource_Forms;
	 */
	private $forms = false;

	/**
	 * Constructor.
	 */
	public function __construct() {

		// Define the class that reads/writes settings.
		$this->settings = new ConvertKit_Settings();

		// Define the settings key.
		$this->settings_key = $this->settings::SETTINGS_NAME;

		// Define the programmatic name, Title and Tab Text.
		$this->name     = 'general';
		$this->title    = __( 'General Settings', 'convertkit' );
		$this->tab_text = __( 'General', 'convertkit' );

		// Enqueue scripts and CSS.
		add_action( 'convertkit_admin_settings_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'convertkit_admin_settings_enqueue_styles', array( $this, 'enqueue_styles' ) );

		// Render container element.
		add_action( 'convertkit_settings_base_render_before', array( $this, 'render_before' ) );

		parent::__construct();

	}

	/**
	 * Enqueues scripts for the Settings > General screen.
	 *
	 * @since   2.2.4
	 *
	 * @param   string $section    Settings section / tab (general|tools|restrict-content).
	 */
	public function enqueue_scripts( $section ) {

		// Bail if we're not on the general section.
		if ( $section !== $this->name ) {
			return;
		}

		// Enqueue Select2 JS.
		convertkit_select2_enqueue_scripts();

		// Enqueue Preview Output JS.
		wp_enqueue_script( 'convertkit-admin-preview-output', CONVERTKIT_PLUGIN_URL . 'resources/backend/js/preview-output.js', array( 'jquery' ), CONVERTKIT_PLUGIN_VERSION, true );

	}

	/**
	 * Enqueues styles for the Settings > General screen.
	 *
	 * @since   2.2.4
	 *
	 * @param   string $section    Settings section / tab (general|tools|restrict-content).
	 */
	public function enqueue_styles( $section ) {

		// Bail if we're not on the general section.
		if ( $section !== $this->name ) {
			return;
		}

		// Enqueue Select2 CSS.
		convertkit_select2_enqueue_styles();

	}

	/**
	 * Registers settings fields for this section.
	 */
	public function register_fields() {

		add_settings_field(
			'account_name',
			__( 'Account Name', 'convertkit' ),
			array( $this, 'account_name_callback' ),
			$this->settings_key,
			$this->name
		);

		add_settings_field(
			'api_key',
			__( 'API Key', 'convertkit' ),
			array( $this, 'api_key_callback' ),
			$this->settings_key,
			$this->name,
			array(
				'label_for' => 'api_key',
			)
		);

		add_settings_field(
			'api_secret',
			__( 'API Secret', 'convertkit' ),
			array( $this, 'api_secret_callback' ),
			$this->settings_key,
			$this->name,
			array(
				'label_for' => 'api_secret',
			)
		);

		foreach ( convertkit_get_supported_post_types() as $supported_post_type ) {
			// Get Post Type's Label.
			$post_type = get_post_type_object( $supported_post_type );

			// Skip if the Post Type doesn't exist.
			if ( ! $post_type ) {
				continue;
			}

			// Add Settings Field.
			add_settings_field(
				$supported_post_type . '_form',
				sprintf(
					/* translators: Post Type Name */
					__( 'Default Form (%s)', 'convertkit' ),
					$post_type->label
				),
				array( $this, 'custom_post_types_callback' ),
				$this->settings_key,
				$this->name,
				array(
					'label_for'        => '_wp_convertkit_settings_' . $supported_post_type . '_form',
					'post_type'        => $supported_post_type,
					'post_type_object' => $post_type,
				)
			);
		}

		add_settings_field(
			'non_inline_form',
			__( 'Default Non-Inline Form (Global)', 'convertkit' ),
			array( $this, 'non_inline_form_callback' ),
			$this->settings_key,
			$this->name,
			array(
				'label_for' => 'non_inline_form',
			)
		);

		add_settings_field(
			'debug',
			__( 'Debug', 'convertkit' ),
			array( $this, 'debug_callback' ),
			$this->settings_key,
			$this->name,
			array(
				'label_for' => 'debug',
			)
		);

		add_settings_field(
			'no_scripts',
			__( 'Disable JavaScript', 'convertkit' ),
			array( $this, 'no_scripts_callback' ),
			$this->settings_key,
			$this->name,
			array(
				'label_for' => 'no_scripts',
			)
		);

		add_settings_field(
			'no_css',
			__( 'Disable CSS', 'convertkit' ),
			array( $this, 'no_css_callback' ),
			$this->settings_key,
			$this->name,
			array(
				'label_for' => 'no_css',
			)
		);

	}

	/**
	 * Prints help info for this section
	 */
	public function print_section_info() {

		?>
		<p><?php esc_html_e( 'Choosing a default form will embed it at the bottom of every post or page (in single view only) across your site.', 'convertkit' ); ?></p>
		<p><?php esc_html_e( 'If you wish to turn off form embedding or select a different form for an individual post or page, you can do so using the ConvertKit meta box on the edit page.', 'convertkit' ); ?></p>
		<p>
			<?php
			printf(
				/* translators: [convertkit] shortcode, wrapped in <code> tags */
				esc_html__( 'The default form can be inserted into the middle of post or page content by using the %s shortcode.', 'convertkit' ),
				'<code>[convertkit]</code>'
			);
			?>
		</p>
		<?php

	}

	/**
	 * Returns the URL for the ConvertKit documentation for this setting section.
	 *
	 * @since   2.0.8
	 *
	 * @return  string  Documentation URL.
	 */
	public function documentation_url() {

		return 'https://help.convertkit.com/en/articles/2502591-the-convertkit-wordpress-plugin';

	}

	/**
	 * Renders container divs for styling, and attempts to fetch the ConvertKit Account
	 * details if API credentials have been specified.
	 *
	 * @since 1.9.6
	 */
	public function render_before() {

		// Initialize the API if an API Key and Secret is defined.
		if ( ! $this->settings->has_api_key_and_secret() ) {
			return;
		}

		$this->api = new ConvertKit_API(
			$this->settings->get_api_key(),
			$this->settings->get_api_secret(),
			$this->settings->debug_enabled(),
			'settings'
		);

		// Get Account Details, which we'll use in account_name_callback(), but also lets us test
		// whether the API credentials are valid.
		$this->account = $this->api->account();

		// Show an error message if Account Details could not be fetched e.g. API credentials supplied are invalid.
		if ( is_wp_error( $this->account ) ) {
			// Depending on the error code, maybe persist a notice in the WordPress Administration until the user
			// fixes the problem.
			switch ( $this->account->get_error_data( $this->account->get_error_code() ) ) {
				case 401:
					// API credentials are invalid.
					WP_ConvertKit()->get_class( 'admin_notices' )->add( 'authorization_failed' );
					break;
			}

			$this->output_error( $this->account->get_error_message() );
		} else {
			// Remove any existing persistent notice.
			WP_ConvertKit()->get_class( 'admin_notices' )->delete( 'authorization_failed' );
		}

	}

	/**
	 * Outputs the Account Name
	 *
	 * @since   1.9.6
	 */
	public function account_name_callback() {

		// Output a notice telling the user to enter their API Key and Secret if they haven't done so yet.
		if ( ! $this->settings->has_api_key_and_secret() || is_wp_error( $this->account ) ) {
			echo '<p class="description">' . esc_html__( 'Add a valid API Key and Secret to get started', 'convertkit' ) . '</p>';
			return;
		}

		// Output Account Name.
		$html  = sprintf(
			'<code>%s</code>',
			isset( $this->account['name'] ) ? esc_attr( $this->account['name'] ) : esc_html__( '(Not specified)', 'convertkit' )
		);
		$html .= '<p class="description">' . esc_html__( 'The name of your connected ConvertKit account.', 'convertkit' ) . '</p>';

		// Output has already been run through escaping functions above.
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput
	}

	/**
	 * Renders the input for the API Key setting.
	 *
	 * @since   1.9.6
	 */
	public function api_key_callback() {

		// If the API Key is stored as a constant, it cannot be edited here.
		if ( $this->settings->is_api_key_a_constant() ) {
			echo $this->get_masked_value( // phpcs:ignore WordPress.Security.EscapeOutput
				$this->settings->get_api_key(),
				esc_html__( 'Your API Key has been defined in your wp-config.php file. For security, it is not displayed here.', 'convertkit' )
			);
			return;
		}

		// Output field.
		echo $this->get_text_field( // phpcs:ignore WordPress.Security.EscapeOutput
			'api_key',
			esc_attr( $this->settings->get_api_key() ),
			array(
				sprintf(
					/* translators: %1$s: Link to ConvertKit Account */
					esc_html__( '%1$s Required for proper plugin function.', 'convertkit' ),
					'<a href="' . esc_url( convertkit_get_api_key_url() ) . '" target="_blank">' . esc_html__( 'Get your ConvertKit API Key.', 'convertkit' ) . '</a>'
				),
				sprintf(
					/* translators: Account, %1$s: wp-config.php, %2$s: <code> block for API Key definition */
					esc_html__( 'Alternatively specify your API Key in the %1$s file using %2$s', 'convertkit' ),
					'<code>wp-config.php</code>',
					'<code>define(\'CONVERTKIT_API_KEY\', \'your-api-key\');</code>'
				),
			),
			array( 'regular-text', 'code' )
		);

	}

	/**
	 * Renders the input for the API Secret setting.
	 *
	 * @since   1.9.6
	 */
	public function api_secret_callback() {

		// If the API Secret is stored as a constant, it cannot be edited here.
		if ( $this->settings->is_api_secret_a_constant() ) {
			echo $this->get_masked_value( // phpcs:ignore WordPress.Security.EscapeOutput
				$this->settings->get_api_secret(),
				esc_html__( 'Your API Secret has been defined in your wp-config.php file. For security, it is not displayed here.', 'convertkit' )
			);
			return;
		}

		// Output field.
		echo $this->get_text_field( // phpcs:ignore WordPress.Security.EscapeOutput
			'api_secret',
			esc_attr( $this->settings->get_api_secret() ),
			array(
				sprintf(
					/* translators: %1$s: Link to ConvertKit Account */
					esc_html__( '%1$s Required for proper plugin function.', 'convertkit' ),
					'<a href="' . esc_url( convertkit_get_api_key_url() ) . '" target="_blank">' . esc_html__( 'Get your ConvertKit API Secret.', 'convertkit' ) . '</a>'
				),
				sprintf(
					/* translators: Account, %1$s: wp-config.php, %2$s: <code> block for API Secret definition */
					esc_html__( 'Alternatively specify your API Secret in the %1$s file using %2$s', 'convertkit' ),
					'<code>wp-config.php</code>',
					'<code>define(\'CONVERTKIT_API_SECRET\', \'your-api-secret\');</code>'
				),
			),
			array( 'regular-text', 'code' )
		);

	}

	/**
	 * Renders the input for the Default Form setting for the given Post Type.
	 *
	 * @since  1.9.6
	 *
	 * @param   array $args  Field arguments.
	 */
	public function custom_post_types_callback( $args ) {

		// Refresh Forms.
		if ( ! $this->forms ) {
			$this->forms = new ConvertKit_Resource_Forms( 'settings' );
			$this->forms->refresh();

			// Also refresh Landing Pages, Tags and Posts. Whilst not displayed in the Plugin Settings, this ensures up to date
			// lists are stored for when editing e.g. Pages.
			$landing_pages = new ConvertKit_Resource_Landing_Pages( 'settings' );
			$landing_pages->refresh();

			$posts = new ConvertKit_Resource_Posts( 'settings' );
			$posts->refresh();

			$products = new ConvertKit_Resource_Products( 'settings' );
			$products->refresh();

			$tags = new ConvertKit_Resource_Tags( 'settings' );
			$tags->refresh();
		}

		// Bail if no Forms exist.
		if ( ! $this->forms->exist() ) {
			esc_html_e( 'No Forms exist in ConvertKit.', 'convertkit' );
			echo '<br /><a href="' . esc_url( convertkit_get_new_form_url() ) . '" target="_blank">' . esc_html__( 'Click here to create your first form', 'convertkit' ) . '</a>';
			return;
		}

		// Build array of select options.
		$options = array(
			'default' => esc_html__( 'None', 'convertkit' ),
		);
		foreach ( $this->forms->get() as $form ) {
			$options[ esc_attr( $form['id'] ) ] = esc_html( $form['name'] );
		}

		// Build description with preview link.
		$description = false;
		$preview_url = WP_ConvertKit()->get_class( 'preview_output' )->get_preview_form_url( $args['post_type'] );
		if ( $preview_url ) {
			// Include a preview link in the description.
			$description = sprintf(
				'%s %s %s',
				sprintf(
					/* translators: Post Type name, plural */
					esc_html__( 'Select a form above to automatically output below all %s.', 'convertkit' ),
					$args['post_type_object']->label
				),
				'<a href="' . esc_url( $preview_url ) . '" id="convertkit-preview-form-' . esc_attr( $args['post_type'] ) . '" target="_blank">' . esc_html__( 'Click here', 'convertkit' ) . '</a>',
				esc_html__( 'to preview how this will display.', 'convertkit' )
			);
		} else {
			// Just output the field's description.
			$description = sprintf(
				/* translators: Post Type name, plural */
				esc_html__( 'Select a form above to automatically output below all %s.', 'convertkit' ),
				$args['post_type_object']->label
			);
		}

		// Build field.
		$select_field = $this->get_select_field(
			$args['post_type'] . '_form',
			$this->settings->get_default_form( $args['post_type'] ),
			$options,
			$description,
			array(
				'convertkit-select2',
				'convertkit-preview-output-link',
			),
			array(
				'data-target' => '#convertkit-preview-form-' . esc_attr( $args['post_type'] ),
				'data-link'   => esc_attr( $preview_url ) . '&convertkit_form_id=',
			)
		);

		// Output field.
		echo '<div class="convertkit-select2-container">' . $select_field . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput

	}


	/**
	 * Renders the input for the Non-inline Form setting.
	 *
	 * @since  2.2.3
	 *
	 * @param   array $args  Field arguments.
	 */
	public function non_inline_form_callback( $args ) {

		// Bail if no non-inline Forms exist.
		if ( ! $this->forms->non_inline_exist() ) {
			esc_html_e( 'No non-inline Forms exist in ConvertKit.', 'convertkit' );
			echo '<br /><a href="' . esc_url( convertkit_get_new_form_url() ) . '" target="_blank">' . esc_html__( 'Click here to create your first modal, slide in or sticky bar form', 'convertkit' ) . '</a>';
			return;
		}

		// Build array of select options.
		$options = array(
			'' => esc_html__( 'None', 'convertkit' ),
		);
		foreach ( $this->forms->get_non_inline() as $form ) {
			$options[ esc_attr( $form['id'] ) ] = esc_html( $form['name'] );
		}

		// Build description with preview link.
		$preview_url = WP_ConvertKit()->get_class( 'preview_output' )->get_preview_form_home_url();
		$description = sprintf(
			'%s %s %s',
			esc_html__( 'Select a modal, slide in or sticky bar form to automatically display site wide.', 'convertkit' ),
			'<a href="' . esc_url( $preview_url ) . '" id="convertkit-preview-non-inline-form" target="_blank">' . esc_html__( 'Click here', 'convertkit' ) . '</a>',
			esc_html__( 'to preview how this will display.', 'convertkit' )
		);

		// Build field.
		$select_field = $this->get_select_field(
			'non_inline_form',
			$this->settings->get_non_inline_form(),
			$options,
			$description,
			array(
				'convertkit-select2',
				'convertkit-preview-output-link',
			),
			array(
				'data-target' => '#convertkit-preview-non-inline-form',
				'data-link'   => esc_url( $preview_url ) . '&convertkit_form_id=',
			)
		);

		// Output field.
		echo '<div class="convertkit-select2-container">' . $select_field . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput

	}

	/**
	 * Renders the input for the Debug setting.
	 *
	 * @since   1.9.6
	 */
	public function debug_callback() {

		// Output field.
		echo $this->get_checkbox_field( // phpcs:ignore WordPress.Security.EscapeOutput
			'debug',
			'on',
			$this->settings->debug_enabled(), // phpcs:ignore WordPress.Security.EscapeOutput
			esc_html__( 'Log requests to file and output browser console messages.', 'convertkit' ),
			esc_html__( 'You can ignore this unless you\'re working with our support team to resolve an issue. Decheck this option to improve performance.', 'convertkit' )
		);

	}

	/**
	 * Renders the input for the Disable Javascript setting.
	 *
	 * @since   1.9.6
	 */
	public function no_scripts_callback() {

		// Output field.
		echo $this->get_checkbox_field( // phpcs:ignore WordPress.Security.EscapeOutput
			'no_scripts',
			'on',
			$this->settings->scripts_disabled(), // phpcs:ignore WordPress.Security.EscapeOutput
			esc_html__( 'Prevent plugin from loading JavaScript files. This will disable the custom content and tagging features of the plugin. Does not apply to landing pages. Use with caution!', 'convertkit' )
		);

	}

	/**
	 * Renders the input for the Disable CSS setting.
	 *
	 * @since   1.9.6.9
	 */
	public function no_css_callback() {

		// Output field.
		echo $this->get_checkbox_field( // phpcs:ignore WordPress.Security.EscapeOutput
			'no_css',
			'on',
			$this->settings->css_disabled(), // phpcs:ignore WordPress.Security.EscapeOutput
			esc_html__( 'Prevents loading plugin CSS files. This will disable styling on broadcasts, form trigger buttons, product buttons and member\'s content. Use with caution!', 'convertkit' ),
			array(
				sprintf(
					'%s <a href="%s" target="_blank">%s</a>',
					esc_html__( 'To customize forms and their styling, use the', 'convertkit' ),
					esc_url( convertkit_get_form_editor_url() ),
					esc_html__( 'ConvertKit form editor', 'convertkit' )
				),
				sprintf(
					'%s <a href="https://wordpress.org/plugins/contact-form-7/" target="_blank">Contact Form 7</a>, <a href="https://wordpress.org/plugins/convertkit-gravity-forms/" target="_blank">Gravity Forms</a> %s <a href="https://wordpress.org/plugins/integrate-convertkit-wpforms/" target="_blank">WPForms</a> %s',
					esc_html__( 'For developers who require custom form designs through use of CSS, consider using the', 'convertkit' ),
					esc_html__( 'or', 'convertkit' ),
					esc_html__( 'integrations.', 'convertkit' )
				),
			)
		);

	}

}
