<?php
/**
 * ConvertKit Admin Post class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Registers a metabox on Posts, Pages and public facing Custom Post Types
 * and saves its settings when the Post is saved in the WordPress Administration
 * interface.
 *
 * @package ConvertKit
 * @author ConvertKit
 */
class ConvertKit_Admin_Post {

	/**
	 * Registers action and filter hooks.
	 *
	 * @since   1.9.6
	 */
	public function __construct() {

		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_post_meta' ) );

	}

	/**
	 * Enqueue JavaScript when editing a Page, Post or Custom Post Type that outputs
	 * ConvertKit Plugin settings.
	 *
	 * @since   1.9.6.4
	 */
	public function enqueue_scripts() {

		// Enqueue Select2 JS.
		convertkit_select2_enqueue_scripts();

		/**
		 * Enqueue JavaScript when editing a Page, Post or Custom Post Type that outputs
		 * ConvertKit Plugin settings.
		 *
		 * @since   1.9.6.4
		 */
		do_action( 'convertkit_admin_post_enqueue_scripts' );

	}

	/**
	 * Enqueue CSS when editing a Page, Post or Custom Post Type that outputs
	 * ConvertKit Plugin settings.
	 *
	 * @since   1.9.6.4
	 */
	public function enqueue_styles() {

		// Enqueue Select2 CSS.
		convertkit_select2_enqueue_styles();

		/**
		 * Enqueue CSS for the Settings Screen at Settings > ConvertKit
		 *
		 * @since   1.9.6.4
		 */
		do_action( 'convertkit_admin_post_enqueue_styles' );

	}

	/**
	 * Adds a meta box for the given Post Type.
	 *
	 * @since   1.9.6
	 *
	 * @param   string $post_type  Post Type.
	 */
	public function add_meta_boxes( $post_type ) {

		// Don't register the meta box if this Post Type isn't supported.
		$supported_post_types = convertkit_get_supported_post_types();
		if ( ! in_array( $post_type, $supported_post_types, true ) ) {
			return;
		}

		// Register Meta Box.
		add_meta_box( 'wp-convertkit-meta-box', __( 'ConvertKit', 'convertkit' ), array( $this, 'display_meta_box' ), $post_type, 'normal' );

		// Enqueue JS and CSS.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );

	}

	/**
	 * Outputs the meta box.
	 *
	 * @since   1.9.6
	 *
	 * @param   WP_Post $post   The Post being edited.
	 */
	public function display_meta_box( $post ) {

		// Don't register the meta box if this Post is the blog archive page.
		if ( $post->ID === get_option( 'page_for_posts' ) ) {
			return;
		}

		// Show a warning if the API credentials haven't been set.
		$settings = new ConvertKit_Settings();
		if ( ! $settings->has_api_key_and_secret() ) {
			$post_type = get_post_type_object( $post->post_type );
			include CONVERTKIT_PLUGIN_PATH . '/views/backend/post/no-api-key.php';
			return;
		}

		// Fetch Post Settings, Forms, Landing Pages and Tags.
		$convertkit_post          = new ConvertKit_Post( $post->ID );
		$convertkit_forms         = new ConvertKit_Resource_Forms();
		$convertkit_landing_pages = new ConvertKit_Resource_Landing_Pages();
		$convertkit_tags          = new ConvertKit_Resource_Tags();

		// Get settings page link.
		$settings_link = convertkit_get_settings_link();

		// Load metabox view.
		include CONVERTKIT_PLUGIN_PATH . '/views/backend/post/meta-box.php';

	}

	/**
	 * Save Post Settings.
	 *
	 * @since   1.9.6
	 *
	 * @param   int $post_id    Post ID.
	 */
	public function save_post_meta( $post_id ) {

		// Bail if this is an autosave.
		if ( wp_is_post_autosave( $post_id ) ) {
			return;
		}

		// Bail if this is a post revision.
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		// Bail if no nonce field exists.
		if ( ! isset( $_POST['wp-convertkit-save-meta-nonce'] ) ) {
			return;
		}

		// Bail if the nonce verification fails.
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wp-convertkit-save-meta-nonce'] ) ), 'wp-convertkit-save-meta' ) ) {
			return;
		}

		// Bail if no ConvertKit settings were posted.
		if ( ! isset( $_POST['wp-convertkit'] ) ) {
			return;
		}

		// Build metadata.
		$meta = array(
			'form'         => ( isset( $_POST['wp-convertkit']['form'] ) ? sanitize_text_field( wp_unslash( $_POST['wp-convertkit']['form'] ) ) : '-1' ),
			'landing_page' => ( isset( $_POST['wp-convertkit']['landing_page'] ) ? sanitize_text_field( wp_unslash( $_POST['wp-convertkit']['landing_page'] ) ) : '' ),
			'tag'          => ( isset( $_POST['wp-convertkit']['tag'] ) ? sanitize_text_field( wp_unslash( $_POST['wp-convertkit']['tag'] ) ) : '' ),
		);

		// Save metadata.
		$convertkit_post = new ConvertKit_Post( $post_id );
		$convertkit_post->save( $meta );

		// If a Form or Landing Page was specified, request a review.
		// This can safely be called multiple times, as the review request
		// class will ensure once a review request is dismissed by the user,
		// it is never displayed again.
		if ( $meta['form'] || $meta['landing_page'] ) {
			WP_ConvertKit()->get_class( 'review_request' )->request_review();
		}

	}

}
