<?php
/*
 Plugin Name: WP ConvertKit
 Plugin URI: http://convertkit.com
 Description: Quickly and easily integrate ConvertKit forms into your site.
 Version: 1.0.0.RC.2
 Author: Nick Ohrn of Plugin-Developer.com
 Author URI: http://plugin-developer.com/
 */

if(!class_exists('WP_ConvertKit')) {
	class WP_ConvertKit {

		// Plugin Version
		const VERSION = '1.0.0.RC.2';

		// DB Keys
		const POST_META_KEY = '_wp_convertkit_post_meta';
		const SETTINGS_NAME = '_wp_convertkit_settings';

		// Transient Keys
		const TRANSIENT_FORMS = '_wpc_api_forms';
		const TRANSIENT_LANDING_PAGE = '_wpc_api_landing_page';
		const TRANSIENT_LANDING_PAGES = '_wpc_api_landing_pages';

		// Page Slugs
		const SETTINGS_PAGE_SLUG = 'wp-convertkit-settings';

		// Data Caching
		private static $meta_defaults = null;
		private static $settings_defaults = null;

		public static function init() {
			self::add_actions();
			self::add_filters();
			self::register_shortcodes();
		}

		private static function add_actions() {
			if(is_admin()) {
				add_action('add_meta_boxes_page', array(__CLASS__, 'add_meta_boxes'));
				add_action('add_meta_boxes_post', array(__CLASS__, 'add_meta_boxes'));

				add_action('admin_init', array(__CLASS__, 'register_settings'));
				add_action('admin_menu', array(__CLASS__, 'add_settings_page'));
			}

			add_action('save_post', array(__CLASS__, 'save_post_meta'), 10, 2);
		}

		private static function add_filters() {
			if(is_admin()) {

			} else {
				add_filter('the_content', array(__CLASS__, 'append_form'));
			}

			add_filter('option_' . self::SETTINGS_NAME, array(__CLASS__, 'sanitize_settings'));
			add_filter('plugin_action_links_' . plugin_basename(__FILE__), array(__CLASS__, 'add_settings_page_link'));
		}

		private static function register_shortcodes() {
			add_shortcode('convertkit', array(__CLASS__, 'shortcode'));
		}

		// Callbacks

		/// Settings Related

		public static function add_settings_page() {
			$settings = add_options_page(__('ConvertKit Settings'), __('ConvertKit'), 'manage_options', self::SETTINGS_PAGE_SLUG, array(__CLASS__, 'display_settings_page'));
		}

		public static function add_settings_page_link($links) {
			$settings_link = sprintf('<a href="%s">%s</a>', self::_get_settings_page_link(), __('Settings'));

			return array('settings' => $settings_link) + $links;
		}

		public static function display_settings_page() {
			$key = self::_get_settings('api_key');

			$forms = self::_get_forms($key);
			$landing_pages = self::_get_landing_pages($key);

			$settings = self::_get_settings();

			include('views/backend/settings/settings.php');
		}

		public static function register_settings() {
			register_setting(self::SETTINGS_NAME, self::SETTINGS_NAME, array(__CLASS__, 'sanitize_settings'));
		}

		public static function sanitize_settings($settings) {
			return shortcode_atts(self::_get_settings_defaults(), $settings);
		}

		private static function _settings_id($name) {
			return self::SETTINGS_NAME . '-' . $name;
		}

		private static function _settings_name($name) {
			return self::SETTINGS_NAME . '[' . $name . ']';
		}

		/// Page / Post Editing

		public static function add_meta_boxes($post) {
			/*$api_key = self::_get_settings('api_key');

			if(!empty($api_key)) {
				add_meta_box('wp-convertkit-meta-box', __('ConvertKit'), array(__CLASS__, 'display_meta_box'), $post->post_type, 'normal');
			}*/
		}

		public static function display_meta_box($post) {
			$forms = self::_get_forms(self::_get_settings('api_key'));
			$meta = self::_get_meta($post->ID);
			$settings_link = self::_get_settings_page_link();

			include('views/backend/meta-boxes/meta-box.php');
		}

		public static function save_post_meta($post_id, $post) {
			/*$data = stripslashes_deep($_POST);
			if(wp_is_post_autosave($post_id)
				|| wp_is_post_revision($post_id)
				|| !isset($data['wp-convertkit-save-meta-nonce'])
				|| !wp_verify_nonce($data['wp-convertkit-save-meta-nonce'], 'wp-convertkit-save-meta')) {
				return;
			}

			self::_set_meta($post_id, $data['wp-convertkit']);*/
		}

		/// Page / Post Display

		public static function append_form($content) {
			if(is_singular(array('post')) || is_page()) {
				$content .= wp_convertkit_get_form_embed(self::_get_meta(get_the_ID()));
			}

			return $content;
		}

		/// Shortcodes

		public static function shortcode($attributes, $content = null) {
			return wp_convertkit_get_form_embed($attributes);
		}

		// Data Retrieval

		/// Page / Post Meta Data

		private static function _get_meta_defaults() {
			if(is_null(self::$meta_defaults)) {
				self::$meta_defaults = array(
					'form' => -1,
					'form_orientation' => 'default',
				);
			}

			return self::$meta_defaults;
		}

		private static function _get_meta($post_id, $meta_key = null) {
			$post_id = empty($post_id) ? get_the_ID() : $post_id;

			$meta = get_post_meta($post_id, self::POST_META_KEY, true);

			if(empty($meta)) {
				$meta = self::_get_meta_defaults();

				$old_value = intval(get_post_meta($post_id, '_convertkit_convertkit_form', true));
				if(0 !== $old_value) {
					$meta['form'] = $old_value;
				}
			}

			return is_null($meta_key) ? $meta : (isset($meta[$meta_key]) ? $meta[$meta_key] : false);
		}

		private static function _set_meta($post_id, $meta) {
			$post_id = empty($post_id) ? get_the_ID() : $post_id;

			update_post_meta($post_id, self::POST_META_KEY, $meta);

			return $meta;
		}

		/// Settings

		private static function _get_settings_defaults() {
			if(is_null(self::$settings_defaults)) {
				self::$settings_defaults = array(
					'api_key' => '',
					'default_form' => 0,
					'default_form_orientation' => 'horizontal',
				);
			}

			return self::$settings_defaults;
		}

		private static function _get_settings($settings_key = null) {
			$settings = get_option(self::SETTINGS_NAME, self::_get_settings_defaults());

			return is_null($settings_key) ? $settings : (isset($settings[$settings_key]) ? $settings[$settings_key] : null);
		}

		/// API

		private static function _get_api_response($path = '', $key = '', $version = '2') {
			$args = array('k' => $key, 'v' => $version);
			$url = add_query_arg($args, path_join('https://api.convertkit.com/', $path));

			$response = wp_remote_get($url);

			if(is_wp_error($response)) {
				$data = $response;
			} else {
				$data = json_decode(wp_remote_retrieve_body($response), true);
			}

			return $data;
		}

		private static function _get_forms_transient_key($key) {
			return md5($key . self::TRANSIENT_FORMS);
		}

		private static function _get_forms($key) {
			if(empty($key)) {
				$forms = array();
			} else {
				$transient = self::_get_forms_transient_key($key);

				$forms = get_transient($transient);

				if(false === $forms) {
					$forms = self::_get_api_response('forms', $key);
					$forms = is_wp_error($forms) ? false : $forms;
					$forms = (isset($forms['error']) || isset($forms['error_message'])) ? false : $forms;

					if($forms) {
						set_transient($transient, $forms, MINUTE_IN_SECONDS * 15);
					}
				}
			}

			return $forms;
		}

		private static function _get_landing_page_transient_key($id) {
			return md5($id . self::TRANSIENT_LANDING_PAGES);
		}

		private static function _get_landing_page($id) {
			if(empty($key)) {
				$landing_page = array();
			} else {
				$transient = self::_get_landing_page_transient_key($id);

				$landing_page = get_transient($transient);

				if(false === $landing_page) {
					$landing_page = false;

					if($landing_page) {
						set_transient($transient, $landing_page, MINUTE_IN_SECONDS * 15);
					}
				}
			}

			return $landing_pages;
		}

		private static function _get_landing_pages_transient_key($key) {
			return md5($key . self::TRANSIENT_LANDING_PAGES);
		}

		private static function _get_landing_pages($key) {
			if(empty($key)) {
				$landing_pages = array();
			} else {
				$transient = self::_get_landing_pages_transient_key($key);

				$landing_pages = get_transient($transient);

				if(false === $landing_pages) {
					$landing_pages = self::_get_api_response('landing_pages', $key);
					$landing_pages = is_wp_error($landing_pages) ? false : $landing_pages;
					$landing_pages = isset($landing_pages['error']) || isset($landing_pages['error_message']) ? false : $landing_pages;

					if($landing_pages) {
						set_transient($transient, $landing_pages, MINUTE_IN_SECONDS * 15);
					}
				}
			}

			return $landing_pages;
		}

		// Links

		private static function _get_settings_page_link($query_args = array()) {
			$query_args = array('page' => self::SETTINGS_PAGE_SLUG) + $query_args;

			return add_query_arg($query_args, admin_url('options-general.php'));
		}

		// Template Tags

		public static function get_form_embed($attributes) {
			$attributes = shortcode_atts(array(
				'form' => -1,
				'form_orientation' => 'default',
			), $attributes);

			extract($attributes);

			$form = intval(($form < 0) ? self::_get_settings('default_form') : $form);

			$form_orientation = ('default' === $form_orientation) ? self::_get_settings('default_form_orientation') : $form_orientation;
			$form_orientation = 'vertical' === $form_orientation ? 'vert' : false;

			$embed = '';

			if($form > 0) {
				$url = add_query_arg(array('orient' => $form_orientation), sprintf('https://convertkit.com/app/landing_pages/%d.js', $form));

				$embed = sprintf('<script src="%s"></script>', $url);
			}

			return $embed;
		}
	}

	require_once('lib/template-tags.php');
	WP_ConvertKit::init();
}
