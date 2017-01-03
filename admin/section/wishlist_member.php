<?php
require_once plugin_dir_path( __FILE__ ) . "../../lib/multi_value_field_table.php";

/**
 * Class ConvertKitSettingsWishlistMember
 */
class ConvertKitSettingsWishlistMember extends ConvertKitSettingsSection {

  /**
   * WLM levels
   * @var array
   */
  private $wlm_levels;

  /**
   * Cont
   */
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

    $forms = $this->api->get_resources('forms');

    foreach($this->wlm_levels as $wlm_level) {
      add_settings_field(
        sprintf('%s_title', $wlm_level['id']),
        __( 'WishList Membership Level', 'convertkit' ),
        array($this, 'wlm_title_callback'),
        $this->settings_key,
        $this->name,
        array(
          'wlm_level_id'   => $wlm_level['id'],
          'wlm_level_name' => $wlm_level['name'],
          'sortable'       => true
        )
      );

      add_settings_field(
        sprintf('%s_form', $wlm_level['id']),
        __( 'ConvertKit Form', 'convertkit' ),
        array($this, 'wlm_level_callback'),
        $this->settings_key,
        $this->name,
        array(
          'wlm_level_id' => $wlm_level['id'],
          'forms'        => $forms
        )
      );

      add_settings_field(
        sprintf('%s_unsubscribe', $wlm_level['id']),
        __( 'Unsubscribe', 'convertkit' ),
        array($this, 'wlm_unsubscribe_callback'),
        $this->settings_key,
        $this->name,
        array(
          'wlm_level_id' => $wlm_level['id']
        )
      );
    }
  }

  /**
   * Prints help info for this section
   */
  public function print_section_info() {
    ?><p><?php echo __('ConvertKit seamlessly integrates with WishList Member to let you capture all of your WishList Membership registrations within your ConvertKit forms.', 'convertkit');
    ?></p>
    <?php
  }

  /**
   * Render the settings table. Designed to mimic WP's do_settings_fields
   */
  public function do_settings_table() {
    global $wp_settings_fields;

    $table   = new MultiValueFieldTable;
    $columns = array();
    $rows    = array();
    $fields  = $wp_settings_fields[$this->settings_key][$this->name];

    foreach ($fields as $field) {
      list($wlm_level_id, $field_type) = explode('_', $field['id']);

      if (!in_array($field_type, $columns)) {
        $table->add_column($field_type, $field['title'], $field['args']['sortable']);
        array_push($columns, $field_type);
      }

      if (!isset($rows[$wlm_level_id])) {
        $rows[$wlm_level_id] = array();
      }

      $rows[$wlm_level_id][$field_type] = call_user_func($field['callback'], $field['args']);
    }

    foreach ($rows as $row) {
      $table->add_item($row);
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

      $forms = $this->api->get_resources('forms');

      if (!empty($forms)) {
        $this->do_settings_table();
        settings_fields($this->settings_key);
        submit_button();
      } else {
        ?>
        <p><?php
          echo __('To set up this integration, you will first need to enter a valid ConvertKit API key in the', 'convertkit' );
         ?><a href="?page=_wp_convertkit_settings&tab=general"><?php echo __( 'General Settings', 'convertkit'); ?></a>.
        </p>
        <?php
      }
    }
  }

  /**
   * Title for WishList Membership Level
   *
   * @param  array  $arguments Arguments from add_settings_field()
   * @return string              WishList Membership Level title
   */
  public function wlm_title_callback($arguments) {
    return $arguments['wlm_level_name'];
  }

  /**
   * CK Form select for WishList Membership Level
   *
   * @param  array  $arguments Arguments from add_settings_field()
   * @return string              Select element
   */
  public function wlm_level_callback($arguments) {
    $wlm_level_id = $arguments['wlm_level_id'];
    $forms  = $arguments['forms'];

    $html = sprintf('<select id="%1$s_%2$s_form" name="%1$s[%2$s_form]">', $this->settings_key, $wlm_level_id);
      $html .= '<option value="default">' . __( 'None', 'convertkit' ) . '</option>';
      foreach($forms as $form) {
        $html .= sprintf(
          '<option value="%s" %s>%s</option>',
          esc_attr($form['id']),
          selected($this->options[$wlm_level_id . '_form'], $form['id'], false),
          esc_html($form['name'])
        );
      }
    $html .= '</select>';

    return $html;
  }

  /**
   * Unsubscribe field for WishList Membership Level
   *
   * @param  array  $arguments Arguments from add_settings_field()
   * @return string              Checkbox and label
   */
  public function wlm_unsubscribe_callback($arguments) {
    $wlm_level_id = $arguments['wlm_level_id'];

    $html = sprintf(
      '<input type="checkbox" id="%1$s_%2$s_unsubscribe" value="1" name="%1$s[%2$s_unsubscribe]" %3$s>',
      $this->settings_key,
      $wlm_level_id,
      checked($this->options[$wlm_level_id . '_unsubscribe'], 1, false)
    );
    $html .= sprintf('<label for="%1$s_%2$s_unsubscribe">%3$s</label>', $this->settings_key, $wlm_level_id, __( 'Unsubscribe if removed from level', 'convertkit' ));

    return $html;
  }

  /**
   * Sanitizes the settings
   *
   * @param  array $input The settings fields submitted
   * @return array           Sanitized settings
   */
  public function sanitize_settings($input) {
    // Settings page can be paginated; combine input with existing options
    $output = $this->options;

    foreach($input as $key => $value) {
      list($level_id, $setting) = explode('_', $key);

      // Unsubscribe must be manually set when moving from true to false
      if (!isset($input["{$level_id}_unsubscribe"])) {
        $output["{$level_id}_unsubscribe"] = 0;
      }

      $output[$key] = stripslashes( $input[$key] );
    }

    return apply_filters("sanitize{$this->settings_key}", $output, $input);
  }
}
