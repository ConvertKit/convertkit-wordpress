<?php

class ConvertKitSettings {
  public $api;
  public $sections = array();
  public $options;

  public $settings_page  = WP_ConvertKit::SETTINGS_PAGE_SLUG;
  public $settings_name  = WP_ConvertKit::SETTINGS_NAME;

  public function __construct() {
    $this->api     = new ConvertKitAPI(get_option( $this->settings_name )['api_key']);
    $this->options = get_option( $this->settings_name );

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
   * Register main settings
   */
  public function register_settings() {
    register_setting(
      $this->settings_name,
      $this->settings_name,
      array($this, 'sanitize_settings')
    );
  }

  /**
   * Options page callback
   */
  public function display_settings_page() {
    $active_section = (isset($_GET['tab'])) ? $_GET['tab'] : $this->sections[0]->name;

    ?>
    <div class="wrap">
      <?php screen_icon(); ?>
      <h2><?php _e('ConvertKit Settings', 'wp_convertkit'); ?></h2>

      <?php if(count($this->sections) > 1) $this->display_section_nav($active_section); ?>

      <form method="post" action="options.php">
        <?php
        foreach($this->sections as $section):
          if ($active_section == $section->name):
            do_settings_sections($section->settings_page);
          endif;
        endforeach;
        settings_fields($this->settings_page);
        submit_button();
        ?>
      </form>
    </div>
    <?php
  }

  /**
   * Render a tab for each section
   *
   * @param string $active_section The currently active section
   */
  public function display_section_nav($active_section) {
    ?>
    <h2 class="nav-tab-wrapper">
      <?php
      foreach($this->sections as $section):
        printf(
          '<a href="?page=%s&tab=%s" class="nav-tab %s">%s</a>',
          $this->settings_page,
          $section->name,
          $active_section == $section->name ? 'nav-tab-active' : '',
          esc_html($section->tab_text)
        );
      endforeach;
      ?>
    </h2>
    <?php
  }

  /**
   * Adds a section to be displayed
   *
   * @param string $section A section class name
   */
  public function register_section($section) {
    array_push($this->sections, new $section());
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

if( is_admin() ) {
  $convertkit_settings = new ConvertKitSettings();

  include 'section/general.php';
}
