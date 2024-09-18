<?php
/**
 * Divi Module: ConvertKit Product.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Registers the ConvertKit Product Block as a Divi Module.
 *
 * @package ConvertKit
 * @author  ConvertKit
 */
class ConvertKit_Divi_Module_Product extends ConvertKit_Divi_Module {

	/**
	 * The ConvertKit block name.
	 *
	 * @since   2.5.7
	 *
	 * @var     string
	 */
	public $block_name = 'product';

	/**
	 * The ConvertKit Divi module name.
	 *
	 * @since   2.5.7
	 *
	 * @var     string
	 */
	public $slug = 'convertkit_product';

}

new ConvertKit_Divi_Module_Product();
