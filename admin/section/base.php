<?php

abstract class ConvertKitSettingsSection {
  public $is_registerable = true;
  public $name;
  public $title;
  public $tab_text;
  public $settings_key;

  public $api;
  public $options;

  public function __construct() {
    global $convertkit_settings;

    $this->api      = $convertkit_settings->api;
    $this->options = get_option($this->settings_key);
    if (empty($this->tab_text)) $this->tab_text = $this->title;

    $this->register_section();
  }

  /**
   * Register settings section
   */
  public function register_section() {
    if(false == get_option($this->settings_key)) {
      add_option($this->settings_key);
    }

    add_settings_section(
      $this->name,                          // Section name (machine-readable)
      $this->title,                         // Section title
      array($this, 'print_section_info'),   // Info callback
      $this->settings_key                   // Settings page
    );

    $this->register_fields();

    register_setting(
      $this->settings_key, // Page
      $this->settings_key, // Settings DB Key
      array($this, 'sanitize_settings')
    );
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

  /**
   * Register settings fields
   */
  abstract public function register_fields();

  /**
   * Prints help info for this section
   */
  abstract public function print_section_info();
}
