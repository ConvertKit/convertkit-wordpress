<?php
/**
 * Divi Integration class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Registers blocks as Divi Modules.
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

		// Register extension.
		add_action( 'divi_extensions_init', array( $this, 'divi_extensions_init' ) );

		add_action( 'init', array( $this, 'register_modules' ) );

	}

	public function divi_extensions_init() {

		require_once CONVERTKIT_PLUGIN_PATH . '/includes/integrations/divi/class-convertkit-divi-extension.php';

	}

	/**
	 * Registers blocks as Divi Modules, so that they can be used in the Divi Builder
	 *
	 * @since   2.5.6
	 */
	public function register_modules() {

		// Bail if Divi isn't loaded.
		if ( ! class_exists( 'ET_Builder_Module' ) ) {
			return;
		}
		if ( ! class_exists( 'ET_Builder_Element' ) ) {
			return;
		}

		// @TODO test
		//require_once CONVERTKIT_PLUGIN_PATH . '/includes/integrations/divi/class-convertkit-divi-module-form.php';
		return;

		// Get blocks.
		$blocks = convertkit_get_blocks();

		// Bail if no blocks are available.
		if ( ! is_array( $blocks ) || ! count( $blocks ) ) {
			return;
		}

		// Load module class here, as we know that Divi is active.
		//require_once CONVERTKIT_PLUGIN_PATH . '/includes/integrations/divi/class-convertkit-divi-module.php';

		// Iterate through blocks, registering them.
		foreach ( $blocks as $block => $properties ) {
			// Skip if this block doesn't have a Divi Module class.
			if ( ! file_exists( CONVERTKIT_PLUGIN_PATH . '/includes/integrations/divi/class-convertkit-divi-module-' . $block . '.php' ) ) {
				continue;
			}

			// Load module class for this block.
			require_once CONVERTKIT_PLUGIN_PATH . '/includes/integrations/divi/class-convertkit-divi-module-' . $block . '.php';
			$class_name = 'ConvertKit_Divi_Module_' . str_replace( '-', '_', $block );

			new ConvertKit_Divi_Module_Form();
			break;

			/*
			// Skip if class doesn't exist.
			if ( ! class_exists( $class_name ) ) {
				continue;
			}

			// Load.
			var_dump( $class_name );
			die();

			$module = new $class_name();
			*/
		}

	}

}
