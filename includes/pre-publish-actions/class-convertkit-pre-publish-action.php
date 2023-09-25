<?php
/**
 * ConvertKit Pre-publish Action class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * ConvertKit Pre-publish Action definition for Gutenberg and Shortcode.
 *
 * @package ConvertKit
 * @author  ConvertKit
 */
class ConvertKit_Pre_Publish_Action {

	/**
	 * Registers the action's meta key in WordPress.
	 * This is required for Gutenberg to save the 'convertkit_action_{$name}'
	 * meta key/value pair when a Post is published.
	 * 
	 * @since 	2.4.0
	 */
	public function register_meta_key() {

		// Register action as a meta key.
		register_meta(
			'post',
			'convertkit_action_' . $this->get_name(),
			array(
			    'show_in_rest'  => true,
			    'single' 		=> true,
			    'type' 			=> 'boolean'
			)
		);

		// Define as a meta key in the Plugin's post settings defaults array.
		add_filter( 'convertkit_post_get_default_settings', function( $defaults ) {

			$defaults[ $this->get_name() ] = '';
			return $defaults;

		} );

	}

	/**
	 * Registers this pre-publish action with the ConvertKit Plugin.
	 *
	 * @since   2.4.0
	 *
	 * @param   array $pre_publish_actions    Pre-publish actions to register.
	 * @return  array               		  Pre-publish actions to register.
	 */
	public function register( $pre_publish_actions ) {

		$pre_publish_actions[ $this->get_name() ] = array(
			'name'           => $this->get_name(),
			'label' 		 => $this->get_label(),
			'description'    => $this->get_description(),
		);

		return $pre_publish_actions;

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
		 * - a Gutenberg pre-publish action, with the name convertkit-action-{$name}.
		 */
		return '';

	}

	/**
	 * Returns this action's label.
	 *
	 * @since   2.4.0
	 *
	 * @return  string
	 */
	public function get_label() {

		return '';

	}

	/**
	 * Returns this action's description.
	 *
	 * @since   2.4.0
	 *
	 * @return  string
	 */
	public function get_description() {

		return '';

	}

}
