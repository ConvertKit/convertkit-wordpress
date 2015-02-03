<?php

/**
 * Establishes API connection to ConvertKit App
 */
class ConvertKitAPI {

  protected $api_key;

  protected $api_version  = 2;
  protected $api_url_base = 'https://api.convertkit.com/';
  protected $resources    = array();
  protected $markup       = array();

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
   * @param  string $resource Resource type
   * @return object           API response
   */
  public function get_resources($resource) {
    if(is_null($this->resources[$resource])) {
      $api_response = $this->_get_api_response($resource);

      if (is_wp_error($api_response) || isset($api_response['error']) || isset($api_response['error_message'])) {
        $this->resources[$resource] = array();
      } else {
        $this->resources[$resource] = array_map(function($item) use ($resource) {
          $item['resource_id'] = sprintf('%1$s|%2$s', $resource, $item['id']);
          return $item;
        }, $api_response);
      }
    }

    return $this->resources[$resource];
  }

  /**
   * Special method to return unified array of forms and landing pages
   *
   * @return array Mixed array of landing pages and forms
   */
  public function get_all_forms() {
    $forms         = $this->get_resources('forms');
    $landing_pages = $this->get_resources('landing_pages');

    $all_forms     = array_merge(array_values($forms), array_values($landing_pages));
    usort($all_forms, function($a, $b) {
      return strcmp($a["name"], $b["name"]);
    });

    return $all_forms;
  }

  /**
   * Adds a subscriber to a resource
   *
   * @param string $resource_type Resource type
   * @param string $resource_id   Resource ID
   * @param array  $options       Array of user data
   */
  public function add_resource_subscriber($resource_type, $resource_id, $options) {
    $request = sprintf('%1$s/%2$s/subscribe', $resource_type, $resource_id);

    $args    = array(
      'email' => $options['email'],
      'fname' => $options['fname']
    );

    return $this->make_request($request, 'POST', $args);
  }

  public function get_resource($url) {
    $resource = '';

    if(!empty($url) && isset($this->markup[$url])) {
      $resource = $this->markup[$url];
    } else if(!empty($url)) {
      $response = wp_remote_get($url);

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

  private function _get_api_response($path = '') {
    $args = array('k' => $this->api_key, 'v' => $this->api_version);
    $url = add_query_arg($args, path_join($this->api_url_base, $path));

    $response = wp_remote_get($url);

    if(is_wp_error($response)) {
      $data = $response;
    } else {
      $data = json_decode(wp_remote_retrieve_body($response), true);
    }

    return $data;
  }

  /**
   * Make a request to the ConvertKit API
   *
   * @param  string $request Request string
   * @param  string $method  HTTP Method
   * @param  array  $args    Request arguments
   * @return object          Response object
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
   * @param  array  $args Request arguments
   * @return array        Request arguments
   */
  public function filter_request_arguments($args = array()) {
    return array_merge($args, array('k' => $this->api_key, 'v' => $this->api_version));
  }

  /**
   * Build the full request URL
   *
   * @param  string $request Request path
   * @param  array  $args    Request arguments
   * @return string          Request URL
   */
  public function build_request_url($request, array $args) {
    return $this->api_url_base . $request . '?' . http_build_query($this->filter_request_arguments($args));
  }

}
