<?php
/**
 * ConvertKit Wishlist class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Wishlist Integration
 *
 * @package ConvertKit
 * @author ConvertKit
 */
class ConvertKit_Wishlist {

	/**
	 * Constructor. Registers required hooks with WishList Member.
	 *
	 * @since   1.9.6
	 */
	public function __construct() {

		// When a user has levels added or registers check for a mapping to a ConvertKit form.
		add_action( 'wishlistmember_add_user_levels', array( $this, 'add_user_levels' ), 10, 2 );

		// When a user has levels removed check for a mapping to a ConvertKit tag, or if the subscriber
		// should be removed from ConvertKit.
		add_action( 'wishlistmember_remove_user_levels', array( $this, 'remove_user_levels' ), 10, 2 );

	}

	/**
	 * When a user has levels added or registers, subscribe them to a ConvertKit Form
	 * if the given WishList Member Level is mapped to a ConvertKit Form.
	 *
	 * @since   1.9.6
	 *
	 * @param   string $member_id  ID for member that has just had levels added.
	 * @param   array  $levels     Levels to which member was added.
	 */
	public function add_user_levels( $member_id, $levels ) {

		// Get WishList Member.
		$member = $this->get_member( $member_id );

		// Bail if no Member was returned.
		if ( ! $member ) {
			return;
		}

		// Initialize Wishlist Settings class.
		$wlm_settings = new ConvertKit_Wishlist_Settings();

		// Iterate through the member's levels.
		foreach ( $levels as $wlm_level_id ) {
			// If no ConvertKit Form is mapped to this level, skip it.
			$convertkit_form_id = $wlm_settings->get_convertkit_form_id_by_wishlist_member_level_id( $wlm_level_id );
			if ( ! $convertkit_form_id ) {
				continue;
			}

			// Subscribe the user to the ConvertKit Form for this level.
			$this->member_resource_subscribe( $member, $convertkit_form_id );
		}

	}

	/**
	 * Note: Form level unsubscribe is not available in v3 of the API.
	 *
	 * Callback function for wishlistmember_remove_user_levels action
	 *
	 * @param  string $member_id ID for member that has just had levels removed.
	 * @param  array  $levels Levels from which member was removed.
	 */
	public function remove_user_levels( $member_id, $levels ) {

		// Get WishList Member.
		$member = $this->get_member( $member_id );

		// Bail if no Member was returned.
		if ( ! $member ) {
			return;
		}

		// Initialize Wishlist Settings class.
		$wlm_settings = new ConvertKit_Wishlist_Settings();

		// Iterate through the member's levels.
		foreach ( $levels as $wlm_level_id ) {
			// If no ConvertKit Tag is mapped to this level, skip it.
			$convertkit_tag_id = $wlm_settings->get_convertkit_tag_id_by_wishlist_member_level_id( $wlm_level_id );
			if ( ! $convertkit_tag_id ) {
				continue;
			}

			// If the Tag ID is 'unsubscribe', unsubscribe the member from tags.
			if ( $convertkit_tag_id === 'unsubscribe' ) {
				$this->member_resource_unsubscribe( $member );
				continue;
			}

			// Tag the member.
			$this->member_tag( $member, $convertkit_tag_id );
		}

	}

	/**
	 * Subscribes a member to a ConvertKit Form.
	 *
	 * @param   array $member  UserInfo from WishList Member.
	 * @param   int   $form_id ConvertKit Form ID.
	 * @return  bool|WP_Error|array
	 */
	public function member_resource_subscribe( $member, $form_id ) {

		// Bail if the API hasn't been configured.
		$settings = new ConvertKit_Settings();
		if ( ! $settings->has_api_key_and_secret() ) {
			return false;
		}

		// Initialize the API.
		$api = new ConvertKit_API( $settings->get_api_key(), $settings->get_api_secret(), $settings->debug_enabled(), 'wishlist_member' );

		// Check for temp email.
		if ( preg_match( '/temp_[a-f0-9]{32}/', $member['user_email'] ) ) {
			$email = $member['wlm_origemail'];
		} else {
			$email = $member['user_email'];
		}

		// Extract the first name.
		$first_name = '';
		if ( isset( $member['display_name'] ) && ! empty( $member['display_name'] ) ) {
			$name       = explode( ' ', $member['display_name'] );
			$first_name = $name[0];
		}

		// Note Wishlist Member combines first and last name into 'display_name'.
		return $api->form_subscribe(
			$form_id,
			$email,
			$first_name
		);
	}

	/**
	 * Unsubscribes a member from ConvertKit.
	 *
	 * @param   array $member  UserInfo from WishList Member.
	 * @return  bool|WP_Error|array
	 */
	public function member_resource_unsubscribe( $member ) {

		// Bail if the API hasn't been configured.
		$settings = new ConvertKit_Settings();
		if ( ! $settings->has_api_key_and_secret() ) {
			return false;
		}

		// Initialize the API.
		$api = new ConvertKit_API( $settings->get_api_key(), $settings->get_api_secret(), $settings->debug_enabled(), 'wishlist_member' );

		// Check for temp email.
		if ( preg_match( '/temp_[a-f0-9]{32}/', $member['user_email'] ) ) {
			$email = $member['wlm_origemail'];
		} else {
			$email = $member['user_email'];
		}

		// Unsubscribe the email.
		return $api->unsubscribe( $email );

	}

	/**
	 * Tag a ConvertKit User with the given Tag ID.
	 *
	 * @param   array $member  UserInfo from WishList Member.
	 * @param   int   $tag_id  ConvertKit Tag ID.
	 * @return  bool|WP_Error|array
	 */
	public function member_tag( $member, $tag_id ) {

		// Bail if the API hasn't been configured.
		$settings = new ConvertKit_Settings();
		if ( ! $settings->has_api_key_and_secret() ) {
			return false;
		}

		// Initialize the API.
		$api = new ConvertKit_API( $settings->get_api_key(), $settings->get_api_secret(), $settings->debug_enabled(), 'wishlist_member' );

		return $api->tag_subscribe( $tag_id, $member['user_email'] );

	}

	/**
	 * Gets a WLM member using the wlmapi functions
	 *
	 * @param  string $id The member id.
	 * @return bool|array
	 */
	public function get_member( $id ) {

		// Get WishList Member.
		$wlm_get_member = wlmapi_get_member( $id );

		// Bail if an error occured.
		if ( 0 === $wlm_get_member['success'] ) {
			return false;
		}

		// Return user's information.
		return $wlm_get_member['member'][0]['UserInfo'];

	}

}

// Bootstrap.
add_action(
	'convertkit_initialize_global',
	function () {

		new ConvertKit_Wishlist();

	}
);
