<?php
/**
 * ConvertKit Custom Content class
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Class ConvertKit_Settings_Custom_Content
 *
 * @since 1.4.4
 */
class ConvertKit_Settings_Custom_Content extends ConvertKit_Settings_Base {

	/**
	 * Constructor
	 */
	public function __construct() {

		$this->settings_key  = '_wp_convertkit_integration_custom_content_settings';
		$this->name          = 'custom_content';
		$this->title         = 'Custom Content Integration Settings';
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

		$pages = get_all_page_ids();
		$tags = $this->api->get_resources( 'tags' );

		foreach( $pages as $page_id ) {

			$title = get_the_title( $page_id );
			add_settings_field(
				sprintf( '%s_title', $page_id ),
				'Page',
				array( $this, 'cc_title_callback' ),
				$this->settings_key,
				$this->name,
				array(
					'page_id'   => $page_id,
					'page_name' => $title,
					'sortable'  => true,
				)
			);

			add_settings_field(
				sprintf( '%s_tag', $page_id ),
				'ConvertKit Tag',
				array( $this, 'cc_form_callback' ),
				$this->settings_key,
				$this->name,
				array(
					'page_id'  => $page_id,
					'tags'     => $tags,
					'sortable' => false,
				)
			);

		}

	}

	/**
	 * Renders the section
	 */
	public function render() {
		$this->do_settings_sections( $this->settings_key );
		settings_fields( $this->settings_key );
		$this->do_settings_table();
		submit_button();
	}

	/**
	 * Render the settings table. Designed to mimic WP's do_settings_fields
	 */
	public function do_settings_table() {
		global $wp_settings_fields;

		$table   = new Multi_Value_Field_Table;
		$columns = array();
		$rows    = array();
		$fields  = $wp_settings_fields[ $this->settings_key ][ $this->name ];

		foreach ( $fields as $field ) {
			if ( strpos( $field['id'], '_' ) ) {
				list( $cf7_form_id, $field_type ) = explode( '_', $field['id'] );

				if ( ! in_array( $field_type, $columns, true ) ) {
					$table->add_column( $field_type, $field['title'], $field['args']['sortable'] );
					array_push( $columns, $field_type );
				}

				if ( ! isset( $rows[ $cf7_form_id ] ) ) {
					$rows[ $cf7_form_id ] = array();
				}

				$rows[ $cf7_form_id ][ $field_type ] = call_user_func( $field['callback'], $field['args'] );
			}
		}

		foreach ( $rows as $row ) {
			$table->add_item( $row );
		}

		$table->prepare_items();
		$table->display_no_nonce();

	}

	/**
	 *
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

		$debug = '';
		if ( isset( $this->options['enable'] ) && 'on' === $this->options['enable'] ) {
			$debug = 'checked';
		}

		echo sprintf( // WPCS: XSS OK
			'<input type="checkbox" class="" id="enable" name="%s[enable]"  %s />%s',
			$this->settings_key,
			$debug,
			__( 'If this is checked custom content will be displayed.','convertkit' )
		);

	}

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
		esc_html_e( 'ConvertKit seamlessly integrates with Contact Form 7 to let you add subscribers using Contact Form 7 forms.', 'convertkit' );
		?></p><p><?php
		printf( 'The Contact Form 7 form must have <code>text*</code> fields named <code>your-name</code> and <code>your-email</code>. ' );
		esc_html_e( 'These fields will be sent to ConvertKit for the subscription.', 'convertkit' );
		?></p><?php
	}

	/**
	 * Not used anymore
	 * TODO: Delete
	 */
	public function mapping_callback() {

		$value = isset( $this->options['mapping'] ) ? esc_attr( $this->options['mapping'] ) : '';
		echo '<textarea class="regular-text code" id="mapping" name="' . $this->settings_key . '[mapping]" style="height:150px">';
		echo $value;
		echo '</textarea>';
		echo '<p class="description"> Map pages to tags using page id and tag name one per line. Example: [55,newsletter]</p>';

	}

	/**
	 * Display page title in first column
	 *
	 * @param  array $args Name argument.
	 * @return string
	 */
	public function cc_title_callback( $args ) {
		return $args['page_name'];
	}

	/**
	 * Display Page to CK tag mapping
	 *
	 * @param  array $args Settings to display.
	 * @return string $html
	 */
	public function cc_form_callback( $args ) {
		$page_id = $args['page_id'];
		$tags  = $args['tags'];

		$html = sprintf( '<select id="%1$s_%2$s" name="%1$s[mapping][%2$s]">', $this->settings_key, $page_id );
		$html .= '<option value="0">' . esc_html__( 'None', 'convertkit' ) . '</option>';
		foreach ( $tags as $tag ) {
			$selected = '';
			if ( isset( $this->options[ $page_id ] ) ) {
				$selected = selected( $this->options[ $page_id ], $tag['id'], false );
			}
			$html .=
				'<option value="' .
				esc_attr( $tag['id'] ) . '" ' .
				$selected . '>' .
				esc_html( $tag['name'] ) . '</option>';
		}
		$html .= '</select>';

		return $html;
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
		//unset( $output['mapping'] );

		foreach ( $input as $key => $value ) {
			if ( is_array( $value ) ) {
				foreach ( $value as $page_id => $tag ) {
					$output[ $key ][ $page_id ] = stripslashes( $tag );
				}
			} else {
				$output[ $key ] = stripslashes( $input[ $key ] );
			}
		}
		$sanitize_hook = 'sanitize' . $this->settings_key;
		return apply_filters( $sanitize_hook, $output, $input );
	}
}