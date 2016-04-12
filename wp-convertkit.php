<?php
/*
 Plugin Name: WP ConvertKit
 Plugin URI: http://convertkit.com/
 Description: Quickly and easily integrate ConvertKit forms into your site.
 Version: 1.4.0
 Author: ConvertKit
 Author URI: http://convertkit.com/
 */

require_once plugin_dir_path( __FILE__ ) . "/lib/convertkit-api.php";
require_once plugin_dir_path( __FILE__ ) . "/lib/integration/wishlist_member.php";

if(!class_exists('WP_ConvertKit')) {
	/**
	 * Class WP_ConvertKit
	 */
	class WP_ConvertKit {

		const VERSION = '1.4.0';

		const POST_META_KEY = '_wp_convertkit_post_meta';

		const SETTINGS_NAME = '_wp_convertkit_settings';

		const SETTINGS_PAGE_SLUG = '_wp_convertkit_settings';

		/** @var ConvertKitAPI */
		private static $api;

		/** @var int Data Caching */
		private static $cache_period = 0;

		/** @var null  */
		private static $meta_defaults = null;

		/** @var array  */
		private static $settings_defaults = array(
			'api_key'      => '',
			'default_form' => 0,
		);

		/** @var array  */
		private static $forms_markup = array();

		/** @var array  */
		private static $landing_pages_markup = array();

		/**
		 * Initialize the class
		 */
		public static function init() {
			self::add_actions();
			self::add_filters();
			self::register_shortcodes();

			self::$cache_period = MINUTE_IN_SECONDS * 10;

			self::_api_connect();
		}

		/**
		 * Add WP Actions
		 */
		private static function add_actions() {
			if(is_admin()) {
				add_action('add_meta_boxes_page', array(__CLASS__, 'add_meta_boxes'));
				add_action('add_meta_boxes_post', array(__CLASS__, 'add_meta_boxes'));
			} else {
				add_action('template_redirect', array(__CLASS__, 'page_takeover'));
			}

			add_action('save_post', array(__CLASS__, 'save_post_meta'), 10, 2);
		}

		/**
		 * Add WP Filters
		 */
		private static function add_filters() {
			if(!is_admin()) {
				add_filter('the_content', array(__CLASS__, 'append_form'));
			}

			add_filter('plugin_action_links_' . plugin_basename(__FILE__), array(__CLASS__, 'add_settings_page_link'));
		}

		/**
		 * Register ConvertKit shortcodes
		 */
		private static function register_shortcodes() {
			add_shortcode('convertkit', array(__CLASS__, 'shortcode'));
		}

		/**
		 * Plugin action links callback
		 *
		 * @param $links
		 * @return array
		 */
		public static function add_settings_page_link($links) {
			$settings_link = sprintf('<a href="%s">%s</a>', self::_get_settings_page_link(), __('Settings'));

			return array('settings' => $settings_link) + $links;
		}

		/**
		 * Add Meta Boxes callback
		 *
		 * @param $post
		 */
		public static function add_meta_boxes($post) {
			$forms = self::$api->get_resources('forms');
			$landing_pages = self::$api->get_resources('landing_pages');

			if(!empty($forms) || ('page' === $post->post_type && !empty($landing_pages))) {
				add_meta_box('wp-convertkit-meta-box', __('ConvertKit'), array(__CLASS__, 'display_meta_box'), $post->post_type, 'normal');
			}
		}

		/**
		 * Metabox callback
		 *
		 * @param $post
		 */
		public static function display_meta_box($post) {
			$forms = self::$api->get_resources('forms');
			$landing_pages = self::$api->get_resources('landing_pages');

			$meta = self::_get_meta($post->ID);
			$settings_link = self::_get_settings_page_link();

			include('views/backend/meta-boxes/meta-box.php');
		}

		/**
		 * Save post meta callback
		 *
		 * @param $post_id
		 * @param $post
		 */
		public static function save_post_meta($post_id, $post) {
			$data = stripslashes_deep($_POST);
			if(wp_is_post_autosave($post_id) || wp_is_post_revision($post_id) || !isset($data['wp-convertkit-save-meta-nonce']) || !wp_verify_nonce($data['wp-convertkit-save-meta-nonce'], 'wp-convertkit-save-meta')) {
				return;
			}

			self::_set_meta($post_id, $data['wp-convertkit']);
		}

		/**
		 * Page/Post display callback
		 *
		 * @param $content
		 * @return string
		 */
		public static function append_form($content) {
			if(is_singular(array('post')) || is_page()) {
				$content .= wp_convertkit_get_form_embed(self::_get_meta(get_the_ID()));
			}

			return $content;
		}

		/**
		 * Replace page content if a landing_page is set
		 */
		public static function page_takeover() {
			$queried_object = get_queried_object();
			if(isset($queried_object->post_type) && 'page' === $queried_object->post_type && ($landing_page_url = self::_get_meta($queried_object->ID, 'landing_page'))) {
				$landing_page = self::$api->get_resource($landing_page_url);

				if(!empty($landing_page)) {
					echo $landing_page;
					exit;
				}
			}

		}

		/**
		 * Shortcode callback
		 *
		 * @param $attributes
		 * @param null $content
		 * @return mixed|void
		 */
		public static function shortcode($attributes, $content = null) {
			return wp_convertkit_get_form_embed($attributes);
		}

		/**
		 * Retrieve meta
		 *
		 * @return array|null
		 */
		private static function _get_meta_defaults() {
			if(is_null(self::$meta_defaults)) {
				self::$meta_defaults = array(
					'form' => -1,
					'landing_page' => '',
				);
			}

			return self::$meta_defaults;
		}

		/**
		 * @param $post_id
		 * @param null $meta_key
		 *
		 * @return array|bool
		 */
		private static function _get_meta($post_id, $meta_key = null) {
			$post_id = empty($post_id) ? get_the_ID() : $post_id;

			$meta = get_post_meta($post_id, self::POST_META_KEY, true);
			$meta_defaults = self::_get_meta_defaults();

			if(empty($meta)) {
				$meta = $meta_defaults;

				$old_value = intval(get_post_meta($post_id, '_convertkit_convertkit_form', true));
				if(0 !== $old_value) {
					$meta['form'] = $old_value;
				}
			}

			$meta = shortcode_atts($meta_defaults, $meta);

			return is_null($meta_key) ? $meta : (isset($meta[$meta_key]) ? $meta[$meta_key] : false);
		}

		/**
		 * @param $post_id
		 * @param $meta
		 *
		 * @return mixed
		 */
		private static function _set_meta($post_id, $meta) {
			$post_id = empty($post_id) ? get_the_ID() : $post_id;

			update_post_meta($post_id, self::POST_META_KEY, $meta);

			return $meta;
		}

		/**
		 * Get plugin settings
		 *
		 * @param null $settings_key
		 * @return mixed|null|void
		 */
		private static function _get_settings($settings_key = null) {
			$settings = get_option(self::SETTINGS_NAME, self::$settings_defaults);

			return is_null($settings_key) ? $settings : (isset($settings[$settings_key]) ? $settings[$settings_key] : null);
		}

		/**
		 * Get instance of ConvertKitAPI
		 */
		private static function _api_connect() {
			$api_key = self::_get_settings('api_key');

			self::$api = new ConvertKitAPI($api_key);
		}

		/**
		 * Get link to the plugin settings page
		 *
		 * @param array $query_args
		 * @return string
		 */
		private static function _get_settings_page_link($query_args = array()) {
			$query_args = array('page' => self::SETTINGS_PAGE_SLUG) + $query_args;

			return add_query_arg($query_args, admin_url('options-general.php'));
		}

		/**
		 * Retrieve hosted form markup form the API and format
		 *
		 * @param $attributes
		 * @return string
		 */
		public static function get_form_embed($attributes) {
			$attributes = shortcode_atts(array(
				'form' => -1,
			), $attributes);

			$form = $attributes['form'];

			$form_id = intval(($form < 0) ? self::_get_settings('default_form') : $form);
			$form = false;

			if ($form_id == 0) {
				return "";
			}

			$forms_available = self::$api->get_resources('forms');
			foreach($forms_available as $form_available) {
				if($form_available['id'] == $form_id) {
					$form = $form_available;
					break;
				}
			}

			$form_markup = self::$api->get_resource($form['embed_url']);

			return $form_markup;
		}
	}

	require_once('lib/template-tags.php');
	WP_ConvertKit::init();
}

include 'admin/settings.php';
