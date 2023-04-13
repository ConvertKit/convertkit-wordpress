<?php
/**
 * ConvertKit Block Toolbar Link Button class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * ConvertKit Block Toolbar Link Button definition for Gutenberg and TinyMCE.
 *
 * @package ConvertKit
 * @author  ConvertKit
 */
class ConvertKit_Block_Toolbar_Button_Link_Form extends ConvertKit_Block_Toolbar_Button {

	/**
	 * Constructor
	 *
	 * @since   2.2.0
	 */
	public function __construct() {

		// Register this as a Gutenberg block toolbar button in the ConvertKit Plugin.
		add_filter( 'convertkit_block_toolbar_buttons', array( $this, 'register' ) );

	}

	/**
	 * Returns this button's programmatic name, excluding the convertkit- prefix.
	 *
	 * @since   2.2.0
	 *
	 * @return  string
	 */
	public function get_name() {

		return 'link-form';

	}

	/**
	 * Returns this button's Title, Icon, Categories, Keywords and properties.
	 *
	 * @since   2.2.0
	 *
	 * @return  array
	 */
	public function get_overview() {

		return array(
			'title'                             => __( 'Link to ConvertKit', 'convertkit' ),
			'description'                       => __( 'Links the selected text to a ConvertKit Form or Product.', 'convertkit' ),
			'icon'                              => 'resources/backend/images/block-icon-form.png',
			
			// Gutenberg: Block Icon in Editor.
			'gutenberg_icon'                    => file_get_contents( CONVERTKIT_PLUGIN_PATH . '/resources/backend/images/block-icon-form.svg' ), /* phpcs:ignore */
		);

	}

	/**
	 * Returns this button's Attributes 
	 *
	 * @since   2.2.0
	 *
	 * @return  array
	 */
	public function get_attributes() {

		return array(
			//'data-form'		  	  => '', // Not needed for ConvertKit, but required for Gutenberg to know which Form to populate the <select> with.
			'data-formkit-toggle' => '',
            'href' 				  => '',
		);

	}

	/**
	 * Returns this block's Fields
	 *
	 * @since   2.2.0
	 *
	 * @return  bool|array
	 */
	public function get_fields() {

		// Bail if the request is not for the WordPress Administration or frontend editor.
		if ( ! WP_ConvertKit()->is_admin_or_frontend_editor() ) {
			return false;
		}

		// Get ConvertKit Forms.
		$forms            = array();
		$forms_data 	  = array();
		$convertkit_forms = new ConvertKit_Resource_Forms( 'block_edit' );
		if ( $convertkit_forms->exist() ) {
			foreach ( $convertkit_forms->get() as $form ) {
				// Ignore inline forms; this button link is only for modal, slide in and sticky bar forms.
				if ( ! array_key_exists( 'format', $form ) ) {
					continue;
				}
				if ( $form['format'] === 'inline' ) {
					continue;
				}

				// Add this form's necessary to the attribute arrays.
				$forms[ absint( $form['id'] ) ] = sanitize_text_field( $form['name'] );
				$forms_data[ absint( $form['id'] ) ] = array(
					'data-formkit-toggle' 	=> sanitize_text_field( $form['uid'] ),
					'href' 					=> $form['embed_url'],
				);
			}
		}

		// Return field.
		return array(
			'form' => array(
				'label'  => __( 'Form', 'convertkit' ),
				'type'   => 'select',

				// Key/value pairs for the <select> dropdown.
				'values' => $forms,

				// Contains all additional data required to build the link.
				'data'   => $forms_data,
			),
		);

	}

}
