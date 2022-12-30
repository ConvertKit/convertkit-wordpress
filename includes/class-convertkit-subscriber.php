<?php
/**
 * ConvertKit Subscriber class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Class to confirm a ConvertKit Subscriber ID exists, writing/reading
 * it from cookie storage.
 *
 * @since   2.0.0
 */
class ConvertKit_Subscriber {

	/**
	 * Holds the key to check on requests and store as a cookie.
	 *
	 * @since   2.0.0
	 *
	 * @var     string
	 */
	private $key = 'ck_subscriber_id';

	/**
	 * Gets the subscriber ID from either the request's `ck_subscriber_id` parameter,
	 * or the existing `ck_subscriber_id` cookie.
	 *
	 * @since   2.0.0
	 *
	 * @return  WP_Error|bool|int|string    Error | false | Subscriber ID | Signed Subscriber ID
	 */
	public function get_subscriber_id() {

		// If the subscriber ID is in the request URI, use it.
		if ( isset( $_REQUEST[ $this->key ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return $this->validate_and_store_subscriber_id( sanitize_text_field( $_REQUEST[ $this->key ] ) ); // phpcs:ignore WordPress.Security.NonceVerification
		}

		// If the subscriber ID is in a cookie, return it.
		// For performance, we don't check that the subscriber ID exists every time, otherwise this would
		// call the API on every page load.
		if ( isset( $_COOKIE[ $this->key ] ) ) {
			return $this->get_subscriber_id_from_cookie();
		}

		// If here, no subscriber ID exists.
		return false;

	}

	/**
	 * Validates the given subscriber ID by querying the API to confirm
	 * the subscriber exists before storing their ID in a cookie.
	 *
	 * @since   2.0.0
	 *
	 * @param   int|string $subscriber_id  Possible Subscriber ID or Signed Subscriber ID.
	 * @return  WP_Error|int|string                 Error | Confirmed Subscriber ID or Signed Subscriber ID
	 */
	public function validate_and_store_subscriber_id( $subscriber_id ) {

		// Bail if the API hasn't been configured.
		$settings = new ConvertKit_Settings();
		if ( ! $settings->has_api_key_and_secret() ) {
			return new WP_Error(
				'convertkit_subscriber_get_subscriber_id_from_request_error',
				__( 'API Key and Secret not configured in Plugin Settings.', 'convertkit' )
			);
		}

		// Initialize the API.
		$api = new ConvertKit_API( $settings->get_api_key(), $settings->get_api_secret(), $settings->debug_enabled() );

		// Get subscriber by ID, to ensure they exist.
		$subscriber = $api->get_subscriber_by_id( $subscriber_id );

		// Bail if no subscriber exists with the given subscriber ID, or an error occured.
		if ( is_wp_error( $subscriber ) ) {
			// Delete the cookie.
			$this->forget();

			// Return error.
			return $subscriber;
		}

		// Store the subscriber ID as a cookie.
		$this->set( $subscriber['id'] );

		// Return subscriber ID.
		return $subscriber['id'];

	}

	/**
	 * Validates the given subscriber email by querying the API to confirm
	 * the subscriber exists before storing their ID in a cookie.
	 *
	 * @since   2.0.0
	 *
	 * @param   string $subscriber_email   Possible Subscriber Email.
	 * @return  WP_Error|int|string                     Error | Confirmed Subscriber ID or Signed Subscriber ID
	 */
	public function validate_and_store_subscriber_email( $subscriber_email ) {

		// Bail if the API hasn't been configured.
		$settings = new ConvertKit_Settings();
		if ( ! $settings->has_api_key_and_secret() ) {
			return new WP_Error(
				'convertkit_subscriber_get_subscriber_id_from_request_error',
				__( 'API Key and Secret not configured in Plugin Settings.', 'convertkit' )
			);
		}

		// Initialize the API.
		$api = new ConvertKit_API( $settings->get_api_key(), $settings->get_api_secret(), $settings->debug_enabled() );

		// Get subscriber by email, to ensure they exist.
		$subscriber = $api->get_subscriber_by_email( $subscriber_email );

		// Bail if no subscriber exists with the given subscriber ID, or an error occured.
		if ( is_wp_error( $subscriber ) ) {
			// Delete the cookie.
			$this->forget();

			// Return error.
			return $subscriber;
		}

		// Store the subscriber ID as a cookie.
		$this->set( $subscriber['id'] );

		// Return subscriber ID.
		return $subscriber['id'];

	}

	/**
	 * Gets the subscriber ID from the `ck_subscriber_id` cookie.
	 *
	 * @since   2.0.0
	 */
	private function get_subscriber_id_from_cookie() {

		return $_COOKIE[ $this->key ];

	}

	/**
	 * Stores the given subscriber ID in the `ck_subscriber_id` cookie.
	 *
	 * @since   2.0.0
	 *
	 * @param   int|string $subscriber_id  Subscriber ID.
	 */
	public function set( $subscriber_id ) {

		setcookie( $this->key, (string) $subscriber_id, time() + ( 365 * DAY_IN_SECONDS ), '/' );

	}

	/**
	 * Deletes the `ck_subscriber_id` cookie.
	 *
	 * @since   2.0.0
	 */
	public function forget() {

		setcookie( $this->key, '', time() - ( 365 * DAY_IN_SECONDS ), '/' );

	}

}
