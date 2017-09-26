<?php
/**
 * ConvertKit Custom Content class
 *
 * This class handles the Custom Content tab on the Settings > ConvertKit page.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Class ConvertKit_Settings_Custom_Content
 *
 * @since 1.5.0
 */
class ConvertKit_Settings_Custom_Content extends ConvertKit_Settings_Base {

	/**
	 * Constructor
	 */
	public function __construct() {

		$this->settings_key  = '_wp_convertkit_integration_custom_content_settings';
		$this->name          = 'custom_content';
		$this->title         = 'Custom Content Settings';
		$this->tab_text      = 'Custom Content';

		parent::__construct();
	}

	/**
	 * Register and add settings
	 */
	public function register_fields() {
		add_settings_field(
			'enable',
			'Enable',
			array( $this, 'enable_callback' ),
			$this->settings_key,
			$this->name
		);

		add_settings_field(
			'expire',
			'Visitor tracking expires after',
			array( $this, 'expire_callback' ),
			$this->settings_key,
			$this->name
		);
		
	}

	/**
	 * Renders the section
	 */
	public function render() {
		$this->do_settings_sections( $this->settings_key );
		settings_fields( $this->settings_key );
		//$this->do_settings_table();
		submit_button();
	}

	/**
	 * Display the page's settings section
	 */
	function do_settings_sections( $page ) {
		global $wp_settings_sections, $wp_settings_fields;

		if ( ! isset( $wp_settings_sections[$page] ) )
			return;

		foreach ( (array) $wp_settings_sections[$page] as $section ) {

			if ( $section['title'] ) {
				echo "<h2>{$section['title']}</h2>\n";
			}

			if ( $section['callback'] ) {
				call_user_func( $section['callback'], $section );
			}

			if ( ! isset( $wp_settings_fields ) || ! isset( $wp_settings_fields[ $page ] ) || ! isset( $wp_settings_fields[ $page ][ $section['id'] ] ) ) {
				continue;
			}
			echo '<table class="form-table">';
			$this->do_settings_fields( $page, $section['id'] );
			echo '</table>';
		}
	}

	/**
	 * Display individual settings fields
	 *
	 * @param $page
	 * @param $section
	 */
	function do_settings_fields($page, $section) {
		global $wp_settings_fields;

		if ( ! isset( $wp_settings_fields[$page][$section] ) )
			return;

		foreach ( (array) $wp_settings_fields[$page][$section] as $field ) {
			if ( ! strpos( $field['id'], '_' ) ) {
				$class = '';

				if ( ! empty( $field['args']['class'] ) ) {
					$class = ' class="' . esc_attr( $field['args']['class'] ) . '"';
				}

				echo "<tr{$class}>";

				if ( ! empty( $field['args']['label_for'] ) ) {
					echo '<th scope="row"><label for="' . esc_attr( $field['args']['label_for'] ) . '">' . $field['title'] . '</label></th>';
				} else {
					echo '<th scope="row">' . $field['title'] . '</th>';
				}

				echo '<td>';
				call_user_func( $field['callback'], $field['args'] );
				echo '</td>';
				echo '</tr>';
			}
		}
	}

	/**
	 * Renders the input for Enable/Disable setting
	 */
	public function enable_callback() {

		$enable = '';
		if ( isset( $this->options['enable'] ) && 'on' === $this->options['enable'] ) {
			$enable = 'checked';
		}

		echo sprintf( // WPCS: XSS OK
			'<input type="checkbox" class="" id="%s[enable]" name="%s[enable]" %s />%s',
			$this->settings_key,
			$this->settings_key,
			$enable,
			__( 'If this is checked custom content will be displayed.','convertkit' )
		);

	}

	/**
	 * Callback to dispaly the tracking callback
	 */
	public function expire_callback() {
		$value = isset( $this->options['expire'] ) ? esc_attr( $this->options['expire'] ) : '';

		$options = array(
			1 => 'One Month',
			2 => 'Two Months',
			3 => 'Three Months',
			4 => 'Four Months',
		);

		echo '<select id="' . $this->settings_key . '[expire]" name="' . $this->settings_key . '[expire]">';

		foreach ( $options as $key => $option ) {
			$selected = selected( $value, $key, false );
			echo '<option value="' . esc_attr( $key ) . '" ' . $selected . '>' . esc_html( $option ) . '</option>';
		}
		echo '</select>';
	}

	/**
	 * Prints help info for this section
	 */
	public function print_section_info() {
		?><p><?php
		esc_html_e( 'ConvertKit custom content will apply tags to site visitors so you can present them with content based on their browsing history.', 'convertkit' );
	}

	/**
	 * Sanitizes the settings
	 *
	 * @param  array $input Values to be sanitized.
	 * @return array sanitized settings
	 */
	public function sanitize_settings( $input ) {
		// Settings page can be paginated; combine input with existing options.
		$output = $this->options;
		unset( $input['mapping'] );

		foreach ( $input as $key => $value ) {
			if ( is_array( $value ) ) {
				foreach ( $value as $page_id => $tag ) {
					$output[ $key ][ $page_id ] = stripslashes( $tag );
				}
			} else {
				$output[ $key ] = stripslashes( $input[ $key ] );
			}
		}

		if ( ! isset( $input['enable'] ) ) {
			$output['enable'] = '';
		}

		$sanitize_hook = 'sanitize' . $this->settings_key;
		return apply_filters( $sanitize_hook, $output, $input );
	}
}
