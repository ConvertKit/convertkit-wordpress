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