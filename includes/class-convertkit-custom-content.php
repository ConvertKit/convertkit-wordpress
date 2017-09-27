<?php
/**
 * Class ConvertKit_Custom_Content
 *
 * @since 1.5.0
 */

class ConvertKit_Custom_Content {


	/**
	 * @var mixed|void
	 */
	protected $options;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->options   = get_option( '_wp_convertkit_integration_custom_content_settings' );

		if ( isset( $this->options['enable'] ) && 'on' === $this->options['enable'] ) {
			$this->add_actions();
			$this->register_shortcodes();
		}
	}

	/**
	 *
	 */
	public function add_actions() {
		add_action( 'wp_login', array( $this, 'login_action' ), 20, 2 );
		add_action( 'the_post', array( $this, 'maybe_tag_subscriber' ), 50 );

	}

	/**
	 *
	 */
	public function register_shortcodes() {
		add_shortcode( 'convertkit_content', array( $this, 'shortcode' ) );
	}

	/**
	 * Shortcode callback
	 *
	 * @param array $attributes Shortcode attributes.
	 * @param string $content
	 * @return mixed|void
	 */
	public static function shortcode( $attributes, $content ) {
		error_log( "\n\nshortcode callback\n" );
		error_log( "ATTRIBUTES: " . print_r( $attributes, true ) );

		// we only care about the 'tag' attribute.
		if ( isset( $attributes['tag'] ) ) {
			$tags = array();
			$tag = $attributes['tag'];
			$user_id = get_current_user_id();
			$api = WP_ConvertKit::get_api();

			//if ( $user_id ) {
			// user is logged in so get tags from user meta
			//error_log( "shortcode: user logged in, get tags from db" );
			//$tags = get_user_meta( $user_id, 'convertkit_tags', true );
			//$tags = ! empty( $tags ) ? json_decode( $tags, true ) : array();
			//} else
			if ( isset( $_COOKIE['ck_subscriber_id'] ) ) {
				error_log( "shortcode: cookie found, calling API" );
				// get cookie and check API for customer tags.
				$subscriber_id = absint( $_COOKIE['ck_subscriber_id'] );
				if ( $subscriber_id ) {
					$tags = $api->get_subscriber_tags( $subscriber_id );
				}
			} elseif ( isset( $_COOKIE['ck_subscriber_id'] ) ) {
				error_log( "shortcode: cookie param found, calling API" );
				// get cookie and check API for customer tags.
				$subscriber_id = absint( $_GET['ck_subscriber_id'] );
				if ( $subscriber_id ) {
					$tags = $api->get_subscriber_tags( $subscriber_id );
				}
			} elseif ( isset( $_GET['ck_subscriber_id'] ) ) {
				error_log( "shortcode: URL param found, calling API" );
				// get cookie and check API for customer tags.
				$subscriber_id = absint( $_GET['ck_subscriber_id'] );
				if ( $subscriber_id ) {
					$tags = $api->get_subscriber_tags( $subscriber_id );
				}
			}

			if ( isset( $tags[ $tag ] ) ) {
				return apply_filters( 'wp_convertkit_shortcode_custom_content', $content, $attributes );
			}

		}

		return null;

	}

	/**
	 * During the login event check if the user is a subscriber and setup
	 * the subscriber_id and subscriber's tags to be used by customization.
	 *
	 * @param string $user_login
	 * @param WP_User $user
	 */
	public function login_action( $user_login, $user ) {

		$user_email = $user->user_email;
		$api = WP_ConvertKit::get_api();

		// Get subscriber id from email and cookie
		$subscriber_id = $api->get_subscriber_id( $user_email );
		if ( $subscriber_id ) {
			update_user_meta( $user->ID, 'convertkit_subscriber_id', $subscriber_id );
			setcookie( 'convertkit_subscriber', $subscriber_id, time() + ( 21 * DAY_IN_SECONDS ), '/' );
			// get tags and add to user meta
			$tags = $api->get_subscriber_tags( $subscriber_id );
			update_user_meta( $user->ID, 'convertkit_tags', json_encode( $tags ) );
		}

	}

	/**
	 * If the user arrives at the site with a URL parameter of 'ck_subscriber_id' then cookie the user with that value.
	 *
	 * @see https://app.convertkit.com/account/edit#email_settings
	 * @param $post
	 */
	public static function maybe_tag_subscriber( $post ) {

		if ( isset( $_COOKIE['ck_subscriber_id']) && absint( $_COOKIE['ck_subscriber_id'] ) ) {
			$subscriber_id = absint( $_COOKIE['ck_subscriber_id'] );
			$api  = WP_ConvertKit::get_api();
			$meta = get_post_meta( $post->ID, '_wp_convertkit_post_meta', true );
			$tag  = isset( $meta['tag'] ) ? $meta['tag'] : 0;

			// get subscriber's email to add tag with
			$subscriber = $api->get_subscriber( $subscriber_id );

			if ( $subscriber ) {
				// tag subscriber
				$args = array(
					'email' => $subscriber->email_address,
				);

				if ( $tag ) {
					$api->add_tag( $tag, $args );
					$api->log( "tagging subscriber (" . $subscriber_id . ")" . " with tag (" . $tag . ")" );
				} else {
					$api->log( "post_id (" . $post->ID . ") not found in user history" );
				}
			}
		}

	}

}

new ConvertKit_Custom_Content();
