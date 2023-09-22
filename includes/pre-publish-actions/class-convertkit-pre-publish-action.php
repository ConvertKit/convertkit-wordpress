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
