<?php
/**
 * Plugin Name: ConvertKit
 * Plugin URI: https://convertkit.com/
 * Description: Quickly and easily integrate ConvertKit forms into your site.
 * Version: 1.4.8
 * Author: ConvertKit
 * Author URI: https://convertkit.com/
 * Text Domain: convertkit
 */

require_once plugin_dir_path( __FILE__ ) . '/lib/class-convertkit-api.php';
require_once plugin_dir_path( __FILE__ ) . '/lib/class-ck-widget-form.php';
require_once plugin_dir_path( __FILE__ ) . '/lib/integration/class-convertkit-wishlist-integration.php';
require_once plugin_dir_path( __FILE__ ) . '/lib/integration/class-convertkit-contactform7-integration.php';

if ( ! class_exists( 'WP_ConvertKit' ) ) {
	/**
	 * Class WP_ConvertKit
	 */
	class WP_ConvertKit {

		const VERSION = '1.4.8';

		const POST_META_KEY = '_wp_convertkit_post_meta';

		const SETTINGS_NAME = '_wp_convertkit_settings';

		const SETTINGS_PAGE_SLUG = '_wp_convertkit_settings';

		/**
		 * @var ConvertKit_API
		 */
		private static $api;

		/**
		 * @var int Data Caching
		 */
		private static $cache_period = 0;

		/**
		 * @var null
		 */
		private static $meta_defaults = null;

		/**
		 * @var array
		 */
		private static $settings_defaults = array(
			'api_key'      => '',
			'default_form' => 0,
		);

		/**
		 * @var array
		 */
		private static $forms_markup = array();

		/** @var array  */
		private static $landing_pages_markup = array();

		/** @var string  */
		private static $forms_version = '6';

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
			add_action( 'plugins_loaded', array( __CLASS__, 'load_textdomain' ) );
			if ( is_admin() ) {
				add_action( 'add_meta_boxes_page', array( __CLASS__, 'add_meta_boxes' ) );
				add_action( 'add_meta_boxes_post', array( __CLASS__, 'add_meta_boxes' ) );
			} else {
				add_action( 'template_redirect', array( __CLASS__, 'page_takeover' ) );
			}

			add_action( 'widgets_init', array( __CLASS__, 'ck_register_widgets' ) );

			add_action( 'save_post', array( __CLASS__, 'save_post_meta' ), 10, 2 );

			add_action( 'init', array( __CLASS__, 'upgrade' ) , 10 );
		}

		/**
		 * Load plugin textdomain
		 */
		public static function load_textdomain() {
			load_plugin_textdomain( 'convertkit', false, basename( dirname( __FILE__ ) ) . '/languages/' );
		}

		/**
		 * Add WP Filters
		 */
		private static function add_filters() {
			if ( ! is_admin() ) {
				add_filter( 'the_content', array( __CLASS__, 'append_form' ) );
			}

			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( __CLASS__, 'add_settings_page_link' ) );
		}

		/**
		 * Register ConvertKit shortcodes
		 */
		private static function register_shortcodes() {
			add_shortcode( 'convertkit', array( __CLASS__, 'shortcode' ) );
		}

		/**
		 * Plugin action links callback
		 *
		 * @param $links
		 * @return array
		 */
		public static function add_settings_page_link( $links ) {
			$settings_link = sprintf( '<a href="%s">%s</a>', self::_get_settings_page_link(), __( 'Settings', 'convertkit' ) );

			return array(
				'settings' => $settings_link,
				) + $links;
		}

		/**
		 * Add Meta Boxes callback
		 *
		 * @param WP_Post $post The current post.
		 */
		public static function add_meta_boxes( $post ) {
			$forms = self::$api->get_resources( 'forms' );
			$landing_pages = self::$api->get_resources( 'landing_pages' );

			if ( ! empty( $forms ) || ( 'page' === $post->post_type && ! empty( $landing_pages ) ) ) {
				add_meta_box( 'wp-convertkit-meta-box', __( 'ConvertKit', 'convertkit' ), array( __CLASS__, 'display_meta_box' ), $post->post_type, 'normal' );
			}
		}

		/**
		 * Metabox callback
		 *
		 * @param $post
		 */
		public static function display_meta_box( $post ) {
			$forms = self::$api->get_resources( 'forms' );
			$landing_pages = self::$api->get_resources( 'landing_pages' );

			$meta = self::_get_meta( $post->ID );
			$settings_link = self::_get_settings_page_link();

			include( 'views/backend/meta-boxes/meta-box.php' );
		}

		/**
		 * Save post meta callback
		 *
		 * @param int     $post_id Post id.
		 * @param WP_Post $post The post.
		 */
		public static function save_post_meta( $post_id, $post ) {
			if ( wp_is_post_autosave( $post_id )
				  || wp_is_post_revision( $post_id )
				  || ! isset( $_POST['wp-convertkit-save-meta-nonce'] ) // WPCS input var okay.
				  || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wp-convertkit-save-meta-nonce'] ) ), 'wp-convertkit-save-meta' ) ) { // WPCS input var okay.
				return;
			}
			if ( isset( $_POST['wp-convertkit'] ) ) { // WPCS input var okay.
				$form = '';
				if ( isset( $_POST['wp-convertkit']['form'] ) ) { // WPCS input var okay.
					$form = sanitize_text_field( wp_unslash( $_POST['wp-convertkit']['form'] ) ); // WPCS input var okay.
				}
				$landing_page = '';
				if ( isset( $_POST['wp-convertkit']['landing_page'] ) ) { // WPCS input var okay.
					$landing_page = sanitize_text_field( wp_unslash( $_POST['wp-convertkit']['landing_page'] ) ); // WPCS input var okay.
				}
				$meta = array(
					'form' => $form,
					'landing_page' => $landing_page,
				);
				update_post_meta( $post_id, self::POST_META_KEY, $meta );
			}
		}

		/**
		 * Page/Post display callback
		 *
		 * @param string $content The post content.
		 * @return string
		 */
		public static function append_form( $content ) {

			if ( is_singular( array( 'post' ) ) || is_page() ) {

				$attributes = self::_get_meta( get_the_ID() );

				$form_id = 0;

				if ( isset( $attributes['form'] ) && ( 0 < $attributes['form'] ) ) {
					$form_id = $attributes['form'];
				} else {
					if ( '-1' === $attributes['form'] ) {
						$form_id = self::_get_settings( 'default_form' );
					}
				}

				if ( 0 < $form_id ) {
					$url = add_query_arg( array(
							'api_key' => self::_get_settings( 'api_key' ),
							'v'       => self::$forms_version,
						),
						'https://forms.convertkit.com/' . $form_id . '.html'
					);

					$form_markup = self::$api->get_resource( $url );
					$content .= $form_markup;
				}
			}

			return $content;
		}

		/**
		 * Replace page content if a landing_page is set
		 */
		public static function page_takeover() {
			$queried_object = get_queried_object();
			if ( isset( $queried_object->post_type )
				&& 'page' === $queried_object->post_type ) {

				$landing_page_url = self::_get_meta( $queried_object->ID, 'landing_page' );

				$landing_page = self::$api->get_resource( $landing_page_url );

				if ( ! empty( $landing_page ) ) {
					echo $landing_page; // WPCS: XSS ok.
					exit;
				}
			}

		}

		/**
		 * Register widget.
		 */
		public static function ck_register_widgets() {
			register_widget( 'CK_Widget_Form' );
		}

		/**
		 * Shortcode callback
		 *
		 * @param array $attributes Shortcode attributes.
		 * @param null $content
		 * @return mixed|void
		 */
		public static function shortcode( $attributes, $content = null ) {

			if ( isset( $attributes['id'] ) ) {
				$form_id = $attributes['id'];
				$url = add_query_arg( array(
						'api_key' => self::_get_settings( 'api_key' ),
						'v'       => self::$forms_version,
					),
					'https://forms.convertkit.com/' . $form_id . '.html'
				);
			} elseif ( isset( $attributes['form'] ) ) {
				$form_id = $attributes['form'];
				$url = add_query_arg( array(
					'k' => self::_get_settings( 'api_key' ),
					'v' => '2',
					),
					'https://api.convertkit.com/forms/' . $form_id . '/embed'
				);
			} else {
				$form_id = self::_get_settings( 'default_form' );
				$url = add_query_arg( array(
						'api_key' => self::_get_settings( 'api_key' ),
						'v'       => self::$forms_version,
					),
					'https://forms.convertkit.com/' . $form_id . '.html'
				);
			}

			if ( 0 < $form_id ) {
				$form_markup = self::$api->get_resource( $url );
			} else {
				$form_markup = '';
			}

			return apply_filters( 'wp_convertkit_get_form_embed', $form_markup, $attributes );
		}

		/**
		 * Retrieve meta
		 *
		 * @return array|null
		 */
		private static function _get_meta_defaults() {
			if ( is_null( self::$meta_defaults ) ) {
				self::$meta_defaults = array(
					'form' => -1,
					'landing_page' => '',
				);
			}

			return self::$meta_defaults;
		}

		/**
		 * Get selected post meta
		 *
		 * @param int $post_id Post ID.
		 * @param null $meta_key Key string to get.
		 *
		 * @return array|bool
		 */
		private static function _get_meta( $post_id, $meta_key = null ) {
			$post_id = empty( $post_id ) ? get_the_ID() : $post_id;

			$meta = get_post_meta( $post_id, self::POST_META_KEY, true );
			$meta_defaults = self::_get_meta_defaults();

			if ( empty( $meta ) ) {
				$meta = $meta_defaults;

				$old_value = intval( get_post_meta( $post_id, '_convertkit_convertkit_form', true ) );
				if ( 0 !== $old_value ) {
					$meta['form'] = $old_value;
				}
			}

			$meta = shortcode_atts( $meta_defaults, $meta );

			return is_null( $meta_key ) ? $meta : ( isset( $meta[ $meta_key ] ) ? $meta[ $meta_key ] : false );
		}

		/**
		 * Get plugin settings
		 *
		 * @param null $settings_key
		 * @return mixed|null|void
		 */
		private static function _get_settings( $settings_key = null ) {
			$settings = get_option( self::SETTINGS_NAME, self::$settings_defaults );

			return is_null( $settings_key ) ? $settings : ( isset( $settings[ $settings_key ] ) ? $settings[ $settings_key ] : null);
		}

		/**
		 * Get instance of ConvertKitAPI
		 */
		private static function _api_connect() {
			$api_key    = self::_get_settings( 'api_key' );
			$api_secret = self::_get_settings( 'api_secret' );
			$debug      = self::_get_settings( 'debug' );

			self::$api = new ConvertKit_API( $api_key, $api_secret, $debug );
		}

		/**
		 * Get instance of the CK API
		 *
		 * @return ConvertKit_API
		 */
		public static function get_api() {
			return self::$api;
		}

		/**
		 * Get ConvertKit API key
		 *
		 * @return mixed|null|void
		 */
		public static function get_api_key() {
			return self::_get_settings( 'api_key' );
		}

		/**
		 * @return string
		 */
		public static function get_forms_version() {
			return self::$forms_version;
		}

		/**
		 * Get link to the plugin settings page
		 *
		 * @param array $query_args Args to add to URL.
		 * @return string
		 */
		private static function _get_settings_page_link( $query_args = array() ) {
			$query_args = array(
				'page' => self::SETTINGS_PAGE_SLUG,
			) + $query_args;

			return add_query_arg( $query_args, admin_url( 'options-general.php' ) );
		}

		/**
		 * Run version specific upgrade.
		 */
		public static function upgrade() {

			$current_version = get_option( 'convertkit_version' );

			if ( ! $current_version ) {
				// Run 1.4.1 upgrade.
				$settings = self::_get_settings();

				if ( isset( $settings['api_key'] ) ) {
					// Get all posts and pages to track what has been updated.
					$posts = get_option( '_wp_convertkit_upgrade_posts' );

					if ( ! $posts ) {

						$args = array(
							'post_type'      => array( 'post', 'page' ),
							'fields'         => 'ids',
						);

						$result = new WP_Query( $args );
						if ( ! is_wp_error( $result ) ) {
							$posts = $result->posts;
							update_option( '_wp_convertkit_upgrade_posts', $posts );
						}
					}

					// Get form mappings.
					$mappings = self::$api->get_resources( 'subscription_forms' );

					// 1. Update global form. Set 'api_version' so this is only done once.
					if ( ! isset( $settings['api_version'] ) ) {
						$old_form_id              = $settings['default_form'];
						$settings['default_form'] = isset( $mappings[ $old_form_id ] ) ? $mappings[ $old_form_id ] : 0;
						$settings['api_version']  = 'v3';
						update_option( self::SETTINGS_NAME, $settings );
					}

					// 2. Scan posts/pages for _wp_convertkit_post_meta and update IDs
					// Scan content for shortcode and update
					// Remove page_id from posts array after page is updated.
					foreach ( $posts as $key => $post_id ) {
						$post_settings = get_post_meta( $post_id, '_wp_convertkit_post_meta', true );

						if ( isset( $post_settings['form'] ) && ( 0 < $post_settings['form'] ) ) {
							$post_settings['form'] = isset( $mappings[ $post_settings['form'] ] ) ? $mappings[ $post_settings['form'] ] : 0;
						}
						if ( isset( $post_settings['landing_page'] ) && ( 0 < $post_settings['landing_page'] ) ) {
							$post_settings['landing_page'] = isset( $mappings[ $post_settings['landing_page'] ] ) ? $mappings[ $post_settings['landing_page'] ] : 0;
						}
						update_post_meta( $post_id, '_wp_convertkit_post_meta', $post_settings );
						unset( $posts[ $key ] );
						update_option( '_wp_convertkit_upgrade_posts', $posts );
					}

					// Done scanning posts, upgrade complete.
					if ( empty( $posts ) ) {
						update_option( 'convertkit_version', self::VERSION );
						delete_option( '_wp_convertkit_upgrade_posts' );
					}
				} else {
					update_option( 'convertkit_version', self::VERSION );
				} // End if().
			} // End if().
		}
	}

	WP_ConvertKit::init();
} // End if().

include 'admin/class-convertkit-settings.php';
