<?php

/**
 * Establishes API connection to ConvertKit App
 */
class ConvertKitAPI {

	/** @var string  */
	protected $api_key;

	/** @var string */
	protected $api_secret;

	/** @var  string */
	protected $debug;

	/** @var string  */
	protected $api_version = 'v3';

	/** @var string  */
	protected $api_url_base = 'https://api.convertkit.com/';

	/** @var array  */
	protected $resources = array();

	/** @var array  */
	protected $markup = array();

	/**
	 * Constructor for ConvertKitAPI instance
	 *
	 * @param string $api_key ConvertKit API Key
	 * @param string $api_secret ConvertKit API Secret
	 */
	public function __construct($api_key, $api_secret, $debug) {
		$this->api_key = $api_key;
		$this->api_secret = $api_secret;
		$this->debug = $debug;
	}

	/**
	 * Gets a resource index
	 *
	 * GET /{$resource}/
	 *
	 * @param string $resource Resource type
	 * @return object API response
	 */
	public function get_resources($resource) {

		if(!array_key_exists($resource, $this->resources)) {

			if ( $resource == 'landing_pages' ) {
				$api_response = $this->_get_api_response( 'forms' );
			} else {
				$api_response = $this->_get_api_response( $resource );
			}

			if (is_null($api_response) || is_wp_error($api_response) || isset($api_response['error']) || isset($api_response['error_message'])) {
				$this->resources[$resource] = array( array('id' => '-2', 'name' => 'Error contacting API' ) );
			} else {
				$_resource = array();

				if ( 'forms' == $resource ) {
					$response = isset( $api_response['forms']) ? $api_response['forms'] : array();
					foreach( $response as $form ) {
						if ( isset( $form['archived'] ) && $form['archived'] )
							continue;
						$_resource[] = $form;
					}
				} elseif ( 'landing_pages' == $resource ) {

					$response = isset( $api_response['forms']) ? $api_response['forms'] : array();
					foreach( $response as $landing_page ){
						if ( 'hosted' == $landing_page['type'] ){
							if ( isset( $landing_page['archived'] ) && $landing_page['archived'] )
								continue;
							$_resource[] = $landing_page;
						}
					}
				} elseif ( 'subscription_forms' == $resource ) {
					foreach( $api_response as $mapping ){
						if ( isset( $mapping['archived'] ) && $mapping['archived'] )
							continue;
						$_resource[ $mapping['id'] ] =  $mapping['form_id'];
					}
				}

				$this->resources[$resource] = $_resource;
			}
		}

		return $this->resources[$resource];
	}

	/**
	 * Adds a subscriber to a form
	 *
	 * @param string $form_id Form ID
	 * @param array $options Array of user data
	 * @return object
	 */
	public function form_subscribe($form_id, $options) {
		$request = $this->api_version . sprintf('/forms/%s/subscribe', $form_id);

		$args = array(
			'api_key' => $this->api_key,
			'email'   => $options['email'],
			'name'   => $options['name'],
		);

		return $this->make_request($request, 'POST', $args);
	}

	/**
	 * Remove subscription from a form
	 *
	 * @param array $options Array of user data
	 * @return object Response object
	 */
	public function form_unsubscribe($options) {
		$request = $this->api_version . '/unsubscribe';

		$args = array(
			'api_secret' => $this->api_secret,
			'email' => $options['email']
		);

		return $this->make_request($request, 'PUT', $args);
	}

	/**
	 * Get markup from ConvertKit for the provided $url
	 *
	 * @param $url
	 * @return string
	 */
	public function get_resource($url) {
		$resource = '';

		if(!empty($url) && isset($this->markup[$url])) {
			$resource = $this->markup[$url];
		} else if(!empty($url)) {
			$response = wp_remote_get($url, array( 'timeout' => 10 ));

			if(!is_wp_error($response)) {
				if(!function_exists('str_get_html')) {
					require_once(dirname(__FILE__).'/../vendor/simple-html-dom/simple-html-dom.php');
				}

				if(!function_exists('url_to_absolute')) {
					require_once(dirname(__FILE__).'/../vendor/url-to-absolute/url-to-absolute.php');
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

				// check `status_code` for 200, otherwise log error
				if ( '200' == $response['response']['code'] ) {
					$this->markup[$url] = $resource = $html->save();
				} else {
					$this->log('Status Code (' . $response['response']['code'] . ') for URL (' . $url .'): ' . $html->save() );
				}
			}
		}

		return $resource;
	}

	/**
	 * Do a remote request.
	 *
	 * @param string $path
	 * @return array
	 */
	private function _get_api_response($path = '') {

		$args = array('api_key' => $this->api_key);
		$api_path = $this->api_url_base . $this->api_version;
		$url = add_query_arg($args, path_join($api_path, $path));

		$this->log( "API Request (_get_api_response): " . $url );

		$data = get_transient( 'convertkit_get_api_response' );

		if ( ! $data ) {

			$response = wp_remote_get( $url, array( 'timeout' => 10, 'sslverify' => false ) );

			if ( is_wp_error( $response ) ) {
				$this->log( "Error: " . $response->get_error_message() );

				return array( 'error' => $response->get_error_message() );
			} else {
				$data = json_decode( wp_remote_retrieve_body( $response ), true );
			}

			set_transient( 'convertkit_get_api_response', $data, 300 );

			$this->log( "API Response (_get_api_response): " . print_r( $data, true ) );
		} else {
			$this->log( "Transient Response (_get_api_response)" );
		}

		return $data;
	}

	/**
	 * Make a request to the ConvertKit API
	 *
	 * @param string $request Request string
	 * @param string $method HTTP Method
	 * @param array $args Request arguments
	 * @return object Response object
	 */
	public function make_request($request, $method = 'GET', $args = array()) {

		$url = $this->build_request_url($request, $args);
		$this->log( "API Request (make_request): " . $url );

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		if ( 'PUT' == $method ){
			curl_setopt($ch, CURLOPT_PUT, true);
		}

		$results = curl_exec($ch);
		curl_close($ch);

		$this->log( "API Response (make_request): " . print_r( json_decode( $results ), true) );

		return json_decode($results);
	}

	/**
	 * Build the full request URL
	 *
	 * @param string $request Request path
	 * @param array $args Request arguments
	 * @return string	Request URL
	 */
	public function build_request_url($request, array $args) {
		return $this->api_url_base . $request . '?' . http_build_query( $args );
	}

	/**
	 * @param $message
	 */
	public function log( $message ) {

		if ( 'on' == $this->debug ) {
			$dir = dirname( __FILE__ );

			$handle = fopen( trailingslashit( $dir ) . 'log.txt', 'a' );
			if ( $handle ) {
				$time   = date_i18n( 'm-d-Y @ H:i:s -' );
				fwrite( $handle, $time . " " . $message . "\n" );
				fclose( $handle );
			}
		}

	}

}
