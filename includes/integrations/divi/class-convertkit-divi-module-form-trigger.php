<?php
/**
 * Divi Module: ConvertKit Form Trigger.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Registers the ConvertKit Form Trigger Block as a Divi Module.
 *
 * @package ConvertKit
 * @author  ConvertKit
 */
class ConvertKit_Divi_Module_Form_Trigger extends ConvertKit_Divi_Module {

	/**
	 * The ConvertKit block name.
	 *
	 * @since   2.5.7
	 *
	 * @var     string
	 */
	public $block_name = 'formtrigger';

	/**
	 * The ConvertKit Divi module name.
	 *
	 * @since   2.5.7
	 *
	 * @var     string
	 */
	public $slug = 'convertkit_formtrigger';

}

new ConvertKit_Divi_Module_Form_Trigger();
