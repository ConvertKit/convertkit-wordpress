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
				'tabs'           => $this->get_tabs(),
				'default_values' => $this->get_default_values(),
			)
		);

		return $blocks;

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
