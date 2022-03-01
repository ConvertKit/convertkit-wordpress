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
	 * Gutenberg: Returns supported built in attributes, such as
	 * className, color etc.
	 *
	 * @since   1.9.6.9
	 *
	 * @return  array   Supports
	 */
	public function get_supports() {

		return array(
			'className' => true,
		);

	}

	/**
	 * Sanitize the given array of attributes, adding attributes that
	 * are missing but registered by the block.
	 *
	 * @since   1.9.6.9
	 *
	 * @param   array $atts   Declared attributes.
	 * @return  array           All attributes, sanitized
	 */
	public function sanitize_and_declare_atts( $atts ) {

		// Sanitize attributes, merging with default values so that the array
		// of attributes contains all expected keys for this block.
		$atts = shortcode_atts(
			$this->get_default_values(),
			$this->sanitize_atts( $atts ),
			$this->get_name()
		);

		// Cast some attributes based on their key.
		if ( array_key_exists( 'limit', $atts ) ) {
			$atts['limit'] = absint( $atts['limit'] );
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

		// If the block supports a link color, and a preset color was selected, add it to the
		// array of CSS classes.
		if ( $atts['linkColor'] ) {
			$atts['_css_classes'][] = 'has-link-color';
			$atts['_css_classes'][] = 'has-' . $atts['linkColor'] . '-color';
		}

		// Remove some unused attributes, now they're declared above.
		unset( $atts['style'] );

		return $atts;

	}

	/**
	 * Returns the given block's field's Default Value
	 *
	 * @since   1.9.6
	 *
	 * @param   string $field Field Name.
	 * @return  mixed   array|string
	 */
	public function get_default_value( $field ) {

		$defaults = $this->get_default_values();
		if ( isset( $defaults[ $field ] ) ) {
			return $defaults[ $field ];
		}

		return '';

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
