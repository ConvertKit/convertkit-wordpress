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
	 * Holds the ConvertKit Tags resource class.
	 *
	 * @since   2.3.2
	 *
	 * @var     bool|ConvertKit_Resource_Tags
	 */
	public $tags = false;

	/**
	 * Holds the ConvertKit Products resource class.
	 *
	 * @since   2.1.0
	 *
	 * @var     bool|ConvertKit_Resource_Products
	 */
	public $products = false;

	/**
	 * Holds the Restrict Content Settings class.
	 *
	 * @since   2.1.0
	 *
	 * @var     bool|ConvertKit_Settings_Restrict_Content
	 */
	public $restrict_content_settings = false;

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

		// Add New Member Content Wizard button to Pages.
		add_filter( 'convertkit_admin_post_register_add_new_buttons', array( $this, 'register_add_new_button' ), 10, 2 );

		// Filter Page's post state to maybe include a label denoting that Restricted Content is enabled.
		// We do this here so we don't run this on Post Types that don't support Restricted Content.
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
		if ( ! in_array( $query->get( 'post_type' ), convertkit_get_supported_post_types(), true ) ) {
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
	 * Registers a button in the Pages WP_List_Table linking to the the Restrict Content Setup Wizard.
	 *
	 * @since   2.5.5
	 *
	 * @param   array  $buttons    Buttons.
	 * @param   string $post_type  Post Type.
	 * @return  array               Views
	 */
	public function register_add_new_button( $buttons, $post_type ) {

		// If no API credentials have been set, don't output the button.
		$settings = new ConvertKit_Settings();
		if ( ! $settings->has_access_and_refresh_token() ) {
			return $buttons;
		}

		// Bail if the Post Type isn't supported.
		if ( ! in_array( $post_type, convertkit_get_supported_post_types(), true ) ) {
			return $buttons;
		}

		// Register button.
		$buttons['convertkit_restrict_content_setup'] = array(
			'url'   => add_query_arg(
				array(
					'page'         => 'convertkit-restrict-content-setup',
					'ck_post_type' => $post_type,
				),
				admin_url( 'options.php' )
			),
			'label' => __( 'Add New Member Content', 'convertkit' ),
		);

		return $buttons;

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
		if ( ! in_array( $post_type, convertkit_get_supported_post_types(), true ) ) {
			return;
		}

		// Don't output filters if API credentials have not been defined in the Plugin's settings.
		$settings = new ConvertKit_Settings();
		if ( ! $settings->has_access_and_refresh_token() ) {
			return;
		}

		// Fetch Products and Tags.
		$this->products = new ConvertKit_Resource_Products();
		$this->tags     = new ConvertKit_Resource_Tags();

		// Don't display filter if no Tags and no Products exist.
		if ( ! $this->products->exist() && ! $this->tags->exist() ) {
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

		// Bail if we're not on a WP_List_Table screen for a supported Post Type.
		if ( ! $this->is_wp_list_table_request_for_supported_post_type() ) {
			return $post_states;
		}

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

	/**
	 * Determines if the current request is for a WP_List_Table, and if so that
	 * the Post Type we're viewing supports Restrict Content functionality.
	 *
	 * @since   2.1.0
	 *
	 * @return  bool    Is WP_List_Table request for a supported Post Type.
	 */
	private function is_wp_list_table_request_for_supported_post_type() {

		// Bail if we cannot determine the screen.
		if ( ! function_exists( 'get_current_screen' ) ) {
			return false;
		}

		// Get screen.
		$screen = get_current_screen();

		// Bail if we're not on an edit.php screen.
		if ( $screen->base !== 'edit' ) {
			return false;
		}

		// Return whether Post Type is supported for Restrict Content functionality.
		return in_array( $screen->post_type, convertkit_get_supported_post_types(), true );

	}

}
