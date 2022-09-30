<?php
/**
 * ConvertKit Post Types class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Class to register the Product Custom Post Type, and store
 * Products in the Custom Post Type, permitting Gutenberg's
 * LinkControl to display ConvertKit Products as results
 * when searching for items to link text to.
 *
 * @since   1.9.8.5
 */
class ConvertKit_Post_Type_Product {

	/**
	 * Holds the Post Type name.
	 * 
	 * @since 	1.9.8.5
	 * 
	 * @var 	string
	 */
	private $post_type_name = 'convertkit_products';

	/**
	 * Constructor.
	 *
	 * @since   1.9.8.5
	 */
	public function __construct() {

		// Register Custom Post Type.
		add_action( 'init', array( $this, 'register' ) );

		// Update Products stored in Custom Post Type when Products Resource is refreshed.
		add_action( 'convertkit_resource_refreshed_products', array( $this, 'refresh' ) );

		// Change Product Custom Post link to the ConvertKit Product's URL.
		add_filter( 'post_type_link', array( $this, 'filter_permalink' ), 10, 2 );

	}

	/**
	 * Registers the ConvertKit Product Custom Post Type.
	 * 
	 * @since 	1.9.8.5
	 */
	public function register() {

		// Define Post Type arguments.
		$args = array(
			'labels'              => array(
				'name'               => __( 'ConvertKit Products', 'convertkit' ),
				'singular_name'      => __( 'ConvertKit Product', 'convertkit' ),
				'menu_name'          => __( 'ConvertKit Products', 'convertkit' ),
				'add_new'            => __( 'Add New', 'convertkit' ),
				'add_new_item'       => __( 'Add New ConvertKit Product', 'convertkit' ),
				'edit_item'          => __( 'Edit ConvertKit Product', 'convertkit' ),
				'new_item'           => __( 'New ConvertKit Product', 'convertkit' ),
				'view_item'          => __( 'View ConvertKit Product', 'convertkit' ),
				'search_items'       => __( 'Search ConvertKit Products', 'convertkit' ),
				'not_found'          => __( 'No ConvertKit Products found', 'convertkit' ),
				'not_found_in_trash' => __( 'No ConvertKit Products found in Trash', 'convertkit' ),
				'parent_item_colon'  => '',
			),
			'description'         => __( 'ConvertKit Products', 'convertkit' ),
			'public'              => true,
			//'publicly_queryable'  => true,
			//'exclude_from_search' => true,
			//'show_ui'             => true,
			//'show_in_menu'        => true,
			//'menu_position'       => 9999,
			'capability_type'     => 'page',
			//'hierarchical'        => false,
			'supports'            => array( 'title' ),
			//'has_archive'         => false,
			//'show_in_nav_menus'   => false,
			'show_in_rest'        => true,
		);

		/**
		 * Filter the arguments for registering the Products Custom Post Type
		 *
		 * @since   1.9.8.5
		 *
		 * @param   array $args     register_post_type() compatible arguments.
		 */
		$args = apply_filters( 'convertkit_post_type_product_register', $args );

		// Register Post Type.
		register_post_type( $this->post_type_name, $args );

	}

	/**
	 * Update the Custom Post Type's Products based on the supplied array
	 * of ConvertKit Products, when the Resource class performs a refresh.
	 * 
	 * @since 	1.9.8.5
	 * 
	 * @param 	array 	$products 	Products.
	 */
	public function refresh( $products ) {

		// Delete all Products in the Custom Post Type.
		$this->delete_all();

		// If no Products exist, bail.
		if ( ! $products ) {
			return;
		}

		// Create/update Products in the Custom Post Type.
		foreach ( $products as $product ) {
			$this->store( $product );
		}

	}

	/**
	 * Returns the ConvertKit Product URL instead of the Product Custom Post URL
	 * when a call to e.g. get_permalink() is made.
	 * 
	 * @since 	1.9.8.5
	 * 
	 * @param 	string 		$url 	URL.
	 * @param 	WP_Post 	$post 	Product Custom Post.
	 * @return 	string 				URL
	 */
	public function filter_permalink( $url, $post ) {

		// Don't filter if the Post's type isn't a ConvertKit Product.
		if ( $post->post_type !== $this->post_type_name ) {
			return $url;
		}

		// Return ConvertKit Product URL.
		return get_post_meta( $post->ID, 'url', true );

	}

	/**
	 * Stores the Product in the Custom Post Type, either creating or updating it
	 * depending on whether the Product already exists in the Custom Post Type.
	 * 
	 * @since 	1.9.8.5
	 * 
	 * @param 	array 			$product 	ConvertKit Product.
	 * @return 	WP_Error|int 				Error or Post ID.
	 */
	private function store( $product ) {

		// Check if the Product already exists in the Custom Post Type.
		$existing_product = new WP_Query( array(
			'post_type' => $this->post_type_name,
			'post_status' => 'publish',
			'meta_query' => array(
				array(
					'key' => 'id',
					'value' => $product['id'],
				)
			),
			'fields' => 'ids',
		) );

		// If the Product does not exist in the Custom Post Type, create it now.
		if ( ! count( $existing_product->posts ) ) {
			return wp_insert_post( array(
				'post_type' => $this->post_type_name,
				'post_title' => $product['name'],
				'post_status' => 'publish',
				'meta_input' => array(
					'id' => $product['id'],
					'url' => $product['url'],
				),
			) );
		}

		// The Product exists in the Custom Post Type; update it now.
		return wp_update_post( array(
			'ID' => $existing_product->posts[0],
			'post_title' => $product['name'],
			'meta_input' => array(
				'id' => $product['id'],
				'url' => $product['url'],
			),
		) );

	}

	/**
	 * Deletes all Products from the Custom Post Type.
	 * 
	 * @since 	1.9.8.5
	 */
	private function delete_all() {

		global $wpdb;

		$wpdb->query(
			$wpdb->prepare(
				'DELETE FROM wp_posts WHERE post_type=%s',
				$this->post_type_name
			)
		);

	}

}
