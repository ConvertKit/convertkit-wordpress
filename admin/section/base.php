<?php

abstract class ConvertKitSettingsSection {
  public $is_registerable = true;
  public $name;
  public $title;
  public $tab_text;
  public $settings_page;

  public $settings_name = WP_ConvertKit::SETTINGS_NAME;

  public $api;
  public $options;

  public function __construct() {
    global $convertkit_settings;

    $this->api      = $convertkit_settings->api;
    $this->options  = $convertkit_settings->options;
    if (empty($this->tab_text)) $this->tab_text = $this->title;

    $this->register_section();
  }

  /**
   * Register settings section
   */
  public function register_section() {
    add_settings_section(
      $this->name,
      $this->title,
      array($this, 'print_section_info'),
      $this->settings_page
    );

    $this->register_fields();
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
