<?php
/**
 * ConvertKit Admin Refresh Resources class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Registers scripts, which run when a Refresh button is clicked, to refresh resources
 * asynchronously whilst editing a Page, Post or Category.
 *
 * @package ConvertKit
 * @author ConvertKit
 */
class ConvertKit_Admin_Refresh_Resources {

	/**
	 * Registers action and filter hooks.
	 *
	 * @since   1.9.8.0
	 */
	public function __construct() {

		add_action( 'wp_ajax_convertkit_admin_refresh_resources', array( $this, 'refresh_resources' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

	}

	/**
	 * Refreshes resources (forms, landing pages or tags) from the API, returning them as a JSON string.
	 *
	 * @since   1.9.8.0
	 */
	public function refresh_resources() {

		// Check nonce.
		check_ajax_referer( 'convertkit_admin_refresh_resources', 'nonce' );

		// Get resource type.
		$resource = sanitize_text_field( $_REQUEST['resource'] );

		// Fetch resources.
		switch ( $resource ) {
			case 'forms':
				$forms   = new ConvertKit_Resource_Forms( 'user_refresh_resource' );
				$results = $forms->refresh();
				break;

			case 'landing_pages':
				$landing_pages = new ConvertKit_Resource_Landing_Pages( 'user_refresh_resource' );
				$results       = $landing_pages->refresh();
				break;

			case 'tags':
				$tags    = new ConvertKit_Resource_Tags( 'user_refresh_resource' );
				$results = $tags->refresh();
				break;

			case 'posts':
				$posts   = new ConvertKit_Resource_Posts( 'user_refresh_resource' );
				$results = $posts->refresh();
				break;

			case 'products':
				$products = new ConvertKit_Resource_Products();
				$results  = $products->refresh();
				break;

			default:
				$results = new WP_Error(
					'convertkit_admin_refresh_resources_error',
					sprintf(
						'Resource type %s is not supported in ConvertKit_Admin_Refresh_Resources class.',
						$resource
					)
				);
				break;
		}

		// Bail if an error occured.
		if ( is_wp_error( $results ) ) {
			wp_send_json_error( $results->get_error_message() );
		}

		// Return resources as a zero based sequential array, so that JS retains the order of resources.
		wp_send_json_success( array_values( $results ) );

	}

	/**
	 * Enqueue JavaScript when editing a Page, Post, Custom Post Type or Category.
	 *
	 * @since   1.9.8.0
	 *
	 * @param   string $hook   Hook.
	 */
	public function enqueue_scripts( $hook ) {

		// Bail if we are not on an Edit or Term screen.
		if ( ! in_array( $hook, array( 'edit.php', 'post-new.php', 'term.php', 'edit-tags.php', 'post.php' ), true ) ) {
			return;
		}

		// Get settings.
		$settings = new ConvertKit_Settings();

		// Bail if no API keys are defined.
		if ( ! $settings->has_api_key_and_secret() ) {
			return;
		}

		// Enqueue JS to perform AJAX request to refresh resources.
		wp_enqueue_script( 'convertkit-admin-refresh-resources', CONVERTKIT_PLUGIN_URL . 'resources/backend/js/refresh-resources.js', array( 'jquery' ), CONVERTKIT_PLUGIN_VERSION, true );
		wp_localize_script(
			'convertkit-admin-refresh-resources',
			'convertkit_admin_refresh_resources',
			array(
				'action'  => 'convertkit_admin_refresh_resources',
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'debug'   => $settings->debug_enabled(),
				'nonce'   => wp_create_nonce( 'convertkit_admin_refresh_resources' ),
			)
		);

	}

}
