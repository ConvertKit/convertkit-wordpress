<?php
/**
 * ConvertKit Block Formatter class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Base class for registering a formatter in Gutenberg
 * using the Formatting Toolbar API.
 *
 * @package ConvertKit
 * @author  ConvertKit
 */
class ConvertKit_Block_Formatter {

	/**
	 * Registers this formatter with the ConvertKit Plugin.
	 *
	 * @since   2.2.0
	 *
	 * @param   array $formatters   Formatters to register.
	 * @return  array               Formatters to register.
	 */
	public function register( $formatters ) {

		$formatters[ $this->get_name() ] = array_merge(
			$this->get_overview(),
			array(
				'name'       => $this->get_name(),
				'tag'        => $this->get_tag(),
				'attributes' => $this->get_attributes(),
				'fields'     => $this->get_fields(),
			)
		);

		return $formatters;

	}

	/**
	 * Returns this formatters's programmatic name, excluding the convertkit- prefix.
	 *
	 * @since   2.2.0
	 */
	public function get_name() {

		/**
		 * This will register as:
		 * - a Gutenberg formatters toolbar button, with the name convertkit/{name}.
		 */
		return '';

	}

	/**
	 * Returns the tag that this formatter applies when used.
	 *
	 * @since   2.2.0
	 */
	public function get_tag() {

		return 'a';

	}

	/**
	 * Returns this formatters's Title, Description and Icon
	 *
	 * @since   2.2.0
	 *
	 * @return  array
	 */
	public function get_overview() {

		return array();

	}

	/**
	 * Returns this formatter's attributes, which are applied
	 * to the tag.
	 *
	 * @since   2.2.0
	 *
	 * @return  array
	 */
	public function get_attributes() {

		return array();

	}

	/**
	 * Returns this formatter's fields to display when the formatter
	 * button is clicked in the toolbar.
	 *
	 * @since   2.2.0
	 *
	 * @return  array
	 */
	public function get_fields() {

		return array();

	}

}
