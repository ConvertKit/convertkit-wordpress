<?php
/**
 * ConvertKit general plugin functions.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Helper method to get supported Post Types.
 * 
 * @since 	1.9.6
 * 
 * @return 	array 	Post Types
 */
function convertkit_get_supported_post_types() {

	$post_types = array(
		'page',
		'post',
		'cpt', // @TODO Remove
	);

	/**
	 * Defines the Post Types that support ConvertKit Forms.
	 * 
	 * @since 	1.9.6
	 * 
	 * @param 	array 	$post_types 	Post Types
	 */
	$post_types = apply_filters( 'convertkit_get_supported_post_types', $post_types );

	return $post_types;

}

/**
 * Helper method to get registered Blocks / Shortcodes.
 * 
 * @since 	1.9.6
 * 
 * @return 	array 	Blocks
 */
function convertkit_get_blocks() {

	$blocks = array();

	/**
	 * Registers blocks / shortcodes for the ConvertKit Plugin.
	 * 
	 * @since 	1.9.6
	 * 
	 * @param 	array 	$blocks 	Blocks
	 */
	$blocks = apply_filters( 'convertkit_blocks', $blocks );

	return $blocks;

}

/**
 * Helper method to return the Plugin Settings Link
 * 
 * @since 	1.9.6
 * 
 * @param 	array 	$query_args 	Optional Query ARgs
 * @return 	string 					Settings Link
 */
function convertkit_get_settings_link( $query_args = array() ) {

	$query_args = array_merge(
		$query_args,
		array(
			'page' => '_wp_convertkit_settings',
		)
	);

	return add_query_arg( $query_args, admin_url( 'options-general.php' ) );

}

/**
 * Helper: Is ConvertKit WP's debug option enabled?
 *
 * @return bool
 */
function convertkit_wp_debug_enabled() {

	$options = get_option( WP_ConvertKit::SETTINGS_PAGE_SLUG );

	return ! empty( $options['debug'] ) && true == $options['debug']; // phpcs:ignore -- Okay use of loose comparison.

}

/**
 * Gets a customized version of the WordPress default user agent; includes WP Version, PHP version, and ConvertKit plugin version.
 *
 * @return string
 */
function convertkit_wp_get_user_agent() {

	// Include an unmodified $wp_version.
	require ABSPATH . WPINC . '/version.php';

	return sprintf(
		'WordPress/%1$s;PHP/%2$s;ConvertKit/%3$s;%4$s',
		$wp_version,
		phpversion(),
		CONVERTKIT_PLUGIN_VERSION,
		home_url( '/' )
	);
	
}

/**
 * Improved version of WordPress' is_admin(), which includes whether we're
 * editing on the frontend using a Page Builder.
 *
 * @since   1.9.6
 *
 * @return  bool    Is Admin or Frontend Editor Request
 */
function convertkit_is_admin_or_frontend_editor() {

	// If we're in the wp-admin, return true.
	if ( is_admin() ) {
		return true;
	}

	// Pro.
	if ( isset( $_SERVER ) ) {
		if ( strpos( sanitize_text_field( $_SERVER['REQUEST_URI'] ), '/pro/' ) !== false ) {
			return true;
		}
		if ( strpos( sanitize_text_field( $_SERVER['REQUEST_URI'] ), '/x/' ) !== false ) {
			return true;
		}
		if ( strpos( sanitize_text_field( $_SERVER['REQUEST_URI'] ), 'cornerstone-endpoint' ) !== false ) {
			return true;
		}
	}

	// If the request global exists, check for specific request keys which tell us that we're using a frontend editor.
	if ( isset( $_REQUEST ) && ! empty( $_REQUEST ) ) {
		// Avada Live.
		if ( array_key_exists( 'fb-edit', $_REQUEST ) ) {
			return true;
		}

		// Beaver Builder.
		if ( array_key_exists( 'fl_builder', $_REQUEST ) ) {
			return true;
		}

		// Brizy.
		if ( array_key_exists( 'brizy-edit', $_REQUEST ) ) {
			return true;
		}

		// Cornerstone (AJAX).
		if ( array_key_exists( '_cs_nonce', $_REQUEST ) ) {
			return true;
		}

		// Divi.
		if ( array_key_exists( 'et_fb', $_REQUEST ) ) {
			return true;
		}

		// Elementor.
		if ( array_key_exists( 'action', $_REQUEST ) && sanitize_text_field( $_REQUEST['action'] ) == 'elementor' ) {
			return true;
		}

		// Kallyas.
		if ( array_key_exists( 'zn_pb_edit', $_REQUEST ) ) {
			return true;
		}

		// Oxygen.
		if ( array_key_exists( 'ct_builder', $_REQUEST ) ) {
			return true;
		}

		// Thrive Architect.
		if ( array_key_exists( 'tve', $_REQUEST ) ) {
			return true;
		}

		// Visual Composer.
		if ( array_key_exists( 'vcv-editable', $_REQUEST ) ) {
			return true;
		}

		// WPBakery Page Builder.
		if ( array_key_exists( 'vc_editable', $_REQUEST ) ) {
			return true;
		}

		// Zion Builder.
		if ( array_key_exists( 'action', $_REQUEST ) && sanitize_text_field( $_REQUEST['action'] ) == 'zion_builder_active' ) {
			return true;
		}
	}

	// Assume we're not in the Administration interface
	$is_admin_or_frontend_editor = false;

	/**
	 * Filters whether the current request is a WordPress Administration / Frontend Editor request or not.
	 *
	 * Page Builders can set this to true to allow Page Generator Pro to load its functionality.
	 *
	 * @since   1.9.6
	 *
	 * @param   bool    $is_admin_or_frontend_editor    Is WordPress Administration / Frontend Editor request.
	 * @param   array   $_REQUEST                       $_REQUEST data                
	 */
	$is_admin_or_frontend_editor = apply_filters( 'convertkit_is_admin_or_frontend_editor', $is_admin_or_frontend_editor, $_REQUEST );
   
	// Return filtered result .
	return $is_admin_or_frontend_editor;

}

/**
 * Detects if the request is through the WP-CLI.
 *
 * @since   1.9.6
 *
 * @return  bool    Is WP-CLI Request
 */
function convertkit_is_cli() {

	if ( ! defined( 'WP_CLI' ) ) {
		return false;
	}
	if ( ! WP_CLI ) {
		return false;
	}

	return true;

}

/**
 * Detects if the request is through the WP CRON.
 *
 * @since   1.9.6
 *
 * @return  bool    Is WP CRON Request
 */
function convertkit_is_cron() {

	if ( ! defined( 'DOING_CRON' ) ) {
		return false;
	}
	if ( ! DOING_CRON ) {
		return false;
	}

	return true;

}

/**
 * Detects if the request is a non-WordPress Administration request for the frontend web site.
 *
 * @since   1.9.6
 *
 * @return  bool    Is WP CRON Request
 */
function convertkit_is_frontend() {

	return ! convertkit_is_admin_or_frontend_editor();

}

/**
 * Detects if the ConvertKit Plugin API keys exist
 *
 * @since 	1.8.6
 *
 * @return 	bool 	API Keys Exist
 */
function convertkit_api_enabled() {

	// @TODO

}