<?php
/**
 * Class ConvertKit_Custom_Content
 *
 * @since 2.0
 */

class ConvertKit_Custom_Content {

	/**
	 * TODO
	 *
	 * Add shortcode  [convertkit_content tag='newsletter'] content [/convertkit_content]
	 * - page loads, get cookie sub_id
	 * - get mapped post_id-> tag
	 * - display custom content
	 */

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
	}

	/**
	 *
	 */
	public function register_shortcodes() {
		add_shortcode( 'convertkit_content', array( $this, 'shortcode' ) );
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
			if ( $user_id ) {
				// user is logged in so get tags from user meta
				error_log( "shortcode: user logged in, get tags from db" );
				$tags = get_user_meta( $user_id, 'convertkit_tags', true );
				$tags = ! empty( $tags ) ? json_decode( $tags, true ) : array();
			} elseif ( isset($_COOKIE['ck_subscriber_id'] ) ) {
				error_log( "shortcode: cookie found, calling API" );
				$api = WP_ConvertKit::get_api();
				// get cookie and check API for customer tags.
				$subscriber_id = absint( $_COOKIE['ck_subscriber_id'] );
				if ( $subscriber_id ) {
					$tags = $api->get_subscriber_tags( $subscriber_id );
				}
			} elseif ( isset( $_GET['ck_subscriber_id'] ) ) {
				error_log( "shortcode: URL param found, calling API" );
				$api = WP_ConvertKit::get_api();
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

}

new ConvertKit_Custom_Content();