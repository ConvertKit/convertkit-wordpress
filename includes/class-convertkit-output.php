<?php
/**
 * ConvertKit Output class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Outputs Forms and Landing Pages on the frontend web site, based on
 * the Post and Plugin's configuration.
 *
 * @since   1.9.6
 */
class ConvertKit_Output {

	/**
	 * Holds the ConvertKit Plugin Settings class
	 *
	 * @since   1.9.6
	 *
	 * @var     bool|ConvertKit_Settings
	 */
	private $settings = false;

	/**
	 * Holds the ConvertKit Post Settings class
	 *
	 * @since   1.9.6
	 *
	 * @var     bool|ConvertKit_Post
	 */
	private $post_settings = false;

	/**
	 * Holds the available ConvertKit Forms
	 *
	 * @since   1.9.6
	 *
	 * @var     bool|ConvertKit_Resource_Forms
	 */
	private $forms = false;

	/**
	 * Holds the available ConvertKit Landing Pages
	 *
	 * @since   1.9.6
	 *
	 * @var     bool|ConvertKit_Resource_Landing_Pages
	 */
	private $landing_pages = false;

	/**
	 * Constructor. Registers actions and filters to output ConvertKit Forms and Landing Pages
	 * on the frontend web site.
	 *
	 * @since   1.9.6
	 */
	public function __construct() {

		add_action( 'template_redirect', array( $this, 'output_form' ) );
		add_action( 'template_redirect', array( $this, 'page_takeover' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_filter( 'the_content', array( $this, 'append_form_to_content' ) );

	}

	/**
	 * Runs the `convertkit_output_output_form` action for singular Post Types that don't use the_content()
	 * or apply_filters( 'the_content' ) to output a ConvertKit Form.
	 *
	 * @since   1.9.6
	 */
	public function output_form() {

		/**
		 * Outputs a ConvertKit Form on singular Post Types that don't use the_content()
		 * or apply_filters( 'the_content' ).
		 *
		 * @since   1.9.6
		 *
		 * @return  string              Post Content with Form Appended, if applicable
		 */
		do_action( 'convertkit_output_output_form' );

	}

	/**
	 * Outputs a ConvertKit Landing Page if configured, replacing all output for the singular Post Type.
	 *
	 * @since   1.9.6
	 */
	public function page_takeover() {

		$queried_object = get_queried_object();

		// Bail if the queried object cannot be inspected.
		if ( ! isset( $queried_object->post_type ) ) {
			return;
		}

		// Get Post ID.
		$post_id = $queried_object->ID;

		// Bail if the queried object isn't a supported Post Type for Landing Pages.
		if ( $queried_object->post_type !== 'page' ) {
			return;
		}

		// Get ConvertKit Post's Settings, if they have not yet been loaded.
		if ( ! $this->post_settings ) {
			$this->post_settings = new ConvertKit_Post( $post_id );
		}

		// Get Landing Page ID.
		$landing_page_id = $this->post_settings->get_landing_page();

		/**
		 * Define the ConvertKit Landing Page ID to display for the given Post ID,
		 * overriding the Post settings.
		 *
		 * Return false to not display any ConvertKit Landing Page.
		 *
		 * @since   1.9.6
		 *
		 * @param   int     $landing_page_id    Landing Page ID
		 * @param   int     $post_id            Post ID
		 */
		$landing_page_id = apply_filters( 'convertkit_output_append_form_to_content_form_id', $landing_page_id, $post_id );

		// Bail if no Landing Page is configured to be output.
		if ( empty( $landing_page_id ) ) {
			return;
		}

		// Get available ConvertKit Landing Pages, if they have not yet been loaded.
		if ( ! $this->landing_pages ) {
			$this->landing_pages = new ConvertKit_Resource_Landing_Pages();
		}

		// Get Landing Page.
		$landing_page = $this->landing_pages->get_html( $this->post_settings->get_landing_page() );

		// Bail if an error occured.
		if ( is_wp_error( $landing_page ) ) {
			return;
		}

		// Output Landing Page.
		echo $landing_page; // phpcs:ignore
		exit;

	}

	/**
	 * Appends a form to the singular Page, Post or Custom Post Type's Content.
	 *
	 * @param   string $content    Post Content.
	 * @return  string              Post Content with Form Appended, if applicable
	 */
	public function append_form_to_content( $content ) {

		// Bail if not a singular Post Type.
		if ( ! is_singular() ) {
			return $content;
		}

		// Get Post ID and ConvertKit Form ID for the Post.
		$post_id = get_the_ID();
		$form_id = $this->get_post_form_id( $post_id );

		/**
		 * Define the ConvertKit Form ID to display for the given Post ID,
		 * overriding the Post, Category or Plugin settings.
		 *
		 * Return false to not display any ConvertKit Form.
		 *
		 * @since   1.9.6
		 *
		 * @param   int     $form_id    Form ID
		 * @param   int     $post_id    Post ID
		 */
		$form_id = apply_filters( 'convertkit_output_append_form_to_content_form_id', $form_id, $post_id );

		// Return the Post Content, unedited, if no Form ID exists.
		if ( ! $form_id ) {
			return $content;
		}

		// Get available ConvertKit Forms, if they have not yet been loaded.
		if ( ! $this->forms ) {
			$this->forms = new ConvertKit_Resource_Forms();
		}

		// Get Form HTML.
		$form = $this->forms->get_html( $form_id );

		// Return the Post Content, unedited, if an error occured.
		if ( is_wp_error( $form ) ) {
			return $content;
		}

		// Append form to Post's Content.
		$content = $content .= $form;

		/**
		 * Filter the Post's Content, which includes a ConvertKit Form, immediately before it is output.
		 *
		 * @since   1.9.6
		 *
		 * @param   string  $content    Post Content
		 * @param   string  $form       ConvertKit Form HTML
		 * @param   int     $post_id    Post ID
		 * @param   int     $form_id    ConvertKit Form ID
		 */
		$content = apply_filters( 'convertkit_frontend_append_form', $content, $form, $post_id, $form_id );

		return $content;

	}

	/**
	 * Returns the Post, Category or Plugin ConvertKit Form ID for the given Post.
	 *
	 * If the Post specifies a form to use, returns that Form ID.
	 * If the Post uses the 'Default' setting, and an assigned Category has a Form ID, uses the Category's Form ID.
	 * Otherwise falls back to the Plugin's Default Form ID (if any).
	 *
	 * @since   1.9.6
	 *
	 * @param   int $post_id    Post ID.
	 * @return  bool|string|int     false|'default'|Form ID
	 */
	private function get_post_form_id( $post_id ) {

		// Get Settings, if they have not yet been loaded.
		if ( ! $this->settings ) {
			$this->settings = new ConvertKit_Settings();
		}

		// Get ConvertKit Post's Settings, if they have not yet been loaded.
		if ( ! $this->post_settings ) {
			$this->post_settings = new ConvertKit_Post( $post_id );
		}

		// If the Post specifies a Form to use, return its ID now.
		if ( $this->post_settings->has_form() ) {
			return $this->post_settings->get_form();
		}

		// If the Post specifies that no Form should be used, return false.
		if ( $this->post_settings->uses_no_form() ) {
			return false;
		}

		// Sanity check that the Post uses the Default Form setting, which should be the case
		// because the above conditions were not met.
		if ( ! $this->post_settings->uses_default_form() ) {
			return false;
		}

		// Get Post's Categories.
		$categories = wp_get_post_categories(
			$post_id,
			array(
				'fields' => 'ids',
			)
		);

		// If no Categories exist, use the Default Form.
		if ( ! is_array( $categories ) || ! count( $categories ) ) {
			// Get Post Type.
			return $this->settings->get_default_form( get_post_type( $post_id ) );
		}

		/**
		 * Iterate through Categories in reverse order.
		 * This honors the behaviour < 1.9.6, which states that if multiple Categories each have a Form.
		 * assigned, the last Category with a Form in the wp_get_post_categories() call will be used.
		 */
		$categories = array_reverse( $categories );
		foreach ( $categories as $term_id ) {
			// Load Term Settings.
			$term_settings = new ConvertKit_Term( $term_id );

			// If a Form ID exists, return it now.
			if ( $term_settings->has_form() ) {
				return $term_settings->get_form();
			}
		}

		// If here, use the Plugin's Default Form.
		return $this->settings->get_default_form( get_post_type( $post_id ) );

	}

	/**
	 * Enqueue scripts.
	 *
	 * @since   1.9.6
	 */
	public function enqueue_scripts() {

		// Get Post.
		$post = get_post();

		// Bail if no Post could be fetched.
		if ( ! $post ) {
			return;
		}

		// Get ConvertKit Settings and Post's Settings.
		$settings        = new ConvertKit_Settings();
		$convertkit_post = new ConvertKit_Post( $post->ID );

		// Register scripts that we might use.
		wp_register_script(
			'convertkit-js',
			CONVERTKIT_PLUGIN_URL . 'resources/frontend/js/convertkit.js',
			array( 'jquery' ),
			CONVERTKIT_PLUGIN_VERSION,
			true
		);
		wp_localize_script(
			'convertkit-js',
			'convertkit',
			array(
				'ajaxurl'       => admin_url( 'admin-ajax.php' ),
				'debug'         => $settings->debug_enabled(),
				'nonce'         => wp_create_nonce( 'convertkit' ),
				'subscriber_id' => $this->get_subscriber_id_from_request(),
				'tag'           => ( ( is_singular() && $convertkit_post->has_tag() ) ? $convertkit_post->get_tag() : false ),
				'post_id'       => $post->ID,
			)
		);

		// Bail if the no scripts setting is enabled.
		if ( $settings->scripts_disabled() ) {
			return;
		}

		// Enqueue.
		wp_enqueue_script( 'convertkit-js' );

	}

	/**
	 * Gets the subscriber ID from the request (either the cookie or the URL).
	 *
	 * @since   1.9.6
	 *
	 * @return  int   Subscriber ID
	 */
	public function get_subscriber_id_from_request() {

		// If the subscriber ID is included in the URL as a query parameter
		// (i.e. 'Add subscriber_id parameter in email links' is enabled at https://app.convertkit.com/account_settings/advanced_settings,
		// return it as the subscriber ID.
		if ( isset( $_GET['ck_subscriber_id'] ) ) { // phpcs:ignore
			return absint( $_GET['ck_subscriber_id'] ); // phpcs:ignore
		}

		// If the subscriber ID is stored as a cookie (i.e. the user subscribed via a form
		// from this Plugin on this site, which sets this cookie), return it as the subscriber ID.
		if ( isset( $_COOKIE['ck_subscriber_id'] ) ) {
			return absint( $_COOKIE['ck_subscriber_id'] );
		}

		return 0;

	}

}
