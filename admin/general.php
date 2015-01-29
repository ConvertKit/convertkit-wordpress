<?php

class ConvertKitGeneralSettings {
  private $options;
  private $api;

  public $settings_group = '_wp_convertkit_settings_general';

  public $settings_page  = WP_ConvertKit::SETTINGS_PAGE_SLUG;
  public $settings_name  = WP_ConvertKit::SETTINGS_NAME;

  public function __construct() {
    $this->api = new ConvertKitAPI(get_option( $this->settings_name )['api_key']);

    add_action('admin_menu', array($this, 'add_settings_page'));
    add_action('admin_init', array($this, 'register_settings'));
  }

  /**
   * Add the options page
   */
  public function add_settings_page() {
    $settings = add_options_page(
      __('ConvertKit Settings'),
      __('ConvertKit'),
      'manage_options',
      $this->settings_page,
      array($this, 'display_settings_page')
    );
  }

  /**
   * Options page callback
   */
  public function display_settings_page() {
    $this->options = get_option( $this->settings_name );

    ?>
    <div class="wrap">
      <?php screen_icon(); ?>
      <h2><?php _e('ConvertKit Settings', 'wp_convertkit'); ?></h2>
      <form method="post" action="options.php">
        <?php
        settings_fields($this->settings_group);
        do_settings_sections($this->settings_page);
        submit_button();
        ?>
      </form>
    </div>
    <?php
  }

  /**
   * Register and add settings
   */
  public function register_settings() {
    register_setting(
      $this->settings_group,
      $this->settings_name,
      array($this, 'sanitize_settings')
    );

    add_settings_section(
      'convertkit_general_settings',
      'General',
      array($this, 'print_section_info'),
      $this->settings_page
    );

    add_settings_field(
      'api_key',
      'API Key',
      array($this, 'api_key_callback'),
      $this->settings_page,
      'convertkit_general_settings'
    );

    add_settings_field(
      'default_form',
      'Default Form',
      array($this, 'default_form_callback'),
      $this->settings_page,
      'convertkit_general_settings',
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
   * Sanitizes the settings
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

if( is_admin() ) $ck_general_settings = new ConvertKitGeneralSettings();
