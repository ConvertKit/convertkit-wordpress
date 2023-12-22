<?php
/**
 * ConvertKit WooCommerce Product Form class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Registers ConvertKit Forms on WooCommerce Products
 *
 * @package ConvertKit
 * @author ConvertKit
 */
class ConvertKit_WooCommerce_Product_Form {

	/**
	 * Constructor.
	 *
	 * @since   1.9.6
	 */
	public function __construct() {

		add_filter( 'convertkit_get_supported_post_types', array( $this, 'register_post_type_support' ) );
		add_action( 'convertkit_output_output_form', array( $this, 'output_form' ) );

	}

	/**
	 * Register ConvertKit Form support for WooCommerce Products.
	 *
	 * @since   1.9.6
	 *
	 * @param   array $post_types     Supported Post Types.
	 * @return  array   $post_types     Supported Post Types
	 */
	public function register_post_type_support( $post_types ) {

		// Bail if WooCommerce isn't active.
		if ( ! $this->is_active() ) {
			return $post_types;
		}

		// Register WooCommerce Product support.
		$post_types[] = 'product';
		return $post_types;

	}

	/**
	 * Output the ConvertKit Form after the Product's Summary.
	 *
	 * @since   1.9.6
	 */
	public function output_form() {

		// Bail if WooCommerce isn't active.
		if ( ! $this->is_active() ) {
			return;
		}

		// Bail if not a singular Product.
		if ( ! is_singular( 'product' ) ) {
			return;
		}

		// Remove the_content filter, as this isn't always reliable.
		remove_filter( 'the_content', array( WP_ConvertKit()->get_class( 'output' ), 'append_form_to_content' ) );

		// Output the Form after the Product's Summary.
		add_action( 'woocommerce_after_single_product_summary', array( $this, 'append_form_to_product_summary' ) );

	}

	/**
	 * Append the ConvertKit Form to the Product's Summary.
	 *
	 * @since   1.9.6
	 */
	public function append_form_to_product_summary() {

		// Output is already escaped in append_form_to_content().
		echo WP_ConvertKit()->get_class( 'output' )->append_form_to_content( '' ); // phpcs:ignore WordPress.Security.EscapeOutput

	}

	/**
	 * Determines if the WooCommerce Plugin is active.
	 *
	 * @since   1.9.6
	 *
	 * @return  bool    Plugin Active.
	 */
	public function is_active() {

		return ( defined( 'WC_PLUGIN_FILE' ) ? true : false );

	}

}

// Bootstrap.
add_action(
	'convertkit_initialize_global',
	function () {

		new ConvertKit_WooCommerce_Product_Form();

	}
);
