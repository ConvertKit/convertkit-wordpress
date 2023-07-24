<?php
/**
 * ConvertKit Broadcasts class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Class to publish WordPress Posts based on ConvertKit Broadcasts,
 * if the Broadcasts functionality is enabled in the Plugin's settings. 
 *
 * @since   2.2.8
 */
class ConvertKit_Broadcasts {

	/**
	 * Constructor. Registers actions and filters to output ConvertKit Forms and Landing Pages
	 * on the frontend web site.
	 *
	 * @since   2.2.8
	 */
	public function __construct() {

		// Create and update Broadcasts stored as Posts when the Broadcasts Resource is refreshed.
		add_action( 'convertkit_resource_refreshed_broadcasts', array( $this, 'refresh' ) );

		// Debug @TODO Remove.
		add_action( 'init', function() {
			$broadcasts = new ConvertKit_Resource_Broadcasts();
			$broadcasts->refresh();
		} );

	}

	/**
	 * When the list of broadcasts is refreshed by the resource class, iterate through
	 * each Broadcast, to check if a Post exists in WordPress for it.
	 * 
	 * If not, add the Post.
	 * 
	 * @since 	2.2.8
	 * 
	 * @param 	array 	$broadcasts 	Broadcasts.
	 */
	public function refresh( $broadcasts ) {

		// Get broadcasts settings class.
		$broadcasts_settings = new ConvertKit_Settings_Broadcasts();

		// Bail if Broadcasts to Posts are disabled.
		if ( ! $broadcasts_settings->enabled() ) {
			return;
		}

		// Bail if no Broadcasts exist.
		if ( ! count( $broadcasts ) ) {
			return;
		}

		// Get settings class.
		$settings = new ConvertKit_Settings();

		// Bail if the Plugin API keys have not been configured.
		if ( ! $settings->has_api_key_and_secret() ) {
			return;
		}

		// Initialize the API.
		$api = new ConvertKit_API( $settings->get_api_key(), $settings->get_api_secret(), $settings->debug_enabled() );

		// Check that we're using the ConvertKit WordPress Libraries 1.3.8 or higher.
		// If another ConvertKit Plugin is active and out of date, its libraries might
		// be loaded that don't have this method.
		if ( ! method_exists( $api, 'get_broadcast' ) ) {
			return;
		}

		// Iterate through each Broadcast.
		foreach ( $broadcasts as $broadcast_id => $broadcast ) {
			// If a WordPress Post exists for this Broadcast ID, we previously imported it - skip it.
			if ( $this->broadcast_exists_as_post( $broadcast_id ) ) {
				continue;
			}

			// Fetch Broadcast's content.
			// This is because the resource will only contain results from v3/broadcasts, which
			// contains the ID, created_at and subject only.
			// We need to query v3/broadcasts/{id} to fetch the full Broadcast information and content.
			$broadcast = $api->get_broadcast( $broadcast_id );

			// Skip if an error occured.
			if ( is_wp_error( $broadcast ) ) {
				continue;
			}

			// Create wp_insert_post() compatible array from Broadcast.
			$post_args = $this->build_post_args( $broadcast );

			// Create Post.
			$post_id = wp_insert_post( $post_args );

			// If the Broadcast has an image, save it to the Media Library and link it to the Post.
			// @TODO.

			// Publish the Post.
			// @TODO.
		}

		var_dump( $broadcasts );
		die();

	}

	/**
	 * Helper method to determine if the given ConvertKit Broadcast already exists
	 * as a WordPress Post.
	 * 
	 * @since 	2.2.7
	 * 
	 * @param 	int 	$broadcast_id 	ConvertKit Broadcast ID.
	 * @return 	bool 					Broadcast exists as a WordPress Post
	 */
	private function broadcast_exists_as_post( $broadcast_id ) {

		$posts = new WP_Query( array(
			'post_type' => 'post',
			'post_status' => 'any',
			'meta_query' => array(
				array(
					'key' 	=> '_convertkit_broadcast_id',
					'value' => $broadcast_id,
				),
			),
			'fields' => 'ids',
			'update_post_cache' => false,
		) );

		if ( ! $posts->post_count ) {
			return false;
		}

		return true;

	}

	private function parse_content( $broadcast_content ) {



		return $content;

	}

	/**
	 * Defines the wp_insert_post() compatible arguments for importing the given ConvertKit
	 * Broadcast to a new WordPress Post.
	 * 
	 * @since 	2.2.8
	 * 
	 * @param 	array 	$broadcast 	Broadcast.
	 * @return 	array 				wp_insert_post() compatible arguments.
	 */
	private function build_post_args( $broadcast ) {

		// Define array for the wp_insert_post() compatible arguments.
		$post_args = array(
			'post_type' 	=> 'post',
			'post_title' 	=> $broadcast['subject'],
			'post_excerpt' 	=> $broadcast['description'],
			'post_content' 	=> $this->parse_broadcast_content( $broadcast['content'] ),
		);

		/**
		 * Define the wp_insert_post() compatible arguments for importing a ConvertKit Broadcast
		 * to a new WordPress Post.
		 * 
		 * @since 	2.2.8
		 * 
		 * @param 	array 	$post_args 	Post arguments.
		 * @param 	array 	$broadcast 	Broadcast.
		 */
		$post_args = apply_filters( 'convertkit_broadcasts_build_post_args', $post_args, $broadcast );

		// Deliberate: force the Post Status = draft. This will be changed to scheduled or publish after
		// any image is imported to the Post's Featured Image. So many plugins wrongly publish a Post
		// and then import the Featured Image, which breaks a lot of social media sharing Plugins.
		$post_args['post_status'] = 'draft';

		// Deliberate: ensure the Broadcast ID is always defined.
		if ( ! is_array( $post_args['meta_input'] ) ) {
			$post_args['meta_input'] = array();
		}
		$post_args['meta_input']['_convertkit_broadcast_id'] = $broadcast['id'];

		return $post_args;

	}

}
