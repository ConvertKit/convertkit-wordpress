<?php
/**
 * ConvertKit Product Link Block Formatter class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * ConvertKit Product Link Block Formatter class.
 *
 * @package ConvertKit
 * @author  ConvertKit
 */
class ConvertKit_Block_Formatter_Product_Link extends ConvertKit_Block_Formatter {

	/**
	 * Holds the ConvertKit Products resource class.
	 *
	 * @since   2.2.0
	 *
	 * @var     bool|ConvertKit_Resource_Products
	 */
	public $products = false;

	/**
	 * Holds the Post's content.
	 *
	 * @since   2.2.0
	 *
	 * @var     string
	 */
	public $content = '';

	/**
	 * Constructor
	 *
	 * @since   2.2.0
	 */
	public function __construct() {

		// Register this as a Gutenberg block formatter in the ConvertKit Plugin,
		// if forms exist on ConvertKit.
		$this->products = new ConvertKit_Resource_Products( 'block_formatter_register' );
		if ( $this->products->exist() ) {
			add_filter( 'convertkit_get_block_formatters', array( $this, 'register' ) );
		}

	}

	/**
	 * Returns this formatter's programmatic name, excluding the convertkit- prefix.
	 *
	 * @since   2.2.0
	 *
	 * @return  string
	 */
	public function get_name() {

		return 'product-link';

	}

	/**
	 * Returns this formatters's Title, Description and Icon
	 *
	 * @since   2.2.0
	 *
	 * @return  array
	 */
	public function get_overview() {

		return array(
			'title'          => __( 'Kit Product Trigger', 'convertkit' ),
			'description'    => __( 'Displays the Product modal when the link is pressed.', 'convertkit' ),
			'icon'           => 'resources/backend/images/block-icon-product.svg',

			// Gutenberg: Block Icon in Editor.
			'gutenberg_icon' => convertkit_get_file_contents( CONVERTKIT_PLUGIN_PATH . '/resources/backend/images/block-icon-product.svg' ),
		);

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

		return array(
			'data-id'       => '',
			'data-commerce' => '',
			'href'          => '',
		);

	}

	/**
	 * Returns this formatter's fields to display when the formatter
	 * button is clicked in the toolbar.
	 *
	 * @since   2.2.0
	 *
	 * @return  bool|array
	 */
	public function get_fields() {

		// Bail if the request is not for the WordPress Administration or frontend editor.
		if ( ! WP_ConvertKit()->is_admin_or_frontend_editor() ) {
			return false;
		}

		// Get ConvertKit Products.
		$products      = array();
		$products_data = array();
		if ( $this->products->exist() ) {
			foreach ( $this->products->get() as $product ) {
				$products[ absint( $product['id'] ) ]      = sanitize_text_field( $product['name'] );
				$products_data[ absint( $product['id'] ) ] = array(
					'data-id'       => sanitize_text_field( $product['id'] ),
					'data-commerce' => '1',
					'href'          => $product['url'],
				);
			}
		}

		// Return field.
		return array(
			'data-id' => array(
				'label'       => __( 'Product', 'convertkit' ),
				'type'        => 'select',
				'description' => __( 'The product modal to display when the text is clicked.', 'convertkit' ),

				// Key/value pairs for the <select> dropdown.
				'values'      => $products,

				// Contains all additional data required to build the link.
				'data'        => $products_data,
			),
		);

	}

}
