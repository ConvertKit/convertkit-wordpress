<?php
/**
 * Divi Integration class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Registers this Plugin as a Divi extension, so that
 * Divi modules can then be registered.
 *
 * @package ConvertKit
 * @author  ConvertKit
 */
class ConvertKit_Divi {

	/**
	 * Constructor
	 *
	 * @since   2.5.6
	 */
	public function __construct() {

		add_action( 'divi_extensions_init', array( $this, 'divi_extensions_init' ) );

	}

	/**
	 * Loads the ConvertKi Divi extension, which registers ConvertKit-specific Divi modules.
	 *
	 * @since   2.5.6
	 */
	public function divi_extensions_init() {

		require_once CONVERTKIT_PLUGIN_PATH . '/includes/integrations/divi/class-convertkit-divi-extension.php';

	}

}
