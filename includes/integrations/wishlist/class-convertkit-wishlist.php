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

		// When a user has levels added or registers, perform actions on ConvertKit.
		add_action( 'wishlistmember_add_user_levels', array( $this, 'manage_member' ), 10, 2 );

		// When a user has levels removed check, perform actions on ConvertKit.
		add_action( 'wishlistmember_remove_user_levels', array( $this, 'manage_member' ), 10, 2 );

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
	public function manage_member( $member_id, $levels ) {

		// Get WishList Member.
		$member = $this->get_member( $member_id );

		// Bail if no Member was returned.
		if ( ! $member ) {
			return;
		}

		// Bail if the API hasn't been configured.
		$settings = new ConvertKit_Settings();
		if ( ! $settings->has_access_and_refresh_token() ) {
			return;
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

		// Initialize Wishlist Settings class.
		$wlm_settings = new ConvertKit_Wishlist_Settings();

		// Iterate through the member's levels.
		foreach ( $levels as $wlm_level_id ) {
			// If no ConvertKit resource is mapped to this level, skip it.
			$resource_type_id = $wlm_settings->get_convertkit_subscribe_setting_by_wishlist_member_level_id( $wlm_level_id );
			if ( ! $resource_type_id ) {
				continue;
			}

			// If the resource setting is 'unsubscribe', just unsubscribe the member.
			if ( $resource_type_id === 'unsubscribe' ) {
				error_log( 'unsubscribe only' );
				error_log( print_r( $api->unsubscribe( $email ), true ) );
				continue;
			}

			// Subscribe.
			$subscriber = $api->create_subscriber( $email, $first_name );

			// If the resource setting is 'subscribe', don't assign to a resource.
			if ( $resource_type_id === 'subscribe' ) {
				error_log( 'subscribe only' );
				continue;
			}

			// Extract resource type and ID from the setting.
			list( $resource_type, $resource_id ) = explode( ':', $resource_type_id );

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
						$api->add_subscriber_to_legacy_form( $resource_id, $subscriber['subscriber']['id'] );
					}

					// Add subscriber to form.
					$api->add_subscriber_to_form( $resource_id, $subscriber['subscriber']['id'] );
					break;

				/**
				 * Sequence
				 */
				case 'sequence':
					// Add subscriber to sequence.
					$api->add_subscriber_to_sequence( $resource_id, $subscriber['subscriber']['id'] );
					break;

				/**
				 * Tag
				 */
				case 'tag':
					// Add subscriber to tag.
					$api->tag_subscriber( $resource_id, $subscriber['subscriber']['id'] );
					break;

			}
		}

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
