<?php
/**
 * REST API: ConvertKit_WP_REST_Product_Search_Handler class
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * WordPress REST API Search Handler for ConvertKit Products.
 *
 * Used by e.g. Gutenberg's Link functionality to populate a list of matching
 * ConvertKit Products to link selected text to.
 *
 * @since   1.9.8.5
 */
class ConvertKit_WP_REST_Search_Handler_Product extends WP_REST_Search_Handler {

	/**
	 * Constructor.
	 *
	 * @since 	1.9.8.5
	 */
	public function __construct() {

		// Endpoint e.g. /wp-json/wp/v2/search?search=test&type=convertkit_product.
		$this->type = 'convertkit_product';

	}

	/**
	 * Searches ConvertKit Products.
	 *
	 * @since   1.9.8.5
	 *
	 * @param 	WP_REST_Request 	$request 	Full REST request.
	 * @return 	array 							Associative array containing an `WP_REST_Search_Handler::RESULT_IDS` containing
	 *               							an array of found IDs and `WP_REST_Search_Handler::RESULT_TOTAL` containing the
	 *               							total count for the matching search results.
	 */
	public function search_items( WP_REST_Request $request ) {

		// Get ConvertKit Products.
		$convertkit_products = new ConvertKit_Resource_Products();

		// Bail if no Products exist.
		if ( ! $convertkit_products->exist() ) {
			return array(
				self::RESULT_IDS   => array(),
				self::RESULT_TOTAL => 0,
			);
		}

		// @TODO Perform search and return results.
		return array(
			self::RESULT_IDS   => array( 1, 2, 3 ),
			self::RESULT_TOTAL => 3,
		);

	}

	/**
	 * Returns an array of data for the given Product ID, in
	 * a structure that is compatible with the WP REST API
	 *
	 * @since   1.9.8.5
	 *
	 * @param   int   $id         Item ID.
	 * @param   array $fields     Fields to include for the item.
	 * @return  array               Associative array containing all fields for the item.
	 */
	public function prepare_item( $id, array $fields ) {

		// Get Product.
		$convertkit_products = new ConvertKit_Resource_Products();
		$product = $convertkit_products->get( $id );

		// Build array of data in compatible format.
		$data = array();

		if ( in_array( WP_REST_Search_Controller::PROP_ID, $fields, true ) ) {
			$data[ WP_REST_Search_Controller::PROP_ID ] = (int) $id;
		}

		if ( in_array( WP_REST_Search_Controller::PROP_TITLE, $fields, true ) ) {
			$data[ WP_REST_Search_Controller::PROP_TITLE ] = $product['name'];
		}

		if ( in_array( WP_REST_Search_Controller::PROP_URL, $fields, true ) ) {
			$data[ WP_REST_Search_Controller::PROP_URL ] = $product['url'];
		}

		if ( in_array( WP_REST_Search_Controller::PROP_TYPE, $fields, true ) ) {
			$data[ WP_REST_Search_Controller::PROP_TYPE ] = $this->type;
		}

		if ( in_array( WP_REST_Search_Controller::PROP_SUBTYPE, $fields, true ) ) {
			$data[ WP_REST_Search_Controller::PROP_SUBTYPE ] = $this->type;
		}

		return $data;

	}

	/**
	 * Prepares links for the search result of a given ID.
	 *
	 * @since 5.0.0
	 *
	 * @param int $id Item ID.
	 * @return array Links for the given item.
	 */
	public function prepare_item_links( $id ) {

		// Get Product.
		$convertkit_products = new ConvertKit_Resource_Products();
		$product = $convertkit_products->get( $id );

		// Return links.
		return array(
			'self' => array(
				'href' => $product['url'],
				'embeddable' => true, // @TODO Determine if this needs to be true or false.
			),
			'about' => array(
				'href' => $product['url'],
			),
		);

	}

	/**
	 * Attempts to detect the route to access a single item.
	 *
	 * @since 5.0.0
	 * @deprecated 5.5.0 Use rest_get_route_for_post()
	 * @see rest_get_route_for_post()
	 *
	 * @param WP_Post $post Post object.
	 * @return string REST route relative to the REST base URI, or empty string if unknown.
	 
	protected function detect_rest_item_route( $post ) {

		_deprecated_function( __METHOD__, '5.5.0', 'rest_get_route_for_post()' );

		return rest_get_route_for_post( $post );

	}
	*/

}
