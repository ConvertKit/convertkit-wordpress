<?php
/**
 * ConvertKit Block Toolbar Link Button class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * ConvertKit Block Toolbar Link Button definition for Gutenberg and TinyMCE.
 *
 * @package ConvertKit
 * @author  ConvertKit
 */
class ConvertKit_Block_Toolbar_Button_Link {

	/**
	 * Constructor
	 *
	 * @since   2.2.0
	 */
	public function __construct() {

		// Register this as a Gutenberg block toolbar button in the ConvertKit Plugin.
		add_filter( 'convertkit_block_toolbar_buttons', array( $this, 'register' ) );

	}

	/**
	 * Returns this button's programmatic name, excluding the convertkit- prefix.
	 *
	 * @since   2.2.0
	 *
	 * @return  string
	 */
	public function get_name() {

		return 'link';

	}

	/**
	 * Returns this button's Title, Icon, Categories, Keywords and properties.
	 *
	 * @since   2.2.0
	 *
	 * @return  array
	 */
	public function get_overview() {

		return array(
			'title'                             => __( 'Link to ConvertKit', 'convertkit' ),
			'description'                       => __( 'Links the selected text to a ConvertKit Form or Product.', 'convertkit' ),
			'icon'                              => 'resources/backend/images/block-icon-link.png',
			'category'                          => 'convertkit',
			'keywords'                          => array(
				__( 'ConvertKit', 'convertkit' ),
				__( 'Form', 'convertkit' ),
				__( 'Product', 'convertkit' ),
			),

			// Gutenberg: Block Icon in Editor.
			'gutenberg_icon'                    => file_get_contents( CONVERTKIT_PLUGIN_PATH . '/resources/backend/images/block-icon-link.svg' ), /* phpcs:ignore */
		);

	}

	/**
	 * Returns this button's Attributes
	 *
	 * @since   2.2.0
	 *
	 * @return  array
	 */
	public function get_attributes() {

		return array(
			'form'  => array(
				'type' => 'string',
			),
		);

	}

	

}
