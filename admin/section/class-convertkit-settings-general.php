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

		parent::__construct();
	}

	/**
	 * Register and add settings
	 */
	public function register_fields() {
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
			$this->api->get_resources( 'forms' )
		);

		add_settings_field(
			'debug',
			'Debug',
			array( $this, 'debug_callback' ),
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

		// Check for error in response.
		if ( isset( $forms[0]['id'] ) && '-2' === $forms[0]['id'] ) {
			$html = '<p class="error">' . __( 'Error connecting to API. Please verify your site can connect to <code>https://api.convertkit.com</code>','convertkit' ) . '</p>';
		} else {
			$html = sprintf( '<select id="default_form" name="%s[default_form]">', $this->settings_key );
			$html .= '<option value="default">' . __( 'None', 'convertkit' ) . '</option>';
			foreach ( $forms as $form ) {
				$html .= sprintf(
					'<option value="%s" %s>%s</option>',
					esc_attr( $form['id'] ),
					selected( $this->options['default_form'], $form['id'], false ),
					esc_html( $form['name'] )
				);
			}
			$html .= '</select>';
		}

		if ( empty( $this->options['api_key'] ) ) {
			$html .= '<p class="description">' . __( 'Enter your API Key above to get your available forms.', 'convertkit' ) . '</p>';
		}

		if ( empty( $forms ) ) {
			$html .= '<p class="description">' . __( 'There are no forms setup in your account. You can go <a href="https://app.convertkit.com/landing_pages/new" target="_blank">here</a> to create one.', 'convertkit' ) . '</p>';
		}

		echo $html; // WPCS: XSS ok.
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
	 * Sanitizes the settings
	 *
	 * @param  array $settings The settings fields submitted.
	 * @return array           Sanitized settings.
	 */
	public function sanitize_settings( $settings ) {

		// Clear the api transient.
		delete_transient( 'convertkit_get_api_response' );
		return shortcode_atts( array(
			'api_key'      => '',
			'api_secret'   => '',
			'default_form' => 0,
			'debug' => '',
		), $settings );
	}
}
