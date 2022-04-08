<?php
/**
 * ConvertKit Contact Form 7 Admin Settings class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Registers Contact Form 7 Settings that can be edited at Settings > ConvertKit > Contact Form 7.
 *
 * @package ConvertKit
 * @author ConvertKit
 */
class ConvertKit_ContactForm7_Admin_Settings extends ConvertKit_Settings_Base {

	/**
	 * Constructor
	 */
	public function __construct() {

		// Define the class that reads/writes settings.
		$this->settings = new ConvertKit_ContactForm7_Settings();

		// Define the settings key.
		$this->settings_key = $this->settings::SETTINGS_NAME;

		// Define the programmatic name, Title and Tab Text.
		$this->name     = 'contactform7';
		$this->title    = __( 'Contact Form 7 Integration Settings', 'convertkit' );
		$this->tab_text = __( 'Contact Form 7', 'convertkit' );

		parent::__construct();

	}

	/**
	 * Register fields for this section
	 */
	public function register_fields() {

		// No fields are registered, because they are output in a WP_List_Table
		// in this class' render() function.
		// This function is deliberately blank.
	}

	/**
	 * Prints help info for this section.
	 */
	public function print_section_info() {

		?>
		<p>
			<?php
			esc_html_e( 'ConvertKit seamlessly integrates with Contact Form 7 to let you add subscribers using Contact Form 7 forms.', 'convertkit' );
			?>
		</p>
		<p>
			<?php
			_e( 'The Contact Form 7 form must have a <code>text*</code> field named <code>your-name</code> and an <code>email*</code> field named <code>your-email</code>. These fields will be sent to ConvertKit for the subscription.', 'convertkit' ); // phpcs:ignore
			?>
		</p>
		<?php

	}

	/**
	 * Outputs the section as a WP_List_Table of Contact Form 7 Forms, with options to choose
	 * a ConvertKit Form mapping for each.
	 *
	 * @since   1.9.6
	 */
	public function render() {

		do_settings_sections( $this->settings_key );

		// Get Forms.
		$forms = new ConvertKit_Resource_Forms();

		// Bail with an error if no ConvertKit Forms exist.
		if ( ! $forms->exist() ) {
			$this->output_error( __( 'No Forms exist on ConvertKit.', 'convertkit' ) );
			return;
		}

		// Build array of select options.
		$options = array(
			'default' => __( 'None', 'convertkit' ),
		);
		foreach ( $forms->get() as $form ) {
			$options[ esc_attr( $form['id'] ) ] = esc_html( $form['name'] );
		}

		// Get Contact Form 7 Forms.
		$cf7_forms = $this->get_cf7_forms();

		// Bail with an error if no Contact Form 7 Forms exist.
		if ( ! $cf7_forms ) {
			$this->output_error( __( 'No Contact Form 7 Forms exist in the Contact Form 7 Plugin.', 'convertkit' ) );
			return;
		}

		// Setup WP_List_Table.
		$table = new Multi_Value_Field_Table();
		$table->add_column( 'title', __( 'Contact Form 7 Form', 'convertkit' ), true );
		$table->add_column( 'form', __( 'ConvertKit Form', 'convertkit' ), false );
		$table->add_column( 'email', __( 'Contact Form 7 Email Field', 'convertkit' ), false );
		$table->add_column( 'name', __( 'Contact Form 7 Name Field', 'convertkit' ), false );

		// Iterate through Contact Form 7 Forms, adding a table row for each Contact Form 7 Form.
		foreach ( $cf7_forms as $cf7_form ) {
			$table->add_item(
				array(
					'title' => $cf7_form['name'],
					'form'  => $this->get_select_field(
						$cf7_form['id'],
						(string) $this->settings->get_convertkit_form_id_by_cf7_form_id( $cf7_form['id'] ),
						$options
					),
					'email' => 'your-email',
					'name'  => 'your-name',
				)
			);
		}

		// Prepare and display WP_List_Table.
		$table->prepare_items();
		$table->display();

		// Register settings field.
		settings_fields( $this->settings_key );

		// Render submit button.
		submit_button();

	}

	/**
	 * Gets available forms from CF7
	 */
	private function get_cf7_forms() {

		$forms = array();

		$result = new WP_Query(
			array(
				'post_type'      => 'wpcf7_contact_form',
				'posts_per_page' => -1,
				'orderby'        => 'ID',
				'order'          => 'ASC',
			)
		);

		if ( ! is_array( $result->posts ) ) {
			return false;
		}
		if ( ! count( $result->posts ) ) {
			return false;
		}

		foreach ( $result->posts as $post ) {
			$forms[] = array(
				'id'   => $post->ID,
				'name' => $post->post_title,
			);
		}

		return $forms;

	}

}

// Register Admin Settings section.
add_filter(
	'convertkit_admin_settings_register_sections',
	function( $sections ) {

		// Bail if Contact Form 7 isn't enabled.
		if ( ! defined( 'WPCF7_VERSION' ) ) {
			return $sections;
		}

		// Register this class as a section at Settings > ConvertKit.
		$sections['contactform7'] = new ConvertKit_ContactForm7_Admin_Settings();
		return $sections;

	}
);
