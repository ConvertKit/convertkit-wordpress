<?php
/**
 * Divi Module: ConvertKit Broadcasts.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Registers the ConvertKit Broadcasts Block as a Divi Module.
 *
 * @package ConvertKit
 * @author  ConvertKit
 */
class ConvertKit_Divi_Module_Broadcasts extends ConvertKit_Divi_Module {

	/**
	 * The ConvertKit block name.
	 *
	 * @since   2.5.7
	 *
	 * @var     string
	 */
	public $block_name = 'broadcasts';

	/**
	 * The ConvertKit Divi module name.
	 *
	 * @since   2.5.7
	 *
	 * @var     string
	 */
	public $slug = 'convertkit_broadcasts';

}

new ConvertKit_Divi_Module_Broadcasts();
