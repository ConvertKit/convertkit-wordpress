<?php
/**
 * ConvertKit AJAX class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Registers AJAX actions for the Plugin.
 *
 * @since   1.9.6
 */
class ConvertKit_AJAX {

	/**
	 * Constructor.
	 *
	 * @since   1.9.6
	 */
	public function __construct() {

		add_action( 'wp_ajax_convertkit_get_blocks', array( $this, 'get_blocks' ) );

		add_action( 'wp_ajax_nopriv_convertkit_store_subscriber_id_in_cookie', array( $this, 'store_subscriber_id_in_cookie' ) );
		add_action( 'wp_ajax_convertkit_store_subscriber_id_in_cookie', array( $this, 'store_subscriber_id_in_cookie' ) );

		add_action( 'wp_ajax_nopriv_convertkit_store_subscriber_email_as_id_in_cookie', array( $this, 'store_subscriber_email_as_id_in_cookie' ) );
		add_action( 'wp_ajax_convertkit_store_subscriber_email_as_id_in_cookie', array( $this, 'store_subscriber_email_as_id_in_cookie' ) );

		add_action( 'wp_ajax_nopriv_convertkit_tag_subscriber', array( $this, 'tag_subscriber' ) );
		add_action( 'wp_ajax_convertkit_tag_subscriber', array( $this, 'tag_subscriber' ) );

	}

	/**
	 * Returns all ConvertKit registered blocks.
	 *
	 * Typically used when a refresh button in a block has been pressed when
	 * convertKitGutenbergDisplayBlockNoticeWithLink() is called, because either
	 * no API keys were specified, or no resources exist in ConvertKit.
	 *
	 * @since   2.2.6
	 */
	public function get_blocks() {

		// Check nonce.
		check_ajax_referer( 'convertkit_get_blocks', 'nonce' );

		// Refresh resources from the API, to reflect any changes.
		$forms = new ConvertKit_Resource_Forms( 'block_edit' );
		$forms->refresh();

		$posts = new ConvertKit_Resource_Posts( 'block_edit' );
		$posts->refresh();

		$products = new ConvertKit_Resource_Products( 'block_edit' );
		$products->refresh();

		// Return blocks.
		wp_send_json_success( convertkit_get_blocks() );

	}

	/**
	 * Stores the ConvertKit Subscriber's ID in a cookie.
	 *
	 * Typically performed when the user subscribes via a ConvertKit Form on the web site
	 * that is set to "Send subscriber to thank you page", and the Plugin's JavaScript is not
	 * disabled, permitting convertkit.js to run.
	 *
	 * @since   1.9.6
	 */
	public function store_subscriber_id_in_cookie() {

		// Check nonce.
		check_ajax_referer( 'convertkit', 'convertkit_nonce' );

		// Bail if required request parameters not submitted.
		if ( ! isset( $_REQUEST['subscriber_id'] ) ) {
			wp_send_json_error( __( 'ConvertKit: Required parameter `subscriber_id` not included in AJAX request.', 'convertkit' ) );
		}

		// Bail if no subscriber ID provided.
		$id = absint( sanitize_text_field( $_REQUEST['subscriber_id'] ) );
		if ( empty( $id ) ) {
			wp_send_json_error( __( 'ConvertKit: Required parameter `subscriber_id` empty in AJAX request.', 'convertkit' ) );
		}

		// Get subscriber ID.
		$subscriber    = new ConvertKit_Subscriber();
		$subscriber_id = $subscriber->validate_and_store_subscriber_id( $id );

		// Bail if an error occured i.e. API hasn't been configured, subscriber ID does not exist in ConvertKit etc.
		if ( is_wp_error( $subscriber_id ) ) {
			wp_send_json_error( $subscriber_id->get_error_message() );
		}

		// Return the subscriber ID.
		wp_send_json_success(
			array(
				'id' => $subscriber_id,
			)
		);

	}

	/**
	 * Stores the ConvertKit Subscriber Email's ID in a cookie.
	 *
	 * Typically performed when the user subscribes via a ConvertKit Form on the web site
	 * and the Plugin's JavaScript is not disabled, permitting convertkit.js to run.
	 *
	 * @since   1.9.6
	 */
	public function store_subscriber_email_as_id_in_cookie() {

		// Check nonce.
		check_ajax_referer( 'convertkit', 'convertkit_nonce' );

		// Bail if required request parameters not submitted.
		if ( ! isset( $_REQUEST['email'] ) ) {
			wp_send_json_error( __( 'ConvertKit: Required parameter `email` not included in AJAX request.', 'convertkit' ) );
		}
		$email = sanitize_text_field( $_REQUEST['email'] );

		// Bail if the email address is empty.
		if ( empty( $email ) ) {
			wp_send_json_error( __( 'ConvertKit: Required parameter `email` is empty.', 'convertkit' ) );
		}

		// Bail if the email address isn't a valid email address.
		if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
			wp_send_json_error( __( 'ConvertKit: Required parameter `email` is not an email address.', 'convertkit' ) );
		}

		// Get subscriber ID.
		$subscriber    = new ConvertKit_Subscriber();
		$subscriber_id = $subscriber->validate_and_store_subscriber_email( $email );

		// Bail if an error occured i.e. API hasn't been configured, subscriber ID does not exist in ConvertKit etc.
		if ( is_wp_error( $subscriber_id ) ) {
			wp_send_json_error( $subscriber_id->get_error_message() );
		}

		// Return the subscriber ID.
		wp_send_json_success(
			array(
				'id' => $subscriber_id,
			)
		);

	}

	/**
	 * Tags a subscriber when their subscriber ID is present in the cookie or URL,
	 * and the Page's ConvertKit Settings specify a Tag.
	 *
	 * @since   1.9.6
	 */
	public function tag_subscriber() {

		// Check nonce.
		check_ajax_referer( 'convertkit', 'convertkit_nonce' );

		// Bail if required request parameters not submitted.
		if ( ! isset( $_REQUEST['subscriber_id'] ) ) {
			wp_send_json_error( __( 'ConvertKit: Required parameter `subscriber_id` not included in AJAX request.', 'convertkit' ) );
		}
		if ( ! isset( $_REQUEST['tag'] ) ) {
			wp_send_json_error( __( 'ConvertKit: Required parameter `tag` not included in AJAX request.', 'convertkit' ) );
		}
		$subscriber_id = absint( sanitize_text_field( $_REQUEST['subscriber_id'] ) );
		$tag_id        = absint( sanitize_text_field( $_REQUEST['tag'] ) );

		// Bail if no subscriber ID or tag provided.
		if ( empty( $subscriber_id ) ) {
			wp_send_json_error( __( 'ConvertKit: Required parameter `subscriber_id` empty in AJAX request.', 'convertkit' ) );
		}
		if ( empty( $tag_id ) ) {
			wp_send_json_error( __( 'ConvertKit: Required parameter `tag` empty in AJAX request.', 'convertkit' ) );
		}

		// Bail if the API hasn't been configured.
		$settings = new ConvertKit_Settings();
		if ( ! $settings->has_api_key_and_secret() ) {
			wp_send_json_error( __( 'ConvertKit: API Keys not defined in Plugin Settings.', 'convertkit' ) );
		}

		// Initialize the API.
		$api = new ConvertKit_API( $settings->get_api_key(), $settings->get_api_secret(), $settings->debug_enabled() );

		// Get subscriber's email address by subscriber ID.
		$subscriber = $api->get_subscriber_by_id( $subscriber_id );

		// Bail if the subscriber could not be found.
		if ( is_wp_error( $subscriber ) ) {
			wp_send_json_error( $subscriber->get_error_message() );
		}

		// Extract the subscriber's email.
		$email = $subscriber['email_address'];

		// Store the subscriber ID as a cookie.
		$subscriber = new ConvertKit_Subscriber();
		$subscriber->set( $subscriber_id );

		// Tag the subscriber with the Post's tag.
		$tag = $api->tag_subscribe( $tag_id, $email );

		// Bail if an error occured tagging the subscriber.
		if ( is_wp_error( $tag ) ) {
			wp_send_json_error( $tag );
		}

		wp_send_json_success( $tag );

	}

}
