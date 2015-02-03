<?php

require_once "base.php";
require_once plugin_dir_path( __FILE__ ) . "../../lib/multi_value_field_table.php";

class ConvertKitSettingsWishlistMember extends ConvertKitSettingsSection {

  /**
   * WLM levels
   * @var array
   */
  private $wlm_levels;

  public function __construct() {
    if (!function_exists('wlmapi_get_levels')) {
      return $this->is_registerable = false;
    }

    $this->settings_key  = '_wp_convertkit_integration_wishlistmember_settings';
    $this->name          = 'wishlist-member';
    $this->title         = 'WishList Member Integration Settings';
    $this->tab_text      = 'WishList Member';

    $this->wlm_levels    = $this->get_wlm_levels();

    parent::__construct();
  }

  /**
   * Gets membership levels from WishList Member API
   *
   * @return array Membership levels
   */
  public function get_wlm_levels() {
    $wlm_get_levels = wlmapi_get_levels();

    if ($wlm_get_levels['success'] == 1) {
      return $this->wlm_levels = $wlm_get_levels['levels']['level'];
    } else {
      return array();
    }
  }

  /**
   * Register and add settings
   */
  public function register_fields() {

    $all_forms = $this->api->get_all_forms();

    foreach($this->wlm_levels as $wlm_level) {
      add_settings_field(
        $wlm_level['id'],
        $wlm_level['name'],
        array($this, 'wlm_level_callback'),
        $this->settings_key,
        $this->name,
        array(
          'wlm_level_id' => $wlm_level['id'],
          'forms'        => $all_forms
        )
      );
    }
  }

  /**
   * Prints help info for this section
   */
  public function print_section_info() {
    ?>
    <p>
      ConvertKit seamlessly integrates with WishList Member to let you capture
      all of your WishList Membership registrations within your ConvertKit forms.
    </p>
    <?php
  }

  /**
   * Render the settings table. Designed to mimic WP's do_settings_fields
   */
  public function do_settings_table() {
    global $wp_settings_fields;

    $table = new MultiValueFieldTable;

    $table->add_column('title', 'WishList Membership Level', true);
    $table->add_column('ck_form_select', 'ConvertKit Form');

    $fields = $wp_settings_fields[$this->settings_key][$this->name];

    foreach ($fields as $field) {
      $table->add_item(array(
        'title'          => $field['title'],
        'ck_form_select' => call_user_func($field['callback'], $field['args'])
      ));
    }

    $table->prepare_items();
    $table->display();
  }

  /**
   * Renders the section
   */
  public function render() {
    global $wp_settings_sections, $wp_settings_fields;

    if (!isset($wp_settings_sections[$this->settings_key])) return;

    foreach ($wp_settings_sections[$this->settings_key] as $section) {
      if ($section['title']) echo "<h3>{$section['title']}</h3>\n";
      if ($section['callback']) call_user_func($section['callback'], $section);

      if (!empty($this->api->get_resources('forms'))) {
        $this->do_settings_table();
        settings_fields($this->settings_key);
        submit_button();
      } else {
        ?>
        <p>
          To set up this integration, you will first need to enter a valid
          ConvertKit API key in the
          <a href="?page=_wp_convertkit_settings&tab=general">General Settings</a>.
        </p>
        <?php
      }
    }
  }

  /**
   * Renders a form select list for a wlm level
   *
   * @param array $options WLM level and CK forms
   */
  public function wlm_level_callback($arguments) {
    $wlm_level_id = $arguments['wlm_level_id'];
    $forms  = $arguments['forms'];

    $html = sprintf('<select id="%1$s_%2$s" name="%1$s[%2$s]">', $this->settings_key, $wlm_level_id);
      $html .= '<option value="default">None</option>';
      foreach($forms as $form) {
        $html .= sprintf(
          '<option value="%s" %s>%s</option>',
          esc_attr($form['resource_id']),
          selected($this->options[$wlm_level_id], $form['resource_id'], false),
          esc_html($form['name'])
        );
      }
    $html .= '</select>';

    return $html;
  }

  /**
   * Sanitizes the settings
   *
   * @param  array $settings The settings fields submitted
   * @return array           Sanitized settings
   */
  public function sanitize_settings($settings) {
    $defaults = array();

    foreach ($this->wlm_levels as $wlm_level) {
      $defaults[$wlm_level['id']] = '0';
    }

    return shortcode_atts($defaults, $settings);
  }
}
