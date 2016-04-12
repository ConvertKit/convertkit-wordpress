<?php

/**
 * Establishes API connection to ConvertKit App
 */
class ConvertKitAPI {

	/** @var String  */
	protected $api_key;

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
	 * @param String $api_key ConvertKit API Key
	 */
	public function __construct($api_key) {
		$this->api_key = $api_key;
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
			// v3 only has 'forms' resource.
			$api_response = $this->_get_api_response('forms');

			if (is_null($api_response) || is_wp_error($api_response) || isset($api_response['error']) || isset($api_response['error_message'])) {
				$this->resources[$resource] = array();
			} else {
				$_resource = array();
				// v3 doesn't have landing_pages resource. Instead check 'type' for 'hosted'
				if ( 'forms' == $resource ) {
					foreach ( $api_response as $form ){
						if ( 'embed' == $form['type'] ){
							$_resource[] = $form;
						}
					}
				} elseif ( 'landing_pages' == $resource ) {
					foreach ( $api_response as $landing_page ){
						if ( 'hosted' == $landing_page['type'] ){
							$_resource[] = $landing_page;
						}
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
		$request = sprintf('forms/%s/subscribe', $form_id);

		$args = array(
			'email' => $options['email'],
			'fname' => $options['fname']
		);

		return $this->make_request($request, 'POST', $args);
	}

	/**
	 * Remove subscription from a form
	 *
	 * @param string $form_id Resource ID
	 * @param array $options Array of user data
	 * @return object Response object
	 */
	public function form_unsubscribe($form_id, $options) {
		$request = sprintf('forms/%s/unsubscribe', $form_id);

		$args = array(
			'email' => $options['email']
		);

		return $this->make_request($request, 'POST', $args);
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
			$response = wp_remote_get($url, array( 'timeout' => 2 ));

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

				$this->markup[$url] = $resource = $html->save();
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
		$response = wp_remote_get($url, array( 'timeout' => 2 ));

		if(is_wp_error($response)) {
			return array();
		} else {
			$data = json_decode(wp_remote_retrieve_body($response), true);
		}

		return isset($data[$path]) ? $data[$path] : array();
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

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

		$results = curl_exec($ch);

		curl_close($ch);

		return json_decode($results);
	}

	/**
	 * Merge default request arguments with those of this request
	 *
	 * @param array $args Request arguments
	 * @return array Request arguments
	 */
	public function filter_request_arguments($args = array()) {
		return array_merge($args, array('k' => $this->api_key, 'v' => $this->api_version));
	}

	/**
	 * Build the full request URL
	 *
	 * @param string $request Request path
	 * @param array $args Request arguments
	 * @return string	Request URL
	 */
	public function build_request_url($request, array $args) {
		return $this->api_url_base . $request . '?' . http_build_query($this->filter_request_arguments($args));
	}

}
