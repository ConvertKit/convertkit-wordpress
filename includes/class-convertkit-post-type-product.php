<?php
/**
 * ConvertKit Post Types class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Class to register the Product Custom Post Type, and store
 * Products in the Custom Post Type, permitting both Gutenberg's
 * LinkControl and Classic Editor link functionality to display
 * ConvertKit Products as results when searching for items to link text to.
 *
 * Also adds data-commerce attributes to any links pointing to a ConvertKit
 * Product URL.
 *
 * @since   2.0.0
 */
class ConvertKit_Post_Type_Product {

	/**
	 * Holds the Post Type name.
	 *
	 * @since   2.0.0
	 *
	 * @var     string
	 */
	private $post_type_name = 'convertkit_product';

	/**
	 * Constructor.
	 *
	 * @since   2.0.0
	 */
	public function __construct() {

		// Register Custom Post Type.
		add_action( 'init', array( $this, 'register' ) );

		// Register commerce.js.
		add_action( 'init', array( $this, 'register_commerce_script' ) );

		// Update Products stored in Custom Post Type when Products Resource is refreshed.
		add_action( 'convertkit_resource_refreshed_products', array( $this, 'refresh' ) );

		// Change Product Custom Post link to the ConvertKit Product's URL.
		add_filter( 'post_type_link', array( $this, 'filter_permalink' ), 10, 2 );

		// Adds the data-commerce attribute to HTML links that link to a ConvertKit Product.
		add_filter( 'the_content', array( $this, 'add_data_commerce_to_permalink' ) );

	}

	/**
	 * Registers the ConvertKit Product Custom Post Type.
	 *
	 * @since   2.0.0
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
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_ui'             => false,
			'show_in_menu'        => false,
			'capability_type'     => 'page',
			'hierarchical'        => false,
			'supports'            => array( 'title' ),
			'has_archive'         => false,
			'show_in_nav_menus'   => false,
			'show_in_rest'        => true,
		);

		/**
		 * Filter the arguments for registering the Products Custom Post Type
		 *
		 * @since   2.0.0
		 *
		 * @param   array   $args   register_post_type() compatible arguments.
		 */
		$args = apply_filters( 'convertkit_post_type_product_register', $args );

		// Register Post Type.
		register_post_type( $this->post_type_name, $args );

	}

	/**
	 * Registers the commerce.js script.
	 *
	 * @since   2.0.0
	 */
	public function register_commerce_script() {

		// Get Products Resource class.
		$products = new ConvertKit_Resource_Products();

		// Get commerce.js URL.
		$url = $products->get_commerce_js_url();

		// If no URL exists, bail.
		if ( ! $url ) {
			return;
		}

		// Enqueue JS.
		wp_register_script( 'convertkit-commerce', $url, array(), CONVERTKIT_PLUGIN_VERSION, true );

	}

	/**
	 * Update the Custom Post Type's Products based on the supplied array
	 * of ConvertKit Products, when the Resource class performs a refresh.
	 *
	 * @since   2.0.0
	 *
	 * @param   array $products   Products.
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
	 * @since   2.0.0
	 *
	 * @param   string  $url    URL.
	 * @param   WP_Post $post   Product Custom Post.
	 * @return  string              URL
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
	 * Adds the data-commerce attribute to HTML links that link to a ConvertKit Product.
	 *
	 * @since   2.0.0
	 *
	 * @param   string $content    Page/Post Content.
	 * @return  string              Page/Post Content
	 */
	public function add_data_commerce_to_permalink( $content ) {

		// Get Products.
		$products = new ConvertKit_Resource_Products();

		// Return content, unedited, if no Products exist.
		if ( ! $products->exist() ) {
			return $content;
		}

		// For each Product, add the data-commerce tag to any href attributes that link to a ConvertKit Product.
		foreach ( $products->get() as $product ) {
			// Skip if this Product's URL does not exist in the content.
			if ( strpos( $content, $product['url'] ) === false ) {
				continue;
			}

			// Enqueue commerce.js.
			if ( ! wp_script_is( 'convertkit-commerce', 'enqueued' ) ) {
				wp_enqueue_script( 'convertkit-commerce' );
			}

			// Add data-commerce attribute.
			$content = str_replace( 'href="' . esc_attr( $product['url'] ) . '"', 'href="' . esc_attr( $product['url'] ) . '" data-commerce', $content );
		}

		// Return content.
		return $content;

	}

	/**
	 * Stores the Product in the Custom Post Type, either creating or updating it
	 * depending on whether the Product already exists in the Custom Post Type.
	 *
	 * @since   2.0.0
	 *
	 * @param   array $product    ConvertKit Product.
	 * @return  WP_Error|int                Error or Post ID.
	 */
	private function store( $product ) {

		// Check if the Product already exists in the Custom Post Type.
		$existing_product = new WP_Query(
			array(
				'post_type'   => $this->post_type_name,
				'post_status' => 'publish',
				'meta_query'  => array(
					array(
						'key'   => 'id',
						'value' => $product['id'],
					),
				),
				'fields'      => 'ids',
			)
		);

		// If the Product does not exist in the Custom Post Type, create it now.
		if ( ! count( $existing_product->posts ) ) {
			return wp_insert_post(
				array(
					'post_type'   => $this->post_type_name,
					'post_title'  => $product['name'],
					'post_status' => 'publish',
					'meta_input'  => array(
						'id'  => $product['id'],
						'url' => $product['url'],
					),
				),
				true
			);
		}

		// The Product exists in the Custom Post Type; update it now.
		return wp_update_post(
			array(
				'ID'         => $existing_product->posts[0],
				'post_title' => $product['name'],
				'meta_input' => array(
					'id'  => $product['id'],
					'url' => $product['url'],
				),
			),
			true
		);

	}

	/**
	 * Deletes all Products from the Custom Post Type.
	 *
	 * @since   2.0.0
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
