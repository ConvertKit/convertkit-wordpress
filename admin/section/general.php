<?php

require_once "base.php";

class ConvertKitSettingsGeneral extends ConvertKitSettingsSection {

  public function __construct() {
    $this->settings_key  = WP_ConvertKit::SETTINGS_PAGE_SLUG;
    $this->name          = 'general';
    $this->title         = 'General Settings';
    $this->tab_text      = 'General';

    parent::__construct();
  }

  /**
   * Register and add settings
   */
  public function register_fields() {
    add_settings_field(
      'api_key',
      'API Key',
      array($this, 'api_key_callback'),
      $this->settings_key,
      $this->name
    );

    add_settings_field(
      'default_form',
      'Default Form',
      array($this, 'default_form_callback'),
      $this->settings_key,
      $this->name,
      $this->api->get_resources('forms')
    );
  }

  /**
   * Prints help info for this section
   */
  public function print_section_info() {
    ?>
    <p>
      Connect your ConvertKit account to Wordpress to start using your forms
      and landing pages within your site.
    </p>
    <?php
  }

  /**
   * Renders the input for api key entry
   */
  public function api_key_callback() {
    $html = sprintf(
      '<input type="text" class="regular-text code" id="api_key" name="%s[api_key]" value="%s" />',
      $this->settings_key,
      isset($this->options['api_key']) ? esc_attr($this->options['api_key']) : ''
    );

    $html .= '<p class="description"><a href="https://app.convertkit.com/account/edit" target="_blank">Get your ConvertKit API Key</a></p>';

    echo $html;
  }

  /**
   * Renders the form select list
   *
   * @param array $forms Form listing
   */
  public function default_form_callback($forms) {
    $html = sprintf('<select id="default_form" name="%s[default_form]">', $this->settings_key);
      $html .= '<option value="default">None</option>';
      foreach($forms as $form) {
        $html .= sprintf(
          '<option value="%s" %s>%s</option>',
          esc_attr($form['id']),
          selected($this->options['default_form'], $form['id'], false),
          esc_html($form['name'])
        );
      }
    $html .= '</select>';

    if (empty($forms)) {
      $html .= '<p class="description">Enter your API Key above to get your available forms.</p>';
    }

    echo $html;
  }

  /**
   * Sanitizes the settings
   *
   * @param  array $settings The settings fields submitted
   * @return array           Sanitized settings
   */
  public function sanitize_settings($settings) {
    return shortcode_atts(array(
      'api_key'      => '',
      'default_form' => 0
    ), $settings);
  }
}
