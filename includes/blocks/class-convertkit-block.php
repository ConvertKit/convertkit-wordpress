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
		return 'form';

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
