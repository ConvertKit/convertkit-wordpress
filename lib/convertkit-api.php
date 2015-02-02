<?php

/**
 * Establishes API connection to ConvertKit App
 */
class ConvertKitAPI {

  private $api_key   = null;
  private $resources = array();
  private $markup    = array();

  /**
   * Constructor for ConvertKitAPI instance
   *
   * @param String $api_key ConvertKit API Key
   */
  public function __construct($api_key) {
    $this->api_key = $api_key;
  }

  public function get_resources($resource) {
    if(is_null($this->resources[$resource])) {
      $api_response = $this->_get_api_response($resource);

      if (is_wp_error($api_response) || isset($api_response['error']) || isset($api_response['error_message'])) {
        $this->resources[$resource] = array();
      } else {
        $this->resources[$resource] = $api_response;
      }
    }

    return $this->resources[$resource];
  }

  public function get_resource($url) {
    $resource = '';

    if(!empty($url) && isset($this->markup[$url])) {
      $resource = $this->marku[$url];
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

        $this->marku[$url] = $resource = $html->save();
      }
    }

    return $resource;
  }

  private function _get_api_response($path = '', $version = '2') {
    $args = array('k' => $this->api_key, 'v' => $version);
    $url = add_query_arg($args, path_join('https://api.convertkit.com/', $path));

    $response = wp_remote_get($url);

    if(is_wp_error($response)) {
      $data = $response;
    } else {
      $data = json_decode(wp_remote_retrieve_body($response), true);
    }

    return $data;
  }

}
