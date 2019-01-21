<?php
/**
 * ConvertKit General Settings class
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Class ConvertKit_Settings_General
 */
class ConvertKit_Settings_General extends ConvertKit_Settings_Base {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->settings_key = WP_ConvertKit::SETTINGS_PAGE_SLUG;
		$this->name         = 'general';
		$this->title        = __( 'General Settings', 'convertkit' );
		$this->tab_text     = __( 'General', 'convertkit' );

		add_action( 'wp_ajax_ck_refresh_forms', array( $this, 'refresh_resources' ) );

		parent::__construct();
	}

	/**
	 * Refreshing Resources on AJAX
	 */
	public function refresh_resources() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'You don\'t have enough permissions.', 'convertkit' ) );
			wp_die();
		}

		$api_key = isset( $_REQUEST['api_key'] ) ? $_REQUEST['api_key'] : WP_ConvertKit::get_api_key();

		if ( ! $api_key ) {
			update_option( 'convertkit_forms', array() );
			wp_send_json_error( __( 'There is no API key.', 'convertkit' ) );
			wp_die();
		}

		$this->api->update_resources( $api_key );

		$forms = get_option( 'convertkit_forms', array() );
		if ( isset( $forms[0] ) && isset( $forms[0]['id'] ) && '-2' === $forms[0]['id'] ) {
			wp_send_json_error( __( 'Error connecting to API. Please verify your site can connect to <code>https://api.convertkit.com</code>','convertkit' ) );
			wp_die();
		}

		$html = '';

		ob_start();
		$this->default_form_callback( $forms );
		$html = ob_get_clean();

		wp_send_json_success( $html );
		wp_die();
	}

	/**
	 * Register and add settings
	 */
	public function register_fields() {
		$forms = get_option( 'convertkit_forms' );
		add_settings_field(
			'api_key',
			'API Key',
			array( $this, 'api_key_callback' ),
			$this->settings_key,
			$this->name
		);

		add_settings_field(
			'api_secret',
			'API Secret',
			array( $this, 'api_secret_callback' ),
			$this->settings_key,
			$this->name
		);

		add_settings_field(
			'default_form',
			'Default Form',
			array( $this, 'default_form_callback' ),
			$this->settings_key,
			$this->name,
			$forms
		);

		add_settings_field(
			'refresh_forms',
			'',
			array( $this, 'refresh_forms_callback' ),
			$this->settings_key,
			$this->name
		);

		add_settings_field(
			'debug',
			'Debug',
			array( $this, 'debug_callback' ),
			$this->settings_key,
			$this->name
		);

		add_settings_field(
			'no_scripts',
			'Disable javascript',
			array( $this, 'no_scripts_callback' ),
			$this->settings_key,
			$this->name
		);
	}

	/**
	 * Prints help info for this section
	 */
	public function print_section_info() {
		?>
        <p><?php esc_html_e( 'Choosing a default form will embed it at the bottom of every post or page (in single view only) across your site.', 'convertkit' ); ?></p>
        <p><?php esc_html_e( 'If you wish to turn off form embedding or select a different form for an individual post or page, you can do so using the ConvertKit meta box on the edit page.', 'convertkit' ); ?></p><?php
		/* translators: 1: shortcode */ ?>
        <p><?php printf( esc_html__( 'The default form can be inserted into the middle of post or page content by using the %s shortcode.', 'convertkit' ), '<code>[convertkit]</code>' ); ?></p>
		<?php
	}

	/**
	 * Renders the input for api key entry
	 */
	public function api_key_callback() {
		$html = sprintf(
			'<input type="text" class="regular-text code" id="api_key" name="%s[api_key]" value="%s" />',
			$this->settings_key,
			isset( $this->options['api_key'] ) ? esc_attr( $this->options['api_key'] ) : ''
		);

		$html .= '<p class="description"><a href="https://app.convertkit.com/account/edit" target="_blank">' . __( 'Get your ConvertKit API Key', 'convertkit' ) . '</a></p>';

		$has_api = isset( $this->options['api_key'] ) ? esc_attr( $this->options['api_key'] ) : false;

		if ( ! $has_api ) {
			$html .= '<p class="description">' . __( 'Add you API Key to get started', 'convertkit' ) . '</p>';
		}
		echo $html; // WPCS: XSS ok.
	}

	/**
	 * Renders the input for api key entry
	 */
	public function api_secret_callback() {
		$html = sprintf(
			'<input type="text" class="regular-text code" id="api_secret" name="%s[api_secret]" value="%s" />',
			$this->settings_key,
			isset( $this->options['api_secret'] ) ? esc_attr( $this->options['api_secret'] ) : ''
		);

		$html .= '<p class="description"><a href="https://app.convertkit.com/account/edit" target="_blank">';
		$html .= __( 'Get your ConvertKit API Secret.', 'convertkit' ) . '</a>';
		$html .= ' ' . __( 'This setting is required to unsubscribe subscribers.', 'convertkit' ) . '</p>';

		echo $html; // WPCS: XSS ok.
	}

	/**
	 * Renders the form select list
	 *
	 * @param array $forms Form listing.
	 */
	public function default_form_callback( $forms ) {
		$html = '<div id="default_form_container">';
		// Check for error in response.
		if ( isset( $forms[0]['id'] ) && '-2' === $forms[0]['id'] ) {
			$html .= '<p id="default_form_error" class="error">' . __( 'Error connecting to API. Please verify your site can connect to <code>https://api.convertkit.com</code>','convertkit' ) . '</p>';
		} else {
			$html .= sprintf( '<select id="default_form" name="%s[default_form]">', $this->settings_key );
			$html .= '<option value="default">' . __( 'None', 'convertkit' ) . '</option>';
			if ( $forms ) {
				foreach ( $forms as $form ) {
					$form = (array) $form;
					$html .= sprintf(
						'<option value="%s" %s>%s</option>',
						esc_attr( $form['id'] ),
						selected( $this->options['default_form'], $form['id'], false ),
						esc_html( $form['name'] )
					);
				}
			}
			$html .= '</select>';
		}

		if ( empty( $this->options['api_key'] ) ) {
			$html .= '<p class="description">' . __( 'Enter your API Key above to get your available forms.', 'convertkit' ) . '</p>';
		}

		if ( empty( $forms ) ) {
			$html .= '<p class="description">' . __( 'There are no forms setup in your account. You can go <a href="https://app.convertkit.com/landing_pages/new" target="_blank">here</a> to create one.', 'convertkit' ) . '</p>';
		}

		$html .= '</div>';

		echo $html; // WPCS: XSS ok.
	}

	public function refresh_forms_callback() {
		$has_api = isset( $this->options['api_key'] ) ? esc_attr( $this->options['api_key'] ) : false;

		$html = '<input ' . ( $has_api ? '' : 'style="display:none;"' ) . ' type="submit" name="refresh" id="refreshCKForms" class="button" value="' . __( 'Refresh forms', 'convertkit' ) . '"><span id="refreshCKSpinner" class="spinner"></span>';

		echo $html; // WPCSS: XSS ok.
	}

	/**
	 * Renders the input for debug setting
	 */
	public function debug_callback() {

		$debug = '';
		if ( isset( $this->options['debug'] ) && 'on' === $this->options['debug'] ) {
			$debug = 'checked';
		}

		echo sprintf( // WPCS: XSS OK
			'<input type="checkbox" class="" id="debug" name="%s[debug]"  %s />%s',
			$this->settings_key,
			$debug,
			__( 'Save connection data to a log file.','convertkit' )
		);

	}

	/**
	 * Renders the input for no_scripts setting
	 */
	public function no_scripts_callback() {

		$no_scripts = '';
		if ( isset( $this->options['no_scripts'] ) && 'on' === $this->options['no_scripts'] ) {
			$no_scripts = 'checked';
		}

		echo sprintf( // WPCS: XSS OK
			'<label><input type="checkbox" class="" id="no_scripts" name="%s[no_scripts]"  %s />%s</label>',
			$this->settings_key,
			$no_scripts,
			__( 'Prevent plugin from loading javascript files. This will disable the custom content and tagging features of the plugin. Does not apply to landing pages. Use with caution!','convertkit' )
		);

	}
	/**
	 * Sanitizes the settings
	 *
	 * @param  array $settings The settings fields submitted.
	 * @return array           Sanitized settings.
	 */
	public function sanitize_settings( $settings ) {

		if ( isset( $settings['api_key'] ) ) {
			$forms = get_option( 'convertkit_forms' );
			if ( ! $forms ) {
				// No Forms? Let's update the resources.
				$this->api->update_resources( $settings['api_key'] );
			}
		}

		return shortcode_atts(
			array(
				'api_key'      => '',
				'api_secret'   => '',
				'default_form' => 0,
				'debug'        => '',
				'no_scripts'   => '',
			),
			$settings
		);
	}
}
