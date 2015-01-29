<?php

require_once "base.php";

class ConvertKitSettingsGeneral extends ConvertKitSettingsSection {

  public function __construct() {
    $this->settings_page = 'wp_convertkit_general_options';
    $this->name          = 'general';
    $this->title         = 'ConvertKit General Settings';
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
      $this->settings_page,
      $this->name
    );

    add_settings_field(
      'default_form',
      'Default Form',
      array($this, 'default_form_callback'),
      $this->settings_page,
      $this->name,
      $this->api->_get_resources('forms')
    );
  }

  /**
   * Prints help info for this section
   */
  public function print_section_info() {
    print 'General settings for the ConvertKit App';
  }

  /**
   * Renders the input for api key entry
   */
  public function api_key_callback() {
    printf(
      '<input type="text" class="regular-text code" id="api_key" name="%s[api_key]" value="%s" />',
      $this->settings_name,
      isset($this->options['api_key']) ? esc_attr($this->options['api_key']) : ''
    );
  }

  /**
   * Renders the form select list
   * @param  array $forms Form listing
   */
  public function default_form_callback($forms) {
    $html = sprintf('<select id="default_form" name="%s[default_form]">', $this->settings_name);
      $html .= '<option value="default">None</option>';
      foreach($forms as $form) {
        $html .= '<option value="' . esc_attr($form['id']) . '"' . selected( $this->options['default_form'], $form['id'], false) . '>' . esc_html($form['name']) . '</option>';
      }
    $html .= '</select>';

    echo $html;
  }
}

$convertkit_settings->register_section("ConvertKitSettingsGeneral");
