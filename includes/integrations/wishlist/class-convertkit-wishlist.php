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
			// If no ConvertKit resource is mapped to this level, skip it.
			$resource_type_id = $wlm_settings->get_convertkit_subscribe_setting_by_wishlist_member_level_id( $resource_type_and_id );
			if ( ! $resource_type_and_id ) {
				continue;
			}

			// If the resource setting is 'subscribe', just subscribe the member.
			if ( $resource_type_id === 'subscribe' ) {
				$this->member_resource_subscribe( $member );
				continue;
			}

			// Extract resource type and ID from the setting.
			list( $resource_type, $resource_id ) = explode( ':', $resource_type_and_id  );

			// Subscribe the member to ConvertKit, and assign them to the resource (Form, Tag, Sequence).
			$this->member_resource_subscribe( $member, $resource_id, $resource_type );
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
			// If no ConvertKit resource is mapped to this level, skip it.
			$resource_type_id = $wlm_settings->get_convertkit_unsubscribe_setting_by_wishlist_member_level_id( $wlm_level_id );
			if ( ! $resource_type_id ) {
				continue;
			}

			// If the resource setting is 'unsubscribe', unsubscribe the member.
			if ( $resource_type_id === 'unsubscribe' ) {
				$this->member_resource_unsubscribe( $member );
				continue;
			}

			// Extract resource type and ID from the setting.
			list( $resource_type, $resource_id ) = explode( ':', $resource_type_and_id  );

			// Subscribe the member to ConvertKit, and assign them to the resource (Form, Tag, Sequence).
			$this->member_resource_subscribe( $member, $resource_id, $resource_type );
		}

	}

	/**
	 * Subscribes a member to ConvertKit, and optionally assigns them to the given resource
	 * (Form, Tag or Sequence).
	 *
	 * @param   array $member  UserInfo from WishList Member.
	 * @param   int   $form_id ConvertKit Form ID.
	 * @return  bool|WP_Error|array
	 */
	public function member_resource_subscribe( $member, $resource_id = false, $resource_type = false ) {

		// Bail if the API hasn't been configured.
		$settings = new ConvertKit_Settings();
		if ( ! $settings->has_access_and_refresh_token() ) {
			return false;
		}

		// Initialize the API.
		$api = new ConvertKit_API_V4(
			CONVERTKIT_OAUTH_CLIENT_ID,
			CONVERTKIT_OAUTH_CLIENT_REDIRECT_URI,
			$settings->get_access_token(),
			$settings->get_refresh_token(),
			$settings->debug_enabled(),
			'wishlist_member'
		);

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

		// Subscribe the email address.
		$subscriber = $api->create_subscriber( $email, $first_name );
		if ( is_wp_error( $subscriber ) ) {
			return;
		}

		// If no resource type or ID, no need to do anything else.
		if ( ! $resource_type || ! $resource_id ) {
			return;
		}

		// Cast ID.
		$resource_id = absint( $resource_id );

		// Add the subscriber to the resource type (form, tag etc).
		switch ( $resource_type ) {

			/**
			 * Form
			 */
			case 'form':
				// For Legacy Forms, a different endpoint is used.
				$forms = new ConvertKit_Resource_Forms();
				if ( $forms->is_legacy( $resource_id ) ) {
					return $api->add_subscriber_to_legacy_form( $resource_id, $subscriber['subscriber']['id'] );
				}

				// Add subscriber to form.
				return $api->add_subscriber_to_form( $resource_id, $subscriber['subscriber']['id'] );

			/**
			 * Sequence
			 */
			case 'sequence':
				// Add subscriber to sequence.
				return $api->add_subscriber_to_sequence( $resource_id, $subscriber['subscriber']['id'] );

			/**
			 * Tag
			 */
			case 'tag':
				// Add subscriber to tag.
				return $api->tag_subscriber( $resource_id, $subscriber['subscriber']['id'] );

		}

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
		if ( ! $settings->has_access_and_refresh_token() ) {
			return false;
		}

		// Initialize the API.
		$api = new ConvertKit_API_V4(
			CONVERTKIT_OAUTH_CLIENT_ID,
			CONVERTKIT_OAUTH_CLIENT_REDIRECT_URI,
			$settings->get_access_token(),
			$settings->get_refresh_token(),
			$settings->debug_enabled(),
			'wishlist_member'
		);

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
