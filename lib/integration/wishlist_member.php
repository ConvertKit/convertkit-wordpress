<?php

require_once plugin_dir_path( __FILE__ ) . "../../lib/convertkit-api.php";

if(!class_exists('ConvertKitWishlistIntegration')) {
  class ConvertKitWishlistIntegration {
    protected $api;

    public function __construct() {
      $this->api = new ConvertKitAPI(get_option('_wp_convertkit_settings')['api_key']);

      add_action('wishlistmember_add_user_levels', array($this, 'subscribe_user_on_add_level'));
    }

    /**
     * Callback function for wishlistmember_add_user_levels action
     *
     * @param string $user_id ID for user that has just had a level added
     */
    public function subscribe_user_on_add_level($user_id) {
      if (!function_exists('wlmapi_get_member')) return;

      $option_key      = '_wp_convertkit_integration_wishlistmember_settings';
      $wlm_get_member  = wlmapi_get_member($user_id);
      $wlm_ck_settings = get_option($option_key);

      if ($wlm_get_member['success'] == 0 || empty($wlm_ck_settings)) return;

      $user        = $wlm_get_member['member'][0]['UserInfo'];
      $user_levels = $wlm_get_member['member'][0]['Levels'];

      foreach ($user_levels as $wlm_level) {
        if (isset($wlm_ck_settings[$wlm_level->Level_ID])) {
          $this->user_resource_subscribe($user, $wlm_ck_settings[$wlm_level->Level_ID]);
        }
      }
    }

    /**
     * Subscribes a user to a ConvertKit resource
     *
     * @param  array  $user         UserInfo from WishList Member
     * @param  string $resource_key Pipe-delimited resource type and id (landing_pages|3)
     * @return object               Response object from API
     */
    public function user_resource_subscribe($user, $resource_key) {
      list($resource_type, $resource_id) = explode('|', $resource_key);

      return $this->api->add_resource_subscriber($resource_type, $resource_id, array(
        'email' => $user['user_email'],
        'fname' => $user['display_name']
      ));
    }

  }

  $convertkit_wishlist_integration = new ConvertKitWishlistIntegration;
}
