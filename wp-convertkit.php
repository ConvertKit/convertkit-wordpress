<?php
/*
 Plugin Name: WP ConvertKit
 Plugin URI: http://convertkit.com
 Description: Quickly and easily integrate ConvertKit forms into your site.
 Version: 1.0.0-RC1
 Author: Nick Ohrn of Plugin-Developer.com
 Author URI: http://plugin-developer.com/
 */

if(!class_exists('WP_ConvertKit')) {
	class WP_ConvertKit {
		/// CONSTANTS

		//// VERSION
		const VERSION = '1.0.0-RC1';

		//// KEYS
		const POST_META_KEY = '_wp_convertkit_post_meta';
		const SETTINGS_KEY = '_wp_convertkit_settings';

		//// SLUGS
		const SETTINGS_PAGE_SLUG = 'wp-convertkit-settings';

		/// DATA
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

				add_action('admin_menu', array(__CLASS__, 'add_administrative_interface_items'));
			}

			add_action('save_post', array(__CLASS__, 'save_post_meta'), 10, 2);
		}

		private static function add_filters() {
			if(is_admin()) {

			} else {
				add_filter('the_content', array(__CLASS__, 'append_form'));
			}

			add_filter('option_' . self::SETTINGS_KEY, array(__CLASS__, 'sanitize_settings'));
			add_filter('plugin_action_links_' . plugin_basename(__FILE__), array(__CLASS__, 'add_settings_link'));
		}

		private static function register_shortcodes() {
			add_shortcode('convertkit', array(__CLASS__, 'shortcode'));
		}

		/// CALLBACKS

		public static function add_administrative_interface_items() {
			$settings = add_options_page(__('ConvertKit Settings'), __('ConvertKit'), 'manage_options', self::SETTINGS_PAGE_SLUG, array(__CLASS__, 'display_settings_page'));

			add_action("load-{$settings}", array(__CLASS__, 'process_settings_save'));
		}

		public static function add_meta_boxes($post) {
			$api_key = self::_get_settings('api_key');

			if(!empty($api_key)) {
				add_meta_box('wp-convertkit-meta-box', __('ConvertKit'), array(__CLASS__, 'display_meta_box'), $post->post_type, 'normal');
			}
		}

		public static function add_settings_link($links) {
			$settings_link = sprintf('<a href="%s">%s</a>', add_query_arg(array('page' => self::SETTINGS_PAGE_SLUG), admin_url('options-general.php')), __('Settings'));

			return array('settings' => $settings_link) + $links;
		}

		public static function append_form($content) {
			if(is_singular(array('post')) || is_page()) {
				$content .= wp_convertkit_get_form_embed(self::_get_meta(get_the_ID()));
			}

			return $content;
		}

		public static function process_settings_save() {
			$data = stripslashes_deep($_POST);

			if(current_user_can('manage_options')
				&& isset($data['wp-convertkit-save-settings-nonce'])
				&& wp_verify_nonce($data['wp-convertkit-save-settings-nonce'], 'wp-convertkit-save-settings')) {

				self::_process_save_settings($data);
			}
		}

		public static function sanitize_settings($settings) {
			return shortcode_atts(self::_get_settings_defaults(), $settings);
		}

		public static function save_post_meta($post_id, $post) {
			$data = stripslashes_deep($_POST);
			if(wp_is_post_autosave($post_id)
				|| wp_is_post_revision($post_id)
				|| !isset($data['wp-convertkit-save-meta-nonce'])
				|| !wp_verify_nonce($data['wp-convertkit-save-meta-nonce'], 'wp-convertkit-save-meta')) {
				return;
			}

			self::_set_meta($post_id, $data['wp-convertkit']);
		}

		/// DISPLAY CALLBACKS

		public static function display_meta_box($post) {
			$forms = self::_get_forms(self::_get_settings('api_key'));
			$meta = self::_get_meta($post->ID);
			$settings_link = self::_get_settings_page_link();

			include('views/backend/meta-boxes/meta-box.php');
		}

		public static function display_settings_page() {
			$forms = self::_get_forms(self::_get_settings('api_key'));
			$settings = self::_get_settings();
			$settings_link = self::_get_settings_page_link();


			include('views/backend/settings/settings.php');
		}

		/// SHORTCODE CALLBACKS

		public static function shortcode($attributes, $content = null) {
			return wp_convertkit_get_form_embed($attributes);
		}

		/// POST META

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
			$meta = empty($meta) ? self::_get_meta_defaults() : $meta;

			return is_null($meta_key) ? $meta : (isset($meta[$meta_key]) ? $meta[$meta_key] : false);
		}

		private static function _set_meta($post_id, $meta) {
			$post_id = empty($post_id) ? get_the_ID() : $post_id;

			update_post_meta($post_id, self::POST_META_KEY, $meta);

			return $meta;
		}

		/// SETTINGS

		private static function _get_settings_defaults() {
			if(is_null(self::$settings_defaults)) {
				self::$settings_defaults = array(
					'api_key' => '',
					'default_form' => 0,
					'default_form_orientation' => 'horizontal'
				);
			}

			return self::$settings_defaults;
		}

		private static function _get_settings($settings_key = null) {
			$settings = get_option(self::SETTINGS_KEY, self::_get_settings_defaults());

			return is_null($settings_key) ? $settings : (isset($settings[$settings_key]) ? $settings[$settings_key] : null);
		}

		private static function _set_settings($settings) {
			update_option(self::SETTINGS_KEY, $settings);
		}

		/// API

		private static function _get_forms($api_key) {
			$forms = array();

			if(!empty($api_key)) {
				$request_url = add_query_arg(compact('api_key'), 'https://convertkit.com/app/api/v1/forms.json');
				$response = wp_remote_get($request_url, array('sslverify' => false));

				if(!is_wp_error($response)) {
					$decoded = json_decode(wp_remote_retrieve_body($response), true);
					if(is_array($decoded)) {
						foreach($decoded as $decoded_item) {
							$forms[] = $decoded_item['landing_page'];
						}
					}
				}
			}

			return $forms;
		}

		/// UTILITY

		private static function _redirect($url, $code = 302) {
			wp_redirect($url, $code); exit;
		}

		//// LINKS

		private static function _get_settings_page_link($query_args = array()) {
			$query_args = array('page' => self::SETTINGS_PAGE_SLUG) + $query_args;

			return add_query_arg($query_args, admin_url('options-general.php'));
		}

		//// PROCESSING

		private static function _process_save_settings($data) {
			self::_set_settings($data['wp-convertkit']);

			add_settings_error('general', 'settings_updated', __('Settings saved.'), 'updated');
			set_transient('settings_errors', get_settings_errors(), 30);

			self::_redirect(self::_get_settings_page_link(array('settings-updated' => 'true')));
		}

		/// TEMPLATE TAGS

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
	require_once('lib/utility.php');
	WP_ConvertKit::init();
}
