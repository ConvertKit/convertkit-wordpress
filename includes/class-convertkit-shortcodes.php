<?php
/**
 * ConvertKit Shortcodes class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Registers blocks defined in the `convertkit_blocks` filter as WordPress Shortcodes.
 *
 * @package ConvertKit
 * @author  ConvertKit
 */
class ConvertKit_Shortcodes {

	/**
	 * Constructor
	 *
	 * @since   1.9.6
	 */
	public function __construct() {

		add_action( 'init', array( $this, 'init' ), 10, 1 );

	}

	/**
	 * Register ConvertKit blocks as shortcodes.
	 *
	 * @since   1.9.6
	 */
	public function init() {

		// Get blocks.
		$blocks = convertkit_get_blocks();

		// Bail if no blocks are available.
		if ( ! is_array( $blocks ) || ! count( $blocks ) ) {
			return;
		}

		// Iterate through blocks, registering them as shortcodes.
		foreach ( $blocks as $block => $properties ) {

			// Register shortcode.
			add_shortcode(
				'convertkit_' . $block,
				array(
					$properties['render_callback'][0],
					$properties['render_callback'][1],
				)
			);

			// For the Form block, register the [convertkit] shortcode for backward compatibility.
			if ( $block === 'form' ) {
				add_shortcode(
					'convertkit',
					array(
						$properties['render_callback'][0],
						$properties['render_callback'][1],
					)
				);
			}
		}

	}

}
