<?php
/**
 * ConvertKit Settings Base class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * ConvertKit Settings class
 *
 * @package ConvertKit
 * @author ConvertKit
 */
abstract class ConvertKit_Settings_Base {

	/**
	 * Section name
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Section title
	 *
	 * @var string
	 */
	public $title;

	/**
	 * Section tab text
	 *
	 * @var string
	 */
	public $tab_text;

	/**
	 * Options table key
	 *
	 * @var string
	 */
	public $settings_key;

	/**
	 * Holds the settings class for the section.
	 *
	 * @since   1.9.6
	 *
	 * @var     false|ConvertKit_Settings|ConvertKit_ContactForm7_Settings|ConvertKit_Wishlist_Settings|ConvertKit_Settings_Restrict_Content|ConvertKit_Settings_Broadcasts|ConvertKit_Forminator_Settings
	 */
	public $settings;

	/**
	 * Holds whether this settings section is for beta functionality.
	 *
	 * @since   2.1.0
	 *
	 * @var     bool
	 */
	public $is_beta = false;

	/**
	 * Constructor
	 */
	public function __construct() {

		// If tab text is not defined, use the title for the tab's text.
		if ( empty( $this->tab_text ) ) {
			$this->tab_text = $this->title;
		}

		// Register the settings section.
		$this->register_section();

	}

	/**
	 * Register settings section.
	 */
	public function register_section() {

		add_settings_section(
			$this->name,
			$this->title,
			array( $this, 'print_section_info' ),
			$this->settings_key
		);

		$this->register_fields();

		register_setting(
			$this->settings_key,
			$this->settings_key,
			array( $this, 'sanitize_settings' )
		);

	}

	/**
	 * Register fields for this section
	 */
	abstract public function register_fields();

	/**
	 * Prints help info for this section
	 */
	abstract public function print_section_info();

	/**
	 * Returns the URL for the ConvertKit documentation for this setting section.
	 */
	abstract public function documentation_url();

	/**
	 * Renders the section
	 */
	public function render() {

		/**
		 * Performs actions prior to rendering the settings form.
		 *
		 * @since   1.9.6
		 */
		do_action( 'convertkit_settings_base_render_before' );

		$this->render_container_start();

		do_settings_sections( $this->settings_key );

		settings_fields( $this->settings_key );

		submit_button();

		$this->render_container_end();

		/**
		 *  Performs actions after rendering of the settings form.
		 *
		 * @since   1.9.6
		 */
		do_action( 'convertkit_settings_base_render_after' );

	}

	/**
	 * Outputs .metabox-holder and .postbox container div elements,
	 * used before beginning a setting screen's output.
	 *
	 * @since   2.0.0
	 */
	public function render_container_start() {

		?>
		<div class="metabox-holder">
			<div class="postbox <?php echo sanitize_html_class( $this->is_beta ? 'convertkit-beta' : '' ); ?>">
		<?php

	}

	/**
	 * Outputs closing .metabox-holder and .postbox container div elements,
	 * used after finishing a setting screen's output.
	 *
	 * @since   2.0.0
	 */
	public function render_container_end() {

		?>
			</div>
		</div>
		<?php

	}

	/**
	 * Redirects to the settings screen, with an option success or error message.
	 *
	 * @since   2.2.9
	 *
	 * @param   false|string $error      The error message key.
	 * @param   false|string $success    The success message key.
	 */
	public function redirect( $error = false, $success = false ) {

		// Build URL to redirect to, depending on whether a message is included.
		$args = array(
			'page' => '_wp_convertkit_settings',
			'tab'  => $this->name,
		);
		if ( $error !== false ) {
			$args['error'] = $error;
		}
		if ( $success !== false ) {
			$args['success'] = $success;
		}

		// Redirect.
		wp_safe_redirect( add_query_arg( $args, 'options-general.php' ) );
		exit();

	}

	/**
	 * Outputs the given success message in an inline notice.
	 *
	 * @since   2.0.0
	 *
	 * @param   string $success_message  Success Message.
	 */
	public function output_success( $success_message ) {

		?>
		<div class="notice notice-success is-dismissible">
			<p>
				<?php echo esc_attr( $success_message ); ?>
			</p>
		</div>
		<?php

	}

	/**
	 * Outputs the given error message in an inline notice.
	 *
	 * @since   1.9.6
	 *
	 * @param   string $error_message  Error Message.
	 */
	public function output_error( $error_message ) {

		?>
		<div class="notice notice-error is-dismissible">
			<p>
				<?php echo esc_attr( $error_message ); ?>
			</p>
		</div>
		<?php

	}

	/**
	 * Returns a masked value.
	 *
	 * @since   1.9.6
	 *
	 * @param   string      $value          Value.
	 * @param   bool|string $description    Description.
	 * @return  string                      Masked Value
	 */
	public function get_masked_value( $value, $description = false ) {

		$html = sprintf(
			'<code>%s</code>',
			str_repeat( '*', strlen( $value ) - 4 ) . substr( $value, - 4 )
		);

		if ( $description ) {
			$html .= $this->get_description( $description );
		}

		return $html;

	}

	/**
	 * Returns a text field.
	 *
	 * @since   1.9.6
	 *
	 * @param   string            $name           Name.
	 * @param   string            $value          Value.
	 * @param   bool|string|array $description    Description (false|string|array).
	 * @param   bool|array        $css_classes    CSS Classes (false|array).
	 * @return  string                              HTML Field
	 */
	public function get_text_field( $name, $value = '', $description = false, $css_classes = false ) {

		$html = sprintf(
			'<input type="text" class="%s" id="%s" name="%s[%s]" value="%s" />',
			( is_array( $css_classes ) ? implode( ' ', $css_classes ) : 'regular-text' ),
			$name,
			$this->settings_key,
			$name,
			$value
		);

		return $html . $this->get_description( $description );

	}

	/**
	 * Returns a textarea field.
	 *
	 * @since   2.3.5
	 *
	 * @param   string            $name           Name.
	 * @param   string            $value          Value.
	 * @param   bool|string|array $description    Description (false|string|array).
	 * @param   bool|array        $css_classes    CSS Classes (false|array).
	 * @return  string                              HTML Field
	 */
	public function get_textarea_field( $name, $value = '', $description = false, $css_classes = false ) {

		$html = sprintf(
			'<textarea class="%s" id="%s" name="%s[%s]">%s</textarea>',
			( is_array( $css_classes ) ? implode( ' ', $css_classes ) : 'regular-text' ),
			$name,
			$this->settings_key,
			$name,
			$value
		);

		return $html . $this->get_description( $description );

	}

	/**
	 * Returns a date field.
	 *
	 * @since   2.2.8
	 *
	 * @param   string            $name           Name.
	 * @param   string            $value          Value.
	 * @param   bool|string|array $description    Description (false|string|array).
	 * @param   bool|array        $css_classes    CSS Classes (false|array).
	 * @return  string                              HTML Field
	 */
	public function get_date_field( $name, $value = '', $description = false, $css_classes = false ) {

		$html = sprintf(
			'<input type="date" class="%s" id="%s" name="%s[%s]" value="%s" />',
			( is_array( $css_classes ) ? implode( ' ', $css_classes ) : 'regular-text' ),
			$name,
			$this->settings_key,
			$name,
			$value
		);

		return $html . $this->get_description( $description );

	}

	/**
	 * Returns a select dropdown field.
	 *
	 * @since   1.9.6
	 *
	 * @param   string      $name            Name.
	 * @param   string      $value           Value.
	 * @param   array       $options         Options / Choices.
	 * @param   bool|string $description     Description.
	 * @param   bool|array  $css_classes     <select> CSS class(es).
	 * @param   bool|array  $attributes      <select> attributes.
	 * @return  string                           HTML Select Field
	 */
	public function get_select_field( $name, $value = '', $options = array(), $description = false, $css_classes = false, $attributes = false ) {

		// Build opening <select> tag.
		$html = sprintf(
			'<select id="%s" name="%s[%s]" class="%s" size="1" %s>',
			$this->settings_key . '_' . $name,
			$this->settings_key,
			$name,
			( is_array( $css_classes ) ? implode( ' ', $css_classes ) : '' ),
			( is_array( $attributes ) ? $this->array_to_attributes( $attributes ) : '' )
		);

		// Build <option> tags.
		foreach ( $options as $option => $label ) {
			$html .= sprintf(
				'<option value="%s"%s>%s</option>',
				$option,
				selected( $value, $option, false ),
				$label
			);
		}

		// Close <select>.
		$html .= '</select>';

		// If no description exists, just return the select field.
		if ( empty( $description ) ) {
			return $html;
		}

		// Return select field with description appended to it.
		return $html . $this->get_description( $description );

	}

	/**
	 * Returns a checkbox field.
	 *
	 * @since   1.9.6
	 *
	 * @param   string            $name           Name.
	 * @param   string            $value          Value.
	 * @param   bool              $checked        Should checkbox be checked/ticked.
	 * @param   bool|string       $label          Label.
	 * @param   bool|string|array $description    Description.
	 * @param   bool|array        $css_classes    CSS class(es).
	 * @return  string                            HTML Checkbox
	 */
	public function get_checkbox_field( $name, $value, $checked = false, $label = '', $description = false, $css_classes = false ) {

		$html = '';

		if ( $label ) {
			$html .= sprintf(
				'<label for="%s">',
				$name
			);
		}

		$html .= sprintf(
			'<input type="checkbox" id="%s" name="%s[%s]" class="%s" value="%s" %s />',
			$name,
			$this->settings_key,
			$name,
			( is_array( $css_classes ) ? implode( ' ', $css_classes ) : '' ),
			$value,
			( $checked ? ' checked' : '' )
		);

		if ( $label ) {
			$html .= sprintf(
				'%s</label>',
				$label
			);
		}

		// If no description exists, just return the field.
		if ( empty( $description ) ) {
			return $html;
		}

		// Return field with description appended to it.
		return $html . $this->get_description( $description );

	}

	/**
	 * Returns the given text wrapped in a paragraph with the description class.
	 *
	 * @since   1.9.6
	 *
	 * @param   bool|string|array $description    Description.
	 * @return  string                              HTML Description
	 */
	public function get_description( $description ) {

		// Return blank string if no description specified.
		if ( ! $description ) {
			return '';
		}

		// Return description in paragraph if a string.
		if ( ! is_array( $description ) ) {
			return '<p class="description">' . $description . '</p>';
		}

		// Return description lines in a paragraph, using breaklines for each description entry in the array.
		return '<p class="description">' . implode( '<br />', $description );

	}

	/**
	 * Converts the given key/value array pairs into a HTML attribute="value" string.
	 *
	 * @since   1.9.8.5
	 *
	 * @param   array $attributes_array  Attributes.
	 * @return  string                  HTML attributes string
	 */
	private function array_to_attributes( $attributes_array ) {

		$attributes = '';
		foreach ( $attributes_array as $key => $value ) {
			$attributes .= esc_attr( $key ) . '="' . esc_attr( $value ) . '" ';
		}

		return trim( $attributes );

	}

	/**
	 * Sanitizes the settings prior to being saved.
	 *
	 * @since   1.9.6
	 *
	 * @param   array $settings   Submitted Settings Fields.
	 * @return  array               Sanitized Settings with Defaults
	 */
	public function sanitize_settings( $settings ) {

		// If a Form or Landing Page was specified, request a review.
		// This can safely be called multiple times, as the review request
		// class will ensure once a review request is dismissed by the user,
		// it is never displayed again.
		if ( ( isset( $settings['page_form'] ) && $settings['page_form'] ) ||
			( isset( $settings['post_form'] ) && $settings['post_form'] ) ) {
			WP_ConvertKit()->get_class( 'review_request' )->request_review();
		}

		// Merge settings with defaults.
		$settings = wp_parse_args( $settings, $this->settings->get_defaults() );

		/**
		 * Performs actions prior to settings being saved.
		 *
		 * @since   2.2.8
		 */
		do_action( 'convertkit_settings_base_sanitize_settings', $this->name, $settings );

		// Return settings to be saved.
		return $settings;

	}

}
