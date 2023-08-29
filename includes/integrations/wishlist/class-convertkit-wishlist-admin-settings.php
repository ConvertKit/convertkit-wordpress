<?php
/**
 * ConvertKit Wishlist Admin Settings class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Registers Wishlist Settings that can be edited at Settings > ConvertKit > Wishlist.
 *
 * @package ConvertKit
 * @author ConvertKit
 */
class ConvertKit_Wishlist_Admin_Settings extends ConvertKit_Settings_Base {

	/**
	 * Constructor.
	 *
	 * @since   1.9.6
	 */
	public function __construct() {

		// Define the class that reads/writes settings.
		$this->settings = new ConvertKit_Wishlist_Settings();

		// Define the settings key.
		$this->settings_key = $this->settings::SETTINGS_NAME;

		// Define the programmatic name, Title and Tab Text.
		$this->name     = 'wishlist-member';
		$this->title    = __( 'WishList Member Integration Settings', 'convertkit' );
		$this->tab_text = __( 'WishList Member', 'convertkit' );

		parent::__construct();

	}

	/**
	 * Register fields for this section.
	 *
	 * @since   1.9.6
	 */
	public function register_fields() {

		// No fields are registered, because they are output in a WP_List_Table
		// in this class' render() function.
		// This function is deliberately blank.
	}

	/**
	 * Prints help info for this section.
	 *
	 * @since   1.9.6
	 */
	public function print_section_info() {

		?>
		<p>
			<?php
			esc_html_e( 'ConvertKit seamlessly integrates with WishList Member to let you capture all of your WishList Membership registrations within your ConvertKit forms.', 'convertkit' );
			?>
		</p>
		<?php

	}

	/**
	 * Returns the URL for the ConvertKit documentation for this setting section.
	 *
	 * @since   2.0.8
	 *
	 * @return  string  Documentation URL.
	 */
	public function documentation_url() {

		return 'https://help.convertkit.com/en/articles/2502591-the-convertkit-wordpress-plugin';

	}

	/**
	 * Outputs the section as a WP_List_Table of WishList Member Levels, with options to choose
	 * a ConvertKit Form and Tag mapping for each.
	 *
	 * @since   1.9.6
	 */
	public function render() {

		// Render opening container.
		$this->render_container_start();

		do_settings_sections( $this->settings_key );

		// Get WishList Member Levels.
		$wlm_levels = $this->get_wlm_levels();

		// Bail with an error if no WishList Member Levels exist.
		if ( ! $wlm_levels ) {
			$this->output_error( __( 'No WishList Member Levels exist in the WishList Member Plugin.', 'convertkit' ) );
			$this->render_container_end();
			return;
		}

		// Get Forms and Tags.
		$forms = new ConvertKit_Resource_Forms( 'wishlist_member' );
		$tags  = new ConvertKit_Resource_Tags( 'wishlist_member' );

		// Bail with an error if no ConvertKit Forms exist.
		if ( ! $forms->exist() ) {
			$this->output_error( __( 'No Forms exist on ConvertKit.', 'convertkit' ) );
			$this->render_container_end();
			return;
		}

		// Bail with an error if no ConvertKit Tags exist.
		if ( ! $tags->exist() ) {
			$this->output_error( __( 'No Tags exist on ConvertKit.', 'convertkit' ) );
			$this->render_container_end();
			return;
		}

		// Build array of select options for Forms.
		$form_options = array(
			'default' => __( 'None', 'convertkit' ),
		);
		foreach ( $forms->get() as $form ) {
			$form_options[ esc_attr( $form['id'] ) ] = esc_html( $form['name'] );
		}

		// Build array of select options for Tags.
		$tag_options = array(
			'0' => __( 'None', 'convertkit' ),
		);
		foreach ( $tags->get() as $tag ) {
			$tag_options[ esc_attr( $tag['id'] ) ] = esc_html( $tag['name'] );
		}
		$tag_options['unsubscribe'] = __( 'Unsubscribe from all', 'convertkit' );

		// Setup WP_List_Table.
		$table = new Multi_Value_Field_Table();
		$table->add_column( 'title', __( 'WishList Membership Level', 'convertkit' ), true );
		$table->add_column( 'form', __( 'ConvertKit Form', 'convertkit' ), false );
		$table->add_column( 'unsubscribe', __( 'Unsubscribe Action', 'convertkit' ), false );

		// Iterate through WishList Member Levels, adding a table row for each Level.
		foreach ( $wlm_levels as $wlm_level ) {
			$table->add_item(
				array(
					'title'       => $wlm_level['name'],
					'form'        => $this->get_select_field(
						$wlm_level['id'] . '_form',
						(string) $this->settings->get_convertkit_form_id_by_wishlist_member_level_id( $wlm_level['id'] ),
						$form_options
					),
					'unsubscribe' => $this->get_select_field(
						$wlm_level['id'] . '_unsubscribe',
						(string) $this->settings->get_convertkit_tag_id_by_wishlist_member_level_id( $wlm_level['id'] ),
						$tag_options
					),
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

		// Render closing container.
		$this->render_container_end();

	}

	/**
	 * Gets membership levels from WishList Member API
	 *
	 * @return bool|array
	 */
	public function get_wlm_levels() {

		// Get WishList Member Levels from the API.
		$wlm_get_levels = wlmapi_get_levels();

		// Bail if the API call failed.
		if ( $wlm_get_levels['success'] !== 1 ) {
			return false;
		}

		return $wlm_get_levels['levels']['level'];

	}

}

// Register Admin Settings section.
add_filter(
	'convertkit_admin_settings_register_sections',
	/**
	 * Register WishList Member as a section at Settings > ConvertKit.
	 *
	 * @param   array   $sections   Settings Sections.
	 * @return  array
	 */
	function ( $sections ) {

		// Bail if WishList Member isn't enabled.
		if ( ! function_exists( 'wlmapi_get_levels' ) ) {
			return $sections;
		}

		// Register this class as a section at Settings > ConvertKit.
		$sections['wishlist-member'] = new ConvertKit_Wishlist_Admin_Settings();
		return $sections;

	}
);
