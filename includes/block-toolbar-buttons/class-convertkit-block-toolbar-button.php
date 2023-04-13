<?php
/**
 * ConvertKit Block Toolbar Button class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * ConvertKit Block Toolbar Button definition for Gutenberg and TinyMCE.
 *
 * @package ConvertKit
 * @author  ConvertKit
 */
class ConvertKit_Block_Toolbar_Button {

	/**
	 * Registers this block toolbar button with the ConvertKit Plugin.
	 *
	 * @since   2.2.0
	 *
	 * @param   array $buttons     Block toolbar buttons to Register.
	 * @return  array              Block toolbar buttons to Register
	 */
	public function register( $buttons ) {

		$buttons[ $this->get_name() ] = array_merge(
			$this->get_overview(),
			array(
				'name'           => $this->get_name(),
				'tag'			 => $this->get_tag(),
				'attributes'     => $this->get_attributes(),
				'fields'         => $this->get_fields(),
			)
		);

		return $buttons;

	}

	/**
	 * Returns this button's programmatic name, excluding the convertkit- prefix.
	 *
	 * @since   2.2.0
	 */
	public function get_name() {

		/**
		 * This will register as:
		 * - a Gutenberg block toolbar button, with the name convertkit/{name}.
		 */
		return '';

	}

	/**
	 * Returns the tag that this button produces on the HTML output.
	 *
	 * @since   2.2.0
	 */
	public function get_tag() {

		return 'a';

	}

	/**
	 * Returns this button's Title, Icon, Categories, Keywords and properties.
	 *
	 * @since   2.2.0
	 *
	 * @return  array
	 */
	public function get_overview() {

		return array();

	}

	/**
	 * Returns this button's Attributes
	 *
	 * @since   2.2.0
	 *
	 * @return  array
	 */
	public function get_attributes() {

		return array();

	}

	/**
	 * Returns this block's Fields
	 *
	 * @since   2.2.0
	 *
	 * @return  array
	 */
	public function get_fields() {

		return array();

	}

}
