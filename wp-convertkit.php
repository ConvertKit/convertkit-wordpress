<?php
/*
 Plugin Name: WP ConvertKit
 Plugin URI: http://convertkit.com
 Description: Quickly and easily integrate ConvertKit forms into your site.
 Version: 1.1.x
 Author: Nick Ohrn of Plugin-Developer.com
 Author URI: http://plugin-developer.com/
 */

if(!class_exists('WP_ConvertKit')) {
	class WP_ConvertKit {

		// Plugin Version
		const VERSION = '1.1.x';

		// DB Keys
		const POST_META_KEY = '_wp_convertkit_post_meta';
		const SETTINGS_NAME = '_wp_convertkit_settings';

		// Transient Keys
		const TRANSIENT_FORM = '_wpc_api_form';
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
			} else {
				add_action('template_redirect', array(__CLASS__, 'page_takeover'));
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
			$settings = self::_get_settings();

			$forms = self::_get_forms();
			$landing_pages = self::_get_landing_pages();

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
			$forms = self::_get_forms();
			$landing_pages = self::_get_landing_pages();

			if(!empty($forms) || ('page' === $post->post_type && !empty($landing_pages))) {
				add_meta_box('wp-convertkit-meta-box', __('ConvertKit'), array(__CLASS__, 'display_meta_box'), $post->post_type, 'normal');
			}
		}

		public static function display_meta_box($post) {
			$forms = self::_get_forms();
			$landing_pages = self::_get_landing_pages();

			$meta = self::_get_meta($post->ID);
			$settings_link = self::_get_settings_page_link();

			include('views/backend/meta-boxes/meta-box.php');
		}

		public static function save_post_meta($post_id, $post) {
			$data = stripslashes_deep($_POST);
			if(wp_is_post_autosave($post_id) || wp_is_post_revision($post_id) || !isset($data['wp-convertkit-save-meta-nonce']) || !wp_verify_nonce($data['wp-convertkit-save-meta-nonce'], 'wp-convertkit-save-meta')) {
				return;
			}

			self::_set_meta($post_id, $data['wp-convertkit']);
		}

		/// Page / Post Display

		public static function append_form($content) {
			if(is_singular(array('post')) || is_page()) {
				$content .= wp_convertkit_get_form_embed(self::_get_meta(get_the_ID()));
			}

			return $content;
		}

		public static function page_takeover() {
			$queried_object = get_queried_object();
			if(isset($queried_object->post_type) && 'page' === $queried_object->post_type && ($landing_page_url = self::_get_meta($queried_object->ID, 'landing_page'))) {
				$landing_page = self::_get_landing_page($landing_page_url);

				if(!empty($landing_page)) {
					echo $landing_page;
					exit;
				}
			}

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
					'landing_page' => '',
				);
			}

			return self::$meta_defaults;
		}

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

		private static function _get_form_transient_key($url) {
			return md5($url . self::TRANSIENT_FORM);
		}

		private static function _get_form($url) {
			if(empty($url)) {
				$form = '';
			} else {
				$transient = self::_get_form_transient_key($url);

				$form = get_transient($transient);

				if(false === $form) {
					$response = wp_remote_get($url);

					if(!is_wp_error($response)) {
						if(!function_exists('str_get_html')) {
							require_once('vendor/simple-html-dom/simple-html-dom.php');
						}

						if(!function_exists('url_to_absolute')) {
							require_once('vendor/url-to-absolute/url-to-absolute.php');
						}

						$url_parts = parse_url($url);

						$body = wp_remote_retrieve_body($response);
						$html = str_get_html($body);
						foreach($html->find('a, link') as $element) {
							if(isset($element->href)) {
								$element->href = url_to_absolute($url, $element->href);
							}
						}

						foreach($html->find('img, script') as $element) {
							if(isset($element->src)) {
								$element->src = url_to_absolute($url, $element->src);
							}
						}

						foreach($html->find('form') as $element) {
							if(isset($element->action)) {
								$element->action = url_to_absolute($url, $element->action);
							} else {
								$element->action = $url;
							}
						}

						$form = $html->save();

						if($form) {
							set_transient($transient, $form, MINUTE_IN_SECONDS * 1);
						}
					}
				}
			}

			return $form;
		}

		private static function _get_forms_transient_key($key) {
			return md5($key . self::TRANSIENT_FORMS);
		}

		private static function _get_forms($key = null) {
			$key = is_null($key) ? self::_get_settings('api_key') : $key;

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
						set_transient($transient, $forms, MINUTE_IN_SECONDS * 1);
					}
				}
			}

			return $forms;
		}

		private static function _get_landing_page_transient_key($url) {
			return md5($url . self::TRANSIENT_LANDING_PAGE);
		}

		private static function _get_landing_page($url) {
			if(empty($url)) {
				$landing_page = '';
			} else {
				$transient = self::_get_landing_page_transient_key($url);

				$landing_page = get_transient($transient);

				if(false === $landing_page) {
					error_log($url);
					$response = wp_remote_get($url);

					if(!is_wp_error($response)) {
						if(!function_exists('str_get_html')) {
							require_once('vendor/simple-html-dom/simple-html-dom.php');
						}

						if(!function_exists('url_to_absolute')) {
							require_once('vendor/url-to-absolute/url-to-absolute.php');
						}

						$url_parts = parse_url($url);

						$body = wp_remote_retrieve_body($response);
						$html = str_get_html($body);
						foreach($html->find('a, link') as $element) {
							if(isset($element->href)) {
								$element->href = url_to_absolute($url, $element->href);
							}
						}

						foreach($html->find('img, script') as $element) {
							if(isset($element->src)) {
								$element->src = url_to_absolute($url, $element->src);
							}
						}

						foreach($html->find('form') as $element) {
							if(isset($element->action)) {
								$element->action = url_to_absolute($url, $element->action);
							} else {
								$element->action = $url;
							}
						}

						$landing_page = $html->save();

						if($landing_page) {
							set_transient($transient, $landing_page, MINUTE_IN_SECONDS * 1);
						}
					}
				}
			}

			return $landing_page;
		}

		private static function _get_landing_pages_transient_key($key) {
			return md5($key . self::TRANSIENT_LANDING_PAGES);
		}

		private static function _get_landing_pages($key = null) {
			$key = is_null($key) ? self::_get_settings('api_key') : $key;

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
						set_transient($transient, $landing_pages, MINUTE_IN_SECONDS * 1);
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

			$form_id = intval(($form < 0) ? self::_get_settings('default_form') : $form);
			$form = false;

			$forms_available = self::_get_forms();
			foreach($forms_available as $form_available) {
				if($form_available['id'] == $form_id) {
					$form = $form_available;
					break;
				}
			}

			$form_markup = self::_get_form($form['embed']);
			$form_orientation = ('default' === $form_orientation) ? self::_get_settings('default_form_orientation') : $form_orientation;

			if('vertical' !== $form_orientation) {
				$form_markup = str_replace('vertical', 'horizontal', $form_markup);
			}

			$form_markup .= '<style type="text/css">.ck_embed_form { margin: 0 auto; }</style>';

			return $form_markup;
		}
	}

	require_once('lib/template-tags.php');
	WP_ConvertKit::init();
}
