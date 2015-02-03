<?php

class ConvertKitSettings {
  public $api;
  public $sections = array();

  public $settings_key  = WP_ConvertKit::SETTINGS_PAGE_SLUG;

  public function __construct() {
    $this->api     = new ConvertKitAPI(get_option( $this->settings_key )['api_key']);

    add_action('admin_menu', array($this, 'add_settings_page'));
    add_action('admin_init', array($this, 'register_sections'));
  }

  /**
   * Add the options page
   */
  public function add_settings_page() {
    $settings = add_options_page(
      __('ConvertKit Settings'),
      __('ConvertKit'),
      'manage_options',
      $this->settings_key,
      array($this, 'display_settings_page')
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
            $section->render();
          endif;
        endforeach;
        ?>
        <p class="description">
          If you have questions or problems, please contact
          <a href="mailto:support@convertkit.com">support@convertkit.com</a>
        </p>
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
          '<a href="?page=%s&tab=%s" class="nav-tab right %s">%s</a>',
          $this->settings_key,
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
    $section_instance = new $section();

    if ($section_instance->is_registerable) {
      array_push($this->sections, $section_instance);
    }
  }

  /**
   * Register each section
   */
  public function register_sections() {
    $this->register_section('ConvertKitSettingsGeneral');
    $this->register_section('ConvertKitSettingsWishlistMember');
  }
}

if( is_admin() ) {
  $convertkit_settings = new ConvertKitSettings();

  include 'section/general.php';
  include 'section/wishlist_member.php';
}
