<?php
/**
 * Divi Module: ConvertKit Form.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Registers the ConvertKit Form Block as a Divi Module.
 *
 * @package ConvertKit
 * @author  ConvertKit
 */
class ConvertKit_Divi_Module_Form extends ConvertKit_Divi_Module {

	/**
	 * The ConvertKit block name.
	 *
	 * @since   2.5.6
	 *
	 * @var     string
	 */
	public $block_name = 'form';

	/**
	 * The ConvertKit Divi module name.
	 *
	 * @since   2.5.6
	 *
	 * @var     string
	 */
	public $slug = 'convertkit_form';

}

new ConvertKit_Divi_Module_Form();
