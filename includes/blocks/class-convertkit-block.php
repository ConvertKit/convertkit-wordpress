<?php
/**
 * ConvertKit Block class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * ConvertKit Block definition for Gutenberg and Shortcode.
 *
 * @package ConvertKit
 * @author  ConvertKit
 */
class ConvertKit_Block {

	/**
	 * Registers this block with the ConvertKit Plugin.
	 *
	 * @since   1.9.6
	 *
	 * @param   array $blocks     Blocks to Register.
	 * @return  array               Blocks to Register
	 */
	public function register( $blocks ) {

		$blocks[ $this->get_name() ] = array_merge(
			$this->get_overview(),
			array(
				'name'           => $this->get_name(),
				'fields'         => $this->get_fields(),
				'attributes'     => $this->get_attributes(),
				'supports'       => $this->get_supports(),
				'panels'         => $this->get_panels(),
				'default_values' => $this->get_default_values(),
			)
		);

		return $blocks;

	}

	/**
	 * Returns this block's programmatic name, excluding the convertkit- prefix.
	 *
	 * @since   1.9.6
	 */
	public function get_name() {

		/**
		 * This will register as:
		 * - a shortcode, with the name [convertkit_form].
		 * - a shortcode, with the name [convertkit], for backward compat.
		 * - a Gutenberg block, with the name convertkit/form.
		 */
		return '';

	}

	/**
	 * Returns this block's Title, Icon, Categories, Keywords and properties.
	 *
	 * @since   1.9.6
	 *
	 * @return  array
	 */
	public function get_overview() {

		return array();

	}

	/**
	 * Returns this block's Attributes
	 *
	 * @since   1.9.6.5
	 *
	 * @return  array
	 */
	public function get_attributes() {

		return array();

	}

	/**
	 * Gutenberg: Returns supported built in attributes, such as
	 * className, color etc.
	 *
	 * @since   1.9.7.4
	 *
	 * @return  array   Supports
	 */
	public function get_supports() {

		return array(
			'className' => true,
		);

	}

	/**
	 * Returns this block's Fields
	 *
	 * @since   1.9.6
	 *
	 * @return  array
	 */
	public function get_fields() {

		return array();

	}

	/**
	 * Returns this block's UI panels / sections.
	 *
	 * @since   1.9.6
	 *
	 * @return  array
	 */
	public function get_panels() {

		return array();

	}

	/**
	 * Returns this block's Default Values
	 *
	 * @since   1.9.6
	 *
	 * @return  array
	 */
	public function get_default_values() {

		return array();

	}

	/**
	 * Returns the given block's field's Default Value
	 *
	 * @since   1.9.6
	 *
	 * @param   string $field Field Name.
	 * @return  string
	 */
	public function get_default_value( $field ) {

		$defaults = $this->get_default_values();
		if ( isset( $defaults[ $field ] ) ) {
			return $defaults[ $field ];
		}

		return '';

	}

	/**
	 * Performs several transformation on a block's attributes, including:
	 * - sanitization
	 * - adding attributes with default values are missing but registered by the block
	 * - cast attribute values based on their defined type
	 *
	 * These steps are performed because the attributes may be defined by a shortcode,
	 * block or third party widget/page builder's block, each of which handle attributes
	 * slightly differently.
	 *
	 * Returns a standardised attributes array.
	 *
	 * @since   1.9.7.4
	 *
	 * @param   array $atts   Declared attributes.
	 * @return  array           All attributes, standardised.
	 */
	public function sanitize_and_declare_atts( $atts ) {

		// Sanitize attributes, merging with default values so that the array
		// of attributes contains all expected keys for this block.
		$atts = shortcode_atts(
			$this->get_default_values(),
			$this->sanitize_atts( $atts ),
			$this->get_name()
		);

		// Fetch attribute definitions.
		$atts_definitions = $this->get_attributes();

		// Iterate through attributes, casting them based on their attribute definition.
		foreach ( $atts as $att => $value ) {
			// Skip if no definition exists for this attribute.
			if ( ! array_key_exists( $att, $atts_definitions ) ) {
				continue;
			}

			// Skip if no type exists for this attribute.
			if ( ! array_key_exists( 'type', $atts_definitions[ $att ] ) ) {
				continue;
			}

			// Cast, depending on the attribute type.
			switch ( $atts_definitions[ $att ]['type'] ) {
				case 'number':
					$atts[ $att ] = (int) $value;
					break;

				case 'boolean':
					$atts[ $att ] = (bool) $value;
					break;
			}
		}

		// Build CSS class(es) that might need to be added to the top level element for this block.
		$atts['_css_classes'] = array( 'convertkit-' . $this->get_name() );
		$atts['_css_styles']  = array();

		// If the block supports a text color, and a preset color was selected, add it to the
		// array of CSS classes.
		if ( $atts['textColor'] ) {
			$atts['_css_classes'][] = 'has-text-color';
			$atts['_css_classes'][] = 'has-' . $atts['textColor'] . '-color';
		}

		// If the block supports a text color, and a custom hex color was selected, add it to the
		// array of CSS inline styles.
		if ( isset( $atts['style']['color'] ) && isset( $atts['style']['color']['text'] ) ) {
			$atts['_css_classes'][]       = 'has-text-color';
			$atts['_css_styles']['color'] = 'color:' . $atts['style']['color']['text'];
		}

		// If the block supports a background color, and a preset color was selected, add it to the
		// array of CSS classes.
		if ( $atts['backgroundColor'] ) {
			$atts['_css_classes'][] = 'has-background';
			$atts['_css_classes'][] = 'has-' . $atts['backgroundColor'] . '-background-color';
		}

		// If the block supports a background color, and a custom hex color was selected, add it to the
		// array of CSS inline styles.
		if ( isset( $atts['style']['color'] ) && isset( $atts['style']['color']['background'] ) ) {
			$atts['_css_classes'][]            = 'has-background';
			$atts['_css_styles']['background'] = 'background-color:' . $atts['style']['color']['background'];
		}

		// Remove some unused attributes, now they're declared above.
		unset( $atts['style'] );

		return $atts;

	}

	/**
	 * Removes any HTML that might be wrongly included in the shorcode attribute's values
	 * due to e.g. copy and pasting from Documentation or other examples.
	 *
	 * @since   1.9.6
	 *
	 * @param   array $atts   Shortcode Attributes.
	 * @return  array           Shortcode Attributes
	 */
	public function sanitize_atts( $atts ) {

		if ( ! is_array( $atts ) ) {
			return $atts;
		}

		foreach ( $atts as $key => $value ) {
			if ( is_array( $value ) ) {
				continue;
			}

			$atts[ $key ] = wp_strip_all_tags( $value );
		}

		return $atts;

	}

}
