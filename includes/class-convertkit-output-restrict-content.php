<?php
/**
 * ConvertKit Output Restrict Content class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Restricts (or displays) a single Page, Post or Custom Post Type's content
 * based on the Post's "Restrict Content" configuration.
 *
 * @since   2.1.0
 */
class ConvertKit_Output_Restrict_Content {

	/**
	 * Holds the success message to display on screen as a notification.
	 *
	 * @since   2.1.0
	 *
	 * @var     bool|string
	 */
	private $success = false; // @phpstan-ignore-line.

	/**
	 * Holds the WP_Error object if an API call / authentication failed,
	 * to display on screen as a notification.
	 *
	 * @since   2.1.0
	 *
	 * @var     bool|WP_Error
	 */
	private $error = false; // @phpstan-ignore-line.

	/**
	 * Holds the ConvertKit Plugin Settings class
	 *
	 * @since   2.1.0
	 *
	 * @var     bool|ConvertKit_Settings
	 */
	private $settings = false;

	/**
	 * Holds the ConvertKit Restrict Content Settings class
	 *
	 * @since   2.1.0
	 *
	 * @var     bool|ConvertKit_Settings_Restrict_Content
	 */
	private $restrict_content_settings = false;

	/**
	 * Holds the ConvertKit Post Settings class
	 *
	 * @since   2.1.0
	 *
	 * @var     bool|ConvertKit_Post
	 */
	private $post_settings = false;

	/**
	 * Holds the Post ID
	 *
	 * @since   2.1.0
	 *
	 * @var     bool|int
	 */
	private $post_id = false;

	/**
	 * Holds the ConvertKit API class
	 *
	 * @since   2.1.0
	 *
	 * @var     bool|ConvertKit_API
	 */
	private $api = false;

	/**
	 * Holds the token returned from calling the subscriber_authentication_send_code API endpoint.
	 *
	 * @since   2.1.0
	 *
	 * @var     bool|string
	 */
	private $token = false;

	/**
	 * Constructor. Registers actions and filters to possibly limit output of a Page/Post/CPT's
	 * content on the frontend site.
	 *
	 * @since   2.1.0
	 */
	public function __construct() {

		// Initialize classes that will be used.
		$this->settings                  = new ConvertKit_Settings();
		$this->restrict_content_settings = new ConvertKit_Settings_Restrict_Content();

		// Bail if Restrict Content isn't enabled.
		if ( ! $this->restrict_content_settings->enabled() ) {
			return;
		}

		add_action( 'init', array( $this, 'maybe_run_subscriber_authentication' ), 1 );
		add_action( 'init', array( $this, 'maybe_run_subscriber_verification' ), 2 );
		add_filter( 'the_content', array( $this, 'maybe_restrict_content' ) );
		add_filter( 'get_previous_post_where', array( $this, 'maybe_change_previous_post_where_clause' ), 10, 5 );
		add_filter( 'get_next_post_where', array( $this, 'maybe_change_next_post_where_clause' ), 10, 5 );
		add_filter( 'get_previous_post_sort', array( $this, 'maybe_change_previous_next_post_order_by_clause' ), 10, 3 );
		add_filter( 'get_next_post_sort', array( $this, 'maybe_change_previous_next_post_order_by_clause' ), 10, 3 );

	}

	/**
	 * Checks if the request is a Restrict Content login request with an email address,
	 * calling the API to send the subscriber a magic link by email.
	 *
	 * Once they click the link in the email, maybe_run_subscriber_verification() will run.
	 *
	 * @since   2.1.0
	 */
	public function maybe_run_subscriber_authentication() {

		// Bail if no nonce was specified.
		if ( ! array_key_exists( '_wpnonce', $_REQUEST ) ) {
			return;
		}

		// Bail if the nonce failed validation.
		if ( ! wp_verify_nonce( sanitize_key( $_REQUEST['_wpnonce'] ), 'convertkit_restrict_content_login' ) ) {
			$this->error = new WP_Error( 'convertkit_output_restrict_content_error', __( 'Invalid nonce specified. Please try again.', 'convertkit' ) );
			return;
		}

		// If the Plugin API keys have not been configured, we can't get this subscriber's ID by email.
		if ( ! $this->settings->has_api_key_and_secret() ) {
			return;
		}

		// Initialize the API.
		$this->api = new ConvertKit_API( $this->settings->get_api_key(), $this->settings->get_api_secret(), $this->settings->debug_enabled() );

		// Send email to subscriber with a link to authenticate they have access to the email address submitted.
		$result = $this->api->subscriber_authentication_send_code(
			sanitize_text_field( $_REQUEST['convertkit_email'] ),
			$this->get_url()
		);

		// Bail if an error occured.
		if ( is_wp_error( $result ) ) {
			$this->error = $result;
			return;
		}

		// Clear any existing subscriber ID cookie, as the authentication flow has started by sending the email.
		$subscriber = new ConvertKit_Subscriber();
		$subscriber->forget();

		// Store the token so it's included in the subscriber code form.
		$this->token = $result;

		// Show a message telling the subscriber to check their email and click the link in the email.
		$this->success = $this->restrict_content_settings->get_by_key( 'email_check_text' );

	}

	/**
	 * Checks if the request contains a token and subscriber_code i.e. the subscriber clicked
	 * the link in the email sent by the maybe_run_subscriber_authentication() function above.
	 *
	 * This calls the API to verify the token and subscriber code, which tells us that the email
	 * address supplied truly belongs to the user, and that we can safely trust their subscriber ID
	 * to be valid.
	 *
	 * @since   2.1.0
	 */
	public function maybe_run_subscriber_verification() {

		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		// Bail if the expected token and subscriber code is missing.
		if ( ! array_key_exists( 'token', $_REQUEST ) ) {
			return;
		}
		if ( ! array_key_exists( 'subscriber_code', $_REQUEST ) ) {
			return;
		}

		// If the Plugin API keys have not been configured, we can't get this subscriber's ID by email.
		if ( ! $this->settings->has_api_key_and_secret() ) {
			return;
		}

		// Store the token so it's included in the subscriber code form if verification fails.
		$this->token = sanitize_text_field( $_REQUEST['token'] );

		// Initialize the API.
		$this->api = new ConvertKit_API( $this->settings->get_api_key(), $this->settings->get_api_secret(), $this->settings->debug_enabled() );

		// Verify the token and subscriber code.
		$subscriber_id = $this->api->subscriber_authentication_verify(
			sanitize_text_field( $_REQUEST['token'] ),
			sanitize_text_field( $_REQUEST['subscriber_code'] )
		);
		// phpcs:enable

		// Bail if an error occured.
		if ( is_wp_error( $subscriber_id ) ) {
			$this->error = $subscriber_id;
			return;
		}

		// Store subscriber ID in cookie.
		// We don't need to use validate_and_store_subscriber_id() as we just validated the subscriber via authentication above.
		$subscriber = new ConvertKit_Subscriber();
		$subscriber->set( $subscriber_id );

		// We append a query parameter to the URL to prevent caching plugins and
		// aggressive cache hosting configurations from serving a cached page, which would
		// result in maybe_restrict_content() not showing an error message or permitting
		// access to the content.
		$url = add_query_arg( array(
			'ck-cache-bust' => microtime(),
		), $this->get_url() );

		// Redirect to the Post without the token and subscriber parameters.
		// This will then run maybe_restrict_content() to get the subscriber's ID from the cookie,
		// and determine if the content can be displayed.
		wp_safe_redirect( $url );
		exit;

	}

	/**
	 * Displays (or hides) content on a singular Page, Post or Custom Post Type's Content,
	 * depending on whether the visitor is an authenticated ConvertKit subscriber and has
	 * subscribed to the ConvertKit Product.
	 *
	 * @since   2.1.0
	 *
	 * @param   string $content    Post Content.
	 * @return  string              Post Content with content restricted/not restricted
	 */
	public function maybe_restrict_content( $content ) {

		// Bail if the Restrict Content setting is not enabled on this Page.
		if ( ! $this->is_restricted_content() ) {
			return $content;
		}

		// Get resource type (Product) that the visitor must be subscribed against to access this content.
		$resource_type = $this->get_resource_type( $this->post_id );

		// Return the Post Content, unedited, if the Resource Type is false.
		if ( ! $resource_type ) {
			return $content;
		}

		// Get resource ID (Product ID) that the visitor must be subscribed against to access this content.
		$resource_id = $this->get_resource_id( $this->post_id );

		// Return the full Post Content, unedited, if the Resource ID is false, as this means
		// no restrict content setting has been defined for this Post.
		if ( ! $resource_id ) {
			return $content;
		}

		// Return if this request is after the user entered their email address,
		// which means we're going through the authentication flow.
		if ( $this->in_authentication_flow() ) {
			return $this->restrict_content( $content, $resource_type, $resource_id );
		}

		// Get the subscriber ID, either from the request or an existing cookie.
		$subscriber_id = $this->get_subscriber_id_from_request();

		// If no subscriber ID exists, the visitor cannot view the content.
		if ( ! $subscriber_id ) {
			return $this->restrict_content( $content, $resource_type, $resource_id );
		}

		// If the subscriber is not subscribed to the product, restrict the content.
		if ( ! $this->subscriber_has_access( $subscriber_id, $resource_type, $resource_id ) ) {
			// Show an error before the call to action, to tell the subscriber why they still cannot
			// view the content.
			$this->error = new WP_Error(
				'convertkit_restrict_content_subscriber_no_access',
				esc_html( $this->restrict_content_settings->get_by_key( 'no_access_text' ) )
			);

			return $this->restrict_content( $content, $resource_type, $resource_id );
		}

		// If here, the subscriber has subscribed to the product.
		// Show the full Post Content.
		return $content;

	}

	/**
	 * Changes how WordPress' get_adjacent_post() function queries Pages, to determine what
	 * the previous Page link is when using the Previous navigation block on a Page that
	 * has the Restrict Content setting defined.
	 *
	 * By default, get_adjacent_post() will query by post_date, which we change to menu_order.
	 *
	 * @since   2.1.0
	 *
	 * @param   string  $where          The `WHERE` clause in the SQL.
	 * @param   bool    $in_same_term   Whether post should be in a same taxonomy term.
	 * @param   array   $excluded_terms Array of excluded term IDs.
	 * @param   string  $taxonomy       Taxonomy. Used to identify the term used when `$in_same_term` is true.
	 * @param   WP_Post $post           WP_Post object.
	 * @return  string                  Modified `WHERE` clause
	 */
	public function maybe_change_previous_post_where_clause( $where, $in_same_term, $excluded_terms, $taxonomy, $post ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter

		// Bail if the Restrict Content setting is not enabled on this Page.
		if ( ! $this->is_restricted_content() ) {
			return $where;
		}

		// Bail if the Page doesn't match the current Page being viewed, or has no parent Page.
		if ( ! $this->has_parent_page( $post ) ) {
			return $where;
		}

		// Build replacement where statement.
		$new_where = 'p.post_parent = ' . $post->post_parent . ' AND p.menu_order < ' . $post->menu_order;

		// Replace existing where statement with new statement.
		$where = 'WHERE ' . $new_where . ' ' . substr( $where, strpos( $where, 'AND' ) );

		// Return.
		return $where;

	}

	/**
	 * Changes how WordPress' get_adjacent_post() function queries Pages, to determine what
	 * the next Page link is when using the Previous navigation block on a Page that
	 * has the Restrict Content setting defined.
	 *
	 * By default, get_adjacent_post() will query by post_date, which we change to menu_order.
	 *
	 * @since   2.1.0
	 *
	 * @param   string  $where          The `WHERE` clause in the SQL.
	 * @param   bool    $in_same_term   Whether post should be in a same taxonomy term.
	 * @param   array   $excluded_terms Array of excluded term IDs.
	 * @param   string  $taxonomy       Taxonomy. Used to identify the term used when `$in_same_term` is true.
	 * @param   WP_Post $post           WP_Post object.
	 * @return  string                  Modified `WHERE` clause
	 */
	public function maybe_change_next_post_where_clause( $where, $in_same_term, $excluded_terms, $taxonomy, $post ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter

		// Bail if the Restrict Content setting is not enabled on this Page.
		if ( ! $this->is_restricted_content() ) {
			return $where;
		}

		// Bail if the Page doesn't match the current Page being viewed, or has no parent Page.
		if ( ! $this->has_parent_page( $post ) ) {
			return $where;
		}

		// Build replacement where statement.
		$new_where = 'p.post_parent = ' . $post->post_parent . ' AND p.menu_order > ' . $post->menu_order;

		// Replace existing where statement with new statement.
		$where = 'WHERE ' . $new_where . ' ' . substr( $where, strpos( $where, 'AND' ) );

		// Return.
		return $where;

	}

	/**
	 * Changes how WordPress' get_adjacent_post() function orders Pages, to determine what
	 * the next and previous Page links are when using Previous / Next navigation blocks
	 * on a Page that has the Restrict Content setting defined.
	 *
	 * By default, get_adjacent_post() will sort by Post Date, which we change to Page Order
	 * (called menu_order in WordPress).
	 *
	 * @since   2.1.0
	 *
	 * @param   string  $order_by   SQL ORDER BY statement.
	 * @param   WP_Post $post       WordPress Post.
	 * @param   string  $order      Order.
	 * @return  string              Modified SQL ORDER BY statement.
	 */
	public function maybe_change_previous_next_post_order_by_clause( $order_by, $post, $order ) {

		// Bail if the Restrict Content setting is not enabled on this Page.
		if ( ! $this->is_restricted_content() ) {
			return $order_by;
		}

		// Bail if the Page doesn't match the current Page being viewed, or has no parent Page.
		if ( ! $this->has_parent_page( $post ) ) {
			return $order_by;
		}

		// Order by Page order (menu_order), highest to lowest, instead of post_date.
		return 'ORDER BY p.menu_order ' . $order . ' LIMIT 1';

	}

	/**
	 * Returns the URL for the current request, excluding any query parameters.
	 *
	 * @since   2.1.0
	 *
	 * @return  string  URL.
	 */
	private function get_url() {

		$url = wp_parse_url( get_site_url() . $_SERVER['REQUEST_URI'] );
		return $url['scheme'] . '://' . $url['host'] . $url['path'];

	}

	/**
	 * Determines if the request is for a WordPress Page that has the Restrict Content
	 * setting defined.
	 *
	 * @since   2.1.0
	 *
	 * @return  bool
	 */
	private function is_restricted_content() {

		// Bail if not a singular Post Type.
		if ( ! is_singular() ) {
			return false;
		}

		// If a Post ID is already defined in this class, this check has already been performed,
		// and the Post's settings class has been initialized.
		if ( $this->post_id ) {
			return true;
		}

		// Get Post ID.
		$this->post_id = get_the_ID();

		// Initialize Settings and Post Setting classes.
		$this->post_settings = new ConvertKit_Post( $this->post_id );

		// If the Plugin API keys have not been configured, we can't determine the validity of this subscriber ID
		// or which resource(s) they have access to.
		if ( ! $this->settings->has_api_key_and_secret() ) {
			return false;
		}

		// Return whether the Post's settings are set to restrict content.
		return $this->post_settings->restrict_content_enabled();

	}

	/**
	 * Determines if the user entered a valid email address, and need to be prompted
	 * to enter a code sent to their email address.
	 *
	 * @since   2.1.0
	 *
	 * @return  bool
	 */
	private function in_authentication_flow() {

		return ( $this->token !== false );

	}

	/**
	 * Checks if the given WordPress Page matches the Page ID viewed, and has a parent.
	 *
	 * @since   2.1.0
	 *
	 * @param   WP_Post $post   WordPress Post.
	 * @return  bool                Has parent page
	 */
	private function has_parent_page( $post ) {

		// Bail if the Page doesn't match the current Page being viewed.
		// This prevents us accidentally interfering with other previous / next link queries, which shouldn't happen
		// as we check if we're viewing a restricted content page above.
		if ( $post->ID !== $this->post_id ) {
			return false;
		}

		// Bail if the Page doesn't have a parent Page.
		// We don't want to modify the default sort behaviour in this instance.
		if ( $post->post_parent === 0 ) {
			return false;
		}

		return true;

	}

	/**
	 * Get the Post's Restricted Content resource type.
	 *
	 * @since   2.1.0
	 *
	 * @param   int $post_id    Post ID.
	 * @return  bool|string     Resource Type (product).
	 */
	private function get_resource_type( $post_id ) {

		// Get resource type.
		$resource_type = $this->post_settings->get_restrict_content_type();

		/**
		 * Define the ConvertKit Resource Type that the visitor must be subscribed against
		 * to access this content, overriding the Post setting.
		 *
		 * Return false or an empty string to not restrict content.
		 *
		 * @since   2.1.0
		 *
		 * @param   string $resource_type   Resource Type (product)
		 * @param   int    $post_id         Post ID
		 */
		$resource_type = apply_filters( 'convertkit_output_restrict_content_get_resource_type', $resource_type, $post_id );

		// If resource type is blank, set it to false.
		if ( empty( $resource_type ) ) {
			$resource_type = false;
		}

		// Return.
		return $resource_type;

	}

	/**
	 * Get the Post's Restricted Content resource ID.
	 *
	 * @since   2.1.0
	 *
	 * @param   int $post_id    Post ID.
	 * @return  int             Resource ID (product ID).
	 */
	private function get_resource_id( $post_id ) {

		// Get resource ID.
		$resource_id = $this->post_settings->get_restrict_content_id();

		/**
		 * Define the ConvertKit Resource ID that the visitor must be subscribed against
		 * to access this content, overriding the Post setting.
		 *
		 * Return 0 to not restrict content.
		 *
		 * @since   2.1.0
		 *
		 * @param   int    $resource_id     Resource ID
		 * @param   int    $post_id         Post ID
		 */
		$resource_id = apply_filters( 'convertkit_output_restrict_content_get_resource_id', $resource_id, $post_id );

		// Return.
		return $resource_id;

	}

	/**
	 * Determines if the given subscriber has an active subscription to
	 * the given resource and its ID.
	 *
	 * @since   2.1.0
	 *
	 * @param   string|int $subscriber_id  Signed Subscriber ID or Subscriber ID.
	 * @param   string     $resource_type  Resource Type (product).
	 * @param   int        $resource_id    Resource ID (Product ID).
	 * @return  bool                        Can view restricted content
	 */
	private function subscriber_has_access( $subscriber_id, $resource_type, $resource_id ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter

		// Initialize the API.
		$this->api = new ConvertKit_API( $this->settings->get_api_key(), $this->settings->get_api_secret(), $this->settings->debug_enabled() );

		// Depending on the resource type, determine if the subscriber has access to it.
		// This is deliberately a switch statement, because we will likely add in support
		// for restrict by tag and form later.
		switch ( $resource_type ) {
			case 'product':
				// Get products that the subscriber has access to.
				$result = $this->api->profile( $subscriber_id );

				// If an error occured, the subscriber ID is invalid.
				if ( is_wp_error( $result ) ) {
					return false;
				}

				// If no products exist, there's no access.
				if ( ! $result['products'] || ! count( $result['products'] ) ) {
					return false;
				}

				// Return if the subscriber is not subscribed to the product.
				if ( ! in_array( absint( $resource_id ), $result['products'], true ) ) {
					return false;
				}

				// If here, the subscriber is subscribed to the product.
				return true;
		}

		// If here, the subscriber does not have access.
		return false;

	}

	/**
	 * Gets the subscriber ID from the request (either the cookie or the URL).
	 *
	 * @since   2.1.0
	 *
	 * @return  int|string   Subscriber ID or Signed ID
	 */
	public function get_subscriber_id_from_request() {

		// Use ConvertKit_Subscriber class to fetch and validate the subscriber ID.
		$subscriber    = new ConvertKit_Subscriber();
		$subscriber_id = $subscriber->get_subscriber_id();

		// If an error occured, the subscriber ID in the request/cookie is not a valid subscriber.
		if ( is_wp_error( $subscriber_id ) ) {
			return 0;
		}

		return $subscriber_id;

	}

	/**
	 * Restrict the given Post Content by showing a preview of the content, and appending
	 * the call to action to subscribe or authenticate.
	 *
	 * @since   2.1.0
	 *
	 * @param   string $content        Post Content.
	 * @param   string $resource_type  Resource Type (product).
	 * @param   int    $resource_id    Resource ID (Product ID).
	 * @return  string                  Post Content preview with call to action
	 */
	private function restrict_content( $content, $resource_type, $resource_id ) {

		return $this->get_content_preview( $content ) . $this->get_call_to_action( $this->post_id, $resource_type, $resource_id );

	}

	/**
	 * Returns a preview of the given content for visitors that don't have access to restricted content.
	 *
	 * The preview is determined by:
	 * - A single <!--more--> tag being placed between WordPress paragraphs when using the Classic Editor.
	 * Content before the tag will be returned as the preview, unless 'noteaser' is enabled.
	 * - A single 'Read More' block being placed between WordPress blocks when using the Gutenberg Editor.
	 * Content before the Read More block will be returned as the preview, unless 'Hide th excerpt
	 * on the full content page' is enabled.
	 *
	 * No preview content is returned if the above conditions are not met.
	 *
	 * @since   2.1.0
	 *
	 * @param   string $content    Post Content.
	 * @return  string              Post Content Preview.
	 */
	private function get_content_preview( $content ) {

		global $post;

		// Check if the content contains a <!--more--> tag, which the editor might have placed
		// in the content through WordPress' Classic Editor.
		$content_breakdown = get_extended( $content );

		// If the <!-- more --> tag exists, the 'extended' key will contain the restricted content.
		if ( ! empty( $content_breakdown['extended'] ) ) {
			// Return the preview content.
			return $content_breakdown['main'];
		}

		// Check if the content contains a 'Read More' block, which the editor might have placed
		// in the content through the Gutenberg Editor.
		$block_editor_tag = '<span id="more-' . $post->ID . '"></span>';
		if ( strpos( $content, $block_editor_tag ) !== false ) {
			// Split content into an array by the tag.
			$content_breakdown = explode( $block_editor_tag, $content );

			// Return the content before the tag.
			// If noteaser is enabled, this will correctly be blank.
			return $content_breakdown[0];
		}

		// If here, there is no preview content available. Don't return any content.
		return '';

	}

	/**
	 * Returns the HTML output for the call to action for visitors not subscribed to the required
	 * resource type and ID.
	 *
	 * @since   2.1.0
	 *
	 * @param   int    $post_id        Post ID.
	 * @param   string $resource_type  Resource Type (product).
	 * @param   int    $resource_id    Resource ID (Product ID).
	 * @return  string                  HTML
	 */
	private function get_call_to_action( $post_id, $resource_type, $resource_id ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter

		// This is deliberately a switch statement, because we will likely add in support
		// for restrict by tag and form later.
		switch ( $resource_type ) {
			case 'product':
				// Only load styles if the Disable CSS option is off.
				if ( ! $this->settings->css_disabled() ) {
					// Enqueue styles.
					wp_enqueue_style( 'convertkit-restrict-content', CONVERTKIT_PLUGIN_URL . 'resources/frontend/css/restrict-content.css', array(), CONVERTKIT_PLUGIN_VERSION );
				}

				// Output product code form if this request is after the user entered their email address,
				// which means we're going through the authentication flow.
				if ( $this->in_authentication_flow() ) { // phpcs:ignore WordPress.Security.NonceVerification
					ob_start();
					include CONVERTKIT_PLUGIN_PATH . '/views/frontend/restrict-content/product-code.php';
					return trim( ob_get_clean() );
				}

				// Output product restricted message and email form.
				// Get Product.
				$products = new ConvertKit_Resource_Products( 'restrict_content' );
				$product  = $products->get_by_id( $resource_id );

				// Get commerce.js URL and enqueue.
				$url = $products->get_commerce_js_url();
				if ( $url ) {
					wp_enqueue_script( 'convertkit-commerce', $url, array(), CONVERTKIT_PLUGIN_VERSION, true );
				}

				// Output.
				ob_start();
				$button = $products->get_html( $resource_id, $this->restrict_content_settings->get_by_key( 'subscribe_button_label' ) );
				include CONVERTKIT_PLUGIN_PATH . '/views/frontend/restrict-content/product.php';
				return trim( ob_get_clean() );

			default:
				return '';

		}

	}

}
