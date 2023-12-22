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

		// Bail if the Enable Export Actions setting is not enabled.
		$broadcasts_settings = new ConvertKit_Settings_Broadcasts();
		if ( ! $broadcasts_settings->enabled_export() ) {
			return;
		}

		// Register action to export Post to ConvertKit Broadcast when published.
		add_action( 'convertkit_pre_publish_action_run_' . $this->get_name(), array( $this, 'export_broadcast' ) );

		// Call parent class constructor.
		parent::__construct();

	}

	/**
	 * Exports the given WordPress Post to a draft ConvertKit Broadcast,
	 * if the user has enabled this pre-publish action in the Post's settings,
	 * and the WordPress Post's status transitions to published from either
	 * draft or scheduled.
	 *
	 * @since   2.4.0
	 *
	 * @param   WP_Post $post   Post.
	 */
	public function export_broadcast( $post ) {

		$broadcasts_exporter = WP_ConvertKit()->get_class( 'broadcasts_exporter' );
		$broadcasts_exporter->export_post_to_broadcast( $post->ID );

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
