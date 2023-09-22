<?php
/**
 * ConvertKit Broadcast Export Pre-publish Action class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * ConvertKit Broadcast Export Pre-publish Action for Gutenberg and Shortcode.
 *
 * @package ConvertKit
 * @author  ConvertKit
 */
class ConvertKit_Pre_Publish_Action_Broadcast_Export extends ConvertKit_Pre_Publish_Action {

	/**
	 * Constructor
	 *
	 * @since   2.4.0
	 */
	public function __construct() {

		// Register meta key.
		add_action( 'init', array( $this, 'register_meta_key' ) );

		// Register this as a Gutenberg pre-publish action in the ConvertKit Plugin.
		add_filter( 'convertkit_get_pre_publish_actions', array( $this, 'register' ) );


	}

	/**
	 * Returns this action's programmatic name, excluding the convertkit- prefix.
	 *
	 * @since   2.4.0
	 *
	 * @return  string
	 */
	public function get_name() {

		/**
		 * This will register as:
		 * - a Classic Editor pre-publish action, displayed as a checkbox on a draft Post.
		 * - a Gutenberg pre-publish action, with the name convertkit-action-broadcast-export.
		 */
		return 'broadcast_export';

	}

	/**
	 * Returns this action's label.
	 *
	 * @since   2.4.0
	 *
	 * @return  string
	 */
	public function get_label() {

		return __( 'Create Broadcast', 'convertkit' );

	}

	/**
	 * Returns this action's description.
	 *
	 * @since   2.4.0
	 *
	 * @return  string
	 */
	public function get_description() {

		return __( 'If enabled, creates a draft ConvertKit Broadcast using this Post\'s title, content and excerpt.', 'convertkit' );

	}

}
