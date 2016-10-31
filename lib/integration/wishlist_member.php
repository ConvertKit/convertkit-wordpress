<?php

require_once plugin_dir_path( __FILE__ ) . "../../lib/convertkit-api.php";

if(!class_exists('ConvertKitWishlistIntegration')) {

	/**
	 * Class ConvertKitWishlistIntegration
	 */
	class ConvertKitWishlistIntegration {
		protected $api;
		protected $options;

		/**
		 * Constructor
		 */
		public function __construct() {
			$general_options = get_option('_wp_convertkit_settings');
			$this->options   = get_option('_wp_convertkit_integration_wishlistmember_settings');
			$api_key         = $general_options && array_key_exists("api_key", $general_options) ? $general_options['api_key'] : null;
			$api_secret      = $general_options && array_key_exists("api_secret", $general_options) ? $general_options['api_secret'] : null;
			$debug           = $general_options && array_key_exists("debug", $general_options) ? $general_options['debug'] : null;
			$this->api       = new ConvertKitAPI($api_key,$api_secret,$debug);

			add_action(
				'wishlistmember_add_user_levels',     // hook
				array($this, 'add_user_levels'),      // function to call
				null,                                 // priority (default is fine)
				2                                     // number of arguments passed
			);

			add_action(
				'wishlistmember_remove_user_levels',  // hook
				array($this, 'remove_user_levels'),   // function to call
				null,                                 // priority
				2                                     // number of arguments passed
			);
		}

		/**
		 * Callback function for wishlistmember_add_user_levels action
		 *
		 * @param string $member_id ID for member that has just had levels added
		 * @param array  $levels    Levels to which member was added
		 */
		public function add_user_levels($member_id, $levels) {
			$member = $this->get_member($member_id);

			foreach ($levels as $wlm_level_id) {
				if (!isset($this->options[$wlm_level_id . '_form'])) continue;

				$this->member_resource_subscribe(
					$member,
					$this->options[$wlm_level_id . '_form']
				);
			}
		}

		/**
	     * Note: Form level unsubscribe is not available in v3 of the API.
	     *
	     * Callback function for wishlistmember_remove_user_levels action
	     *
	     * @param  string $member_id ID for member that has just had levels removed
	     * @param  array  $levels    Levels from which member was removed
	     */
		public function remove_user_levels($member_id, $levels) {

	      $member = $this->get_member($member_id);

	      foreach ($levels as $wlm_level_id) {
	        if (
	          isset($this->options[$wlm_level_id . '_form'])
	          && isset($this->options[$wlm_level_id . '_unsubscribe'])
	          && $this->options[$wlm_level_id . '_unsubscribe'] == '1'
	        ) {
	          $this->member_resource_unsubscribe(
	            $member,
	            $this->options[$wlm_level_id . '_form']
	          );
	        }
	      }

	    }

	    /**
	     * Subscribes a member to a ConvertKit resource
	     *
	     * @param  array  $member  UserInfo from WishList Member
	     * @param  string $form_id ConvertKit form id
	     * @return object          Response object from API
	     */
		public function member_resource_subscribe($member, $form_id) {

			// Check for temp email
			if ( preg_match('/temp_[a-f0-9]{32}/', $member['user_email'] ) ) {
				$email = $member['wlm_origemail'];
			} else {
				$email = $member['user_email'];
			}

		    // Note Wishlist Member combines first and last name into 'display_name'
			return $this->api->form_subscribe(
				$form_id,
				array(
					'email' => $email,
					'name'  => $member['display_name'],
				)
			);
		}

	    /**
	     * Unsubscribes a member from a ConvertKit resource
	     *
	     * @param  array  $member  UserInfo from WishList Member
	     * @param  string $form_id ConvertKit form id
	     * @return object          Response object from API
	     */
	    public function member_resource_unsubscribe($member, $form_id) {
			return $this->api->form_unsubscribe(
				array(
					'email' => $member['user_email']
				)
			);
	    }

	    /**
	     * Gets a WLM member using the wlmapi functions
	     *
	     * @param  string $id The member id
	     * @return array      The member fields from the API request
	     */
	    public function get_member($id) {
			if (!function_exists('wlmapi_get_member')) return false;

			$wlm_get_member = wlmapi_get_member($id);

			if ($wlm_get_member['success'] == 0) return false;

			return $wlm_get_member['member'][0]['UserInfo'];
		}

	}

  $convertkit_wishlist_integration = new ConvertKitWishlistIntegration;
}
