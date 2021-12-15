<?php
/**
 * ConvertKit Admin User class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Displays ConvertKit Tags assigned to a WordPress User.
 *
 * @package ConvertKit
 * @author ConvertKit
 */
class ConvertKit_Admin_User {

	/**
	 * Registers action and filter hooks.
	 *
	 * @since   1.9.6
	 */
	public function __construct() {

		// Bail if WordPress debugging is disabled.
		if ( ! defined( 'WP_DEBUG' ) || WP_DEBUG ) {
			add_action( 'show_user_profile', array( $this, 'add_customer_meta_fields' ) );
			add_action( 'edit_user_profile', array( $this, 'add_customer_meta_fields' ) );
		}

	}

	/**
	 * Displays ConvertKit Tags assigned to the given WordPress User.
	 *
	 * @since   1.9.6
	 *
	 * @param   WP_User $user   WordPress User.
	 */
	public function add_customer_meta_fields( $user ) {

		$convertkit_user = new ConvertKit_User( $user->ID );

	}

}
