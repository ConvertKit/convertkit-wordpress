<?php

require_once plugin_dir_path( __FILE__ ) . "../../lib/convertkit-api.php";

if(!class_exists('ConvertKitWishlistIntegration')) {
  class ConvertKitWishlistIntegration {
    protected $api;
    protected $options;

    public function __construct() {
      $this->options = get_option('_wp_convertkit_integration_wishlistmember_settings');
      $this->api     = new ConvertKitAPI(get_option('_wp_convertkit_settings')['api_key']);

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
     * Callback function for wishlistmember_add_member_levels action
     *
     * @param string $member_id ID for member that has just had a level added
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
      return $this->api->form_subscribe(
        $form_id,
        array(
          'email' => $member['user_email'],
          'fname' => $member['display_name']
        )
      );
    }

    public function member_resource_unsubscribe($member, $form_id) {
      return $this->api->form_unsubscribe(
        $form_id,
        array(
          'email' => $member['user_email']
        )
      );
    }

    public function get_member($id) {
      if (!function_exists('wlmapi_get_member')) return false;

      $wlm_get_member = wlmapi_get_member($id);

      if ($wlm_get_member['success'] == 0) return false;

      return $wlm_get_member['member'][0]['UserInfo'];
    }

  }

  $convertkit_wishlist_integration = new ConvertKitWishlistIntegration;
}
