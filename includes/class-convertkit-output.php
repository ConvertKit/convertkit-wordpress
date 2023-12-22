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
	 * Holds the ConvertKit Subscriber ID.
	 *
	 * @since   2.0.6
	 *
	 * @var     int|string
	 */
	private $subscriber_id = 0;

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

		add_action( 'init', array( $this, 'get_subscriber_id_from_request' ), 1 );
		add_action( 'template_redirect', array( $this, 'output_form' ) );
		add_action( 'template_redirect', array( $this, 'page_takeover' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_filter( 'the_content', array( $this, 'append_form_to_content' ) );
		add_action( 'wp_footer', array( $this, 'output_global_non_inline_form' ), 1 );
		add_action( 'wp_footer', array( $this, 'output_scripts_footer' ) );

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
		$landing_page_id = apply_filters( 'convertkit_output_page_takeover_landing_page_id', $landing_page_id, $post_id );

		// Bail if no Landing Page is configured to be output.
		if ( empty( $landing_page_id ) ) {
			return;
		}

		// Get available ConvertKit Landing Pages, if they have not yet been loaded.
		if ( ! $this->landing_pages ) {
			$this->landing_pages = new ConvertKit_Resource_Landing_Pages( 'output_landing_page' );
		}

		// Get Landing Page.
		$landing_page = $this->landing_pages->get_html( $this->post_settings->get_landing_page() );

		// Bail if an error occured.
		if ( is_wp_error( $landing_page ) ) {
			return;
		}

		// Replace the favicon with the WordPress site's favicon, if specified.
		$landing_page = $this->landing_pages->replace_favicon( $landing_page );

		// Output Landing Page.
		// Output is supplied from ConvertKit's API, which is already sanitized.
		echo $landing_page; // phpcs:ignore WordPress.Security.EscapeOutput
		exit;

	}

	/**
	 * Appends a form to the singular Page, Post or Custom Post Type's Content.
	 *
	 * @param   string $content    Post Content.
	 * @return  string              Post Content with Form Appended, if applicable
	 */
	public function append_form_to_content( $content ) {

		// Bail if not a singular Post Type supported by ConvertKit.
		if ( ! is_singular( convertkit_get_supported_post_types() ) ) {
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
		 * @param   bool|int    $form_id    Form ID
		 * @param   int         $post_id    Post ID
		 */
		$form_id = apply_filters( 'convertkit_output_append_form_to_content_form_id', $form_id, $post_id );

		// Return the Post Content, unedited, if the Form ID is false or zero.
		if ( ! $form_id ) {
			return $content;
		}

		// Get available ConvertKit Forms, if they have not yet been loaded.
		if ( ! $this->forms ) {
			$this->forms = new ConvertKit_Resource_Forms( 'output_form' );
		}

		// Get Form HTML.
		$form = $this->forms->get_html( $form_id );

		// If an error occured, it could be because the specified Form ID for the Post either:
		// - belongs to another ConvertKit account (i.e. API credentials were changed in the Plugin, but this Post's specified Form was not changed), or
		// - the form was deleted from the ConvertKit account.
		// Attempt to fallback to the default form for this Post Type.
		if ( is_wp_error( $form ) ) {
			if ( $this->settings->debug_enabled() ) {
				$content .= '<!-- ConvertKit append_form_to_content(): ' . $form->get_error_message() . ' Attempting fallback to Default Form. -->';
			}

			// Get Default Form ID for this Post's Type.
			$form_id = $this->settings->get_default_form( get_post_type( $post_id ) );

			// If no Default Form is specified, just return the Post Content, unedited.
			if ( ! $form_id ) {
				if ( $this->settings->debug_enabled() ) {
					$content .= '<!-- ConvertKit append_form_to_content(): No Default Form exists as a fallback. -->';
				}

				return $content;
			}

			// Get Form HTML.
			$form = $this->forms->get_html( $form_id );

			// If an error occured again, the default form doesn't exist in this ConvertKit account.
			// Just return the Post Content, unedited.
			if ( is_wp_error( $form ) ) {
				if ( $this->settings->debug_enabled() ) {
					$content .= '<!-- ConvertKit append_form_to_content(): Default Form: ' . $form->get_error_message() . ' -->';
				}

				return $content;
			}
		}

		// If here, we have a ConvertKit Form.
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
				'subscriber_id' => $this->subscriber_id,
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
	 */
	public function get_subscriber_id_from_request() {

		// Use ConvertKit_Subscriber class to fetch and validate the subscriber ID.
		$subscriber    = new ConvertKit_Subscriber();
		$subscriber_id = $subscriber->get_subscriber_id();

		// If an error occured, the subscriber ID in the request/cookie is not a valid subscriber.
		if ( is_wp_error( $subscriber_id ) ) {
			return;
		}

		$this->subscriber_id = $subscriber_id;

	}

	/**
	 * Outputs a non-inline form if defined in the Plugin's settings >
	 * Default Non-Inline Form (Global) setting.
	 *
	 * @since   2.3.3
	 */
	public function output_global_non_inline_form() {

		// Get Settings, if they have not yet been loaded.
		if ( ! $this->settings ) {
			$this->settings = new ConvertKit_Settings();
		}

		// Bail if no non-inline form setting is specified.
		if ( ! $this->settings->has_non_inline_form() ) {
			return;
		}

		// Get form.
		$convertkit_forms = new ConvertKit_Resource_Forms();
		$form             = $convertkit_forms->get_by_id( (int) $this->settings->get_non_inline_form() );

		// Bail if the Form doesn't exist (this shouldn't happen, but you never know).
		if ( ! $form ) {
			return;
		}

		// Add the form to the scripts array so it is included in the output.
		add_filter(
			'convertkit_output_scripts_footer',
			function ( $scripts ) use ( $form ) {

				$scripts[] = array(
					'async'    => true,
					'data-uid' => $form['uid'],
					'src'      => $form['embed_js'],
				);

				return $scripts;

			}
		);

	}

	/**
	 * Outputs any JS <script> tags registered with the convertkit_output_scripts_footer
	 * filter
	 *
	 * @since   2.1.4
	 */
	public function output_scripts_footer() {

		// Define array of scripts.
		$scripts = array();

		/**
		 * Define an array of scripts to output in the footer of the WordPress site.
		 *
		 * @since   2.1.4
		 *
		 * @param   array   $scripts    Scripts.
		 */
		$scripts = apply_filters( 'convertkit_output_scripts_footer', $scripts );

		// Bail if no scripts exist.
		if ( ! count( $scripts ) ) {
			return;
		}

		// Define array to store <script> outputs.
		$output_scripts = array();

		// Iterate through scripts, building the <script> tag for each.
		foreach ( $scripts as $script ) {
			$output = '<script';

			foreach ( $script as $attribute => $value ) {
				// If the value is true, just output the attribute.
				if ( $value === true ) {
					$output .= ' ' . esc_attr( $attribute );
					continue;
				}

				// Output the attribute and value.
				$output .= ' ' . esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';
			}
			$output .= '></script>';

			// Add to array.
			$output_scripts[] = $output;
		}

		// Remove duplicate scripts.
		// This prevents the same non-inline form displaying twice. For example, if a modal form is specified both
		// in the Page's settings and the Form block, the user would see the same modal form displayed twice
		// because the script would be output twice.
		$output_scripts = array_unique( $output_scripts );

		// Output scripts.
		foreach ( $output_scripts as $output_script ) {
			echo $output_script . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

	}

}
