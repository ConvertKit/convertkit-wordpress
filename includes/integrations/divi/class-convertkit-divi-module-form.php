<?php
/**
 * Divi Module: ConvertKit Form.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Registers the ConvertKit Form Block as a Divi Module.
 *
 * @package ConvertKit
 * @author  ConvertKit
 */
class ConvertKit_Divi_Module_Form extends ConvertKit_Divi_Module {

	/**
	 * The ConvertKit block name.
	 *
	 * @since   2.5.6
	 *
	 * @var     string
	 */
	public $block_name = 'form';

	/**
	 * The ConvertKit Divi module name.
	 *
	 * @since   2.5.6
	 *
	 * @var     string
	 */
	public $slug = 'convertkit_form';

	/**
	 * Checks if any Forms exist in ConvertKit.
	 *
	 * If no fields exist, shows on screen instructions
	 * instead of the configuration fields.
	 *
	 * @since   2.5.6
	 */
	public function getxxxx_fields() {

		// Bail if no block.
		if ( is_wp_error( $this->block ) ) {
			return array();
		}

		// Bail if no fields.
		if ( ! is_array( $this->block['fields'] ) ) {
			return array();
		}

		// If no Forms exist, return a description field with instructions.
		$convertkit_forms = new ConvertKit_Resource_Forms( 'divi' );
		if ( ! $convertkit_forms->exist() ) {
			return array(
				'form' => array(
					'type'        => 'text',
					'default'     => '',
					'description' => 'No Forms exist in ConvertKit. Instructions here.',
					'label'       => __( 'Form', 'convertkit' ),
					'toggle_slug' => 'main_content',
				),
			);
		}

		// Call parent function to build configuration fields in Divi.
		parent::get_fields();

	}

}

new ConvertKit_Divi_Module_Form();
