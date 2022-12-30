<?php
/**
 * ConvertKit Admin Restrict Content class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Modifies the Pages WP_List_Table to provide:
 * - an 'Add New Member Content' button next to the 'Add New' button
 * - a dropdown filter to show Pages restricted to a Form, Tag or Product
 * - a 'ConvertKit Member Content' label appended to the Page's title when a Form, Tag or Product is selected
 *
 * @package ConvertKit
 * @author ConvertKit
 */
class ConvertKit_Admin_Restrict_Content {

	/**
	 * Holds the ConvertKit Products resource class.
	 *
	 * @since   2.1.0
	 *
	 * @var     bool|ConvertKit_Resource_Products
	 */
	public $products = false;

	/**
	 * Holds the value chosen for the Restrict Content filter dropdown
	 * in the WP_List_Table.
	 *
	 * @since   2.1.0
	 *
	 * @var     int|string
	 */
	public $restrict_content_filter = 0;

	/**
	 * Registers action and filter hooks.
	 *
	 * @since   2.1.0
	 */
	public function __construct() {

		// Filter Page's post state.
		add_filter( 'display_post_states', array( $this, 'maybe_display_restrict_content_post_state' ), 10, 2 );

		// Filter WP_List_Table by Restrict Content setting.
		add_action( 'pre_get_posts', array( $this, 'filter_wp_list_table_output' ) );
		add_action( 'restrict_manage_posts', array( $this, 'output_wp_list_table_filters' ) );

	}

	/**
	 * Query Pages in the WP_List_Table by the Restrict Content filter, if a filter
	 * was included in the request.
	 *
	 * @since   2.1.0
	 *
	 * @param   WP_Query $query  WordPress Query.
	 */
	public function filter_wp_list_table_output( $query ) {

		// Bail if no Restrict Content filter specified.
		if ( ! array_key_exists( 'convertkit_restrict_content', $_REQUEST ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return;
		}
		if ( ! $_REQUEST['convertkit_restrict_content'] ) { // phpcs:ignore WordPress.Security.NonceVerification
			return;
		}

		// Don't filter if we're not querying a Post Type that supports Restricted Content.
		if ( ! in_array( $query->get( 'post_type' ), convertkit_get_supported_restrict_content_post_types(), true ) ) {
			return;
		}

		// Build query.
		// Because WordPress stores metadata in a single serialized string for a single key, we have to search
		// the string for the Restrict Content setting. However, other settings will also be in this serialized string,
		// so to avoid false positives, we define our search value formatted as a serialized string comprising of the
		// setting name and value, to be as accurate as possible.
		$value = maybe_serialize(
			array(
				'restrict_content' => sanitize_text_field( $_REQUEST['convertkit_restrict_content'] ), // phpcs:ignore WordPress.Security.NonceVerification
			)
		);

		// Strip a:1:{ and final }, as a Post's serialized settings will include other settings.
		$value = str_replace( 'a:1:{', '', $value ); // e.g. s:16:"restrict_content";s:13:"product_36377";}.
		$value = substr( $value, 0, strlen( $value ) - 1 ); // e.g. s:16:"restrict_content";s:13:"product_36377";.

		// Add value to query.
		$meta_query = array(
			'key'     => '_wp_convertkit_post_meta',
			'value'   => $value,
			'compare' => 'LIKE',
		);

		// If the existing meta query is an array, append our query to it, so we honor
		// any other constraints that have been defined by WordPress or third party code.
		$existing_meta_query = $query->get( 'meta_query' );
		if ( is_array( $existing_meta_query ) ) {
			$existing_meta_query[] = $meta_query;
			$query->set( 'meta_query', $existing_meta_query );
		} else {
			$query->set( 'meta_query', array( $meta_query ) );
		}

		// Store Restrict Content filter value.
		$this->restrict_content_filter = sanitize_text_field( $_REQUEST['convertkit_restrict_content'] ); // phpcs:ignore WordPress.Security.NonceVerification

	}

	/**
	 * Outputs a dropdown filter on a WP_List_Table filter section to permit
	 * filtering by a Restrict Content Form, Tag or Product.
	 *
	 * @since   2.1.0
	 *
	 * @param   string $post_type  Post Type.
	 */
	public function output_wp_list_table_filters( $post_type ) {

		// Don't output filters if we're not viewing a Post Type that supports Restricted Content.
		if ( ! in_array( $post_type, convertkit_get_supported_restrict_content_post_types(), true ) ) {
			return;
		}

		// Don't output filters if API credentials have not been defined in the Plugin's settings.
		$settings = new ConvertKit_Settings();
		if ( ! $settings->has_api_key_and_secret() ) {
			return;
		}

		// Fetch Products.
		$this->products = new ConvertKit_Resource_Products();

		// Don't display filter if no Products exist.
		if ( ! $this->products->exist() ) {
			return;
		}

		// Output filter.
		include_once CONVERTKIT_PLUGIN_PATH . '/views/backend/post/wp-list-table-filter.php';

	}

	/**
	 * Appends the 'ConvertKit Member Content' text to a Page's Title in the WP_List_Table,
	 * if the given Page has a Restrict Content setting.
	 *
	 * @param   string[] $post_states    An array of post display states.
	 * @param   WP_Post  $post           The current post object.
	 * @return  string[]                    An array of post display states
	 */
	public function maybe_display_restrict_content_post_state( $post_states, $post ) {

		// Fetch Post's settings.
		$convertkit_post = new ConvertKit_Post( $post->ID );

		// Return post states, unedited, if Restrict Content isn't enabled on this Post.
		if ( ! $convertkit_post->restrict_content_enabled() ) {
			return $post_states;
		}

		// Add Post State.
		$post_states['convertkit_restrict_content'] = esc_html__( 'ConvertKit Member Content', 'convertkit' );

		// Return.
		return $post_states;

	}

}
