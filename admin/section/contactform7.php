<?php
/**
 * Class ConvertKitSettingsContactForm7
 *
 * @since 1.4.4
 */
class ConvertKitSettingsContactForm7 extends ConvertKitSettingsSection {

	/** @var array Contact Form 7 forms */
	private $forms;

	/**
	 * Constructor
	 */
	public function __construct() {

		if (!defined('WPCF7_VERSION')) {
			$this->is_registerable = false;
			return;
		}

		$this->settings_key  = '_wp_convertkit_integration_contactform7_settings';
		$this->name          = 'contactform7';
		$this->title         = 'Contact Form 7 Integration Settings';
		$this->tab_text      = 'Contact Form 7';

		$this->get_cf7_forms();

		parent::__construct();
	}

	/**
	 * Gets available forms from CF7
	 *
	 * @return array
	 */
	public function get_cf7_forms() {

		$forms = array();

		$posts = get_posts( array(
			'numberposts' => -1,
			'orderby' => 'ID',
			'order' => 'ASC',
			'post_type' => 'wpcf7_contact_form' )
		);

		foreach( $posts as $post ){
			$forms[] = array(
				'id' => $post->ID,
				'name' => $post->post_title,
			);
		}
		$this->forms = $forms;
	}

	/**
	 * Register and add settings
	 */
	public function register_fields() {

		$forms = $this->api->get_resources('forms');

		foreach($this->forms as $form) {

			add_settings_field(
				sprintf('%s_title', $form['id']),
				'Contact Form 7 Form',
				array($this, 'cf7_title_callback'),
				$this->settings_key,
				$this->name,
				array(
					'cf7_form_id'   => $form['id'],
					'cf7_form_name' => $form['name'],
					'sortable'      => true
				)
			);

			add_settings_field(
				sprintf('%s_form', $form['id']),
				'ConvertKit Form',
				array($this, 'cf7_form_callback'),
				$this->settings_key,
				$this->name,
				array(
					'cf7_form_id' => $form['id'],
					'forms'       => $forms,
					'sortable'    => false
				)
			);

			add_settings_field(
				sprintf('%s_email', $form['id']),
				'CF7 Email Field',
				array($this, 'cf7_email_callback'),
				$this->settings_key,
				$this->name,
				array(
					'cf7_email_id' => 'your-email',
					'sortable'     => false
				)
			);

			add_settings_field(
				sprintf('%s_name', $form['id']),
				'CF7 Name Field',
				array($this, 'cf7_name_callback'),
				$this->settings_key,
				$this->name,
				array(
					'cf7_name_id' => 'your-name',
					'sortable'    => false
				)
			);

		}
	}

	/**
	 * Prints help info for this section
	 */
	public function print_section_info() {
		?><p><?php
		echo __( 'ConvertKit seamlessly integrates with Contact Form 7 to let you add subscribers using Contact Form 7 forms.', 'convertkit' );
		?></p><p><?php
		echo __( 'The Contact Form 7 form must have <code>text*</code> fields named <code>your-name</code> and <code>your-email</code>. ', 'convertkit' );
		echo __( 'These fields will be sent to ConvertKit for the subscription.', 'convertkit' );
		?></p><?php
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
			list($cf7_form_id, $field_type) = explode('_', $field['id']);

			if (!in_array($field_type, $columns)) {
				$table->add_column($field_type, $field['title'], $field['args']['sortable']);
				array_push($columns, $field_type);
			}

			if (!isset($rows[$cf7_form_id])) {
				$rows[$cf7_form_id] = array();
			}

			$rows[$cf7_form_id][$field_type] = call_user_func($field['callback'], $field['args']);
		}

		foreach ($rows as $row) {
			$table->add_item($row);
		}

		$table->prepare_items();
		$table->display();
	}

	/**
	 * Renders the section
	 *
	 * Called from ConvertKitSettings::display_settings_page()
	 * @return void
	 */
	public function render() {
		global $wp_settings_sections;

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
				<p><?php echo sprintf( __( 'To set up this integration, you will first need to enter a valid ConvertKit API key in the %s.', 'convertkit'),
					'<a href="?page=_wp_convertkit_settings&tab=general">' . __('General Settings', 'convertkit') . '</a>');
				?></p>
				<?php
			}
		}
	}

	/**
	 * Display form title in first column
	 *
	 * @param  array
	 * @return string
	 */
	public function cf7_title_callback( $args ) {
		return $args['cf7_form_name'];
	}

	/**
	 * Display CF7 to CK mapping
	 *
	 * @param  array $args
	 * @return string $html
	 */
	public function cf7_form_callback( $args ) {
		$cf7_form_id = $args['cf7_form_id'];
		$forms  = $args['forms'];

		$html = sprintf('<select id="%1$s_%2$s" name="%1$s[%2$s]">', $this->settings_key, $cf7_form_id);
		$html .= '<option value="default">' . __( 'None', 'convertkit') . '</option>';
		foreach($forms as $form) {
			$html .=
				'<option value="' .
				esc_attr($form['id']).'" ' .
				selected($this->options[$cf7_form_id], $form['id'], false) .
				'>' .
				esc_html($form['name']) . '</option>';
		}
		$html .= '</select>';

		return $html;
	}


	/**
	 * Display email in first column
	 *
	 * @param array $args
	 * @return string
	 */
	public function cf7_email_callback( $args ) {
		return $args['cf7_email_id'];
	}

	/**
	 * Display form title in first column
	 *
	 * @param array $args
	 * @return string
	 */
	public function cf7_name_callback( $args ) {
		return $args['cf7_name_id'];
	}

	/**
	 * Sanitizes the settings
	 *
	 * @param  array $input
	 * @return array sanitized settings
	 */
	public function sanitize_settings($input) {
		// Settings page can be paginated; combine input with existing options
		$output = $this->options;

		foreach($input as $key => $value) {
			$output[$key] = stripslashes( $input[$key] );
		}

		return apply_filters( 'sanitize{$this->settings_key}', $output, $input);
	}
}
