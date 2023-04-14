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
	 * Holds the ConvertKit Forms resource class.
	 *
	 * @since   2.2.0
	 *
	 * @var     bool|ConvertKit_Resource_Forms
	 */
	public $forms = false;

	/**
	 * Constructor
	 *
	 * @since   2.2.0
	 */
	public function __construct() {

		// Register this as a Gutenberg block toolbar button in the ConvertKit Plugin.
		add_filter( 'convertkit_block_toolbar_buttons', array( $this, 'register' ) );

		// Output JS.
				// Adds the data-commerce attribute to HTML links that link to a ConvertKit Product.
		add_filter( 'the_content', array( $this, 'maybe_append_script' ) );

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
			'data-id'		  	  => '',
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
					'data-id'				=> sanitize_text_field( $form['id'] ),
					'data-formkit-toggle' 	=> sanitize_text_field( $form['uid'] ),
					'href' 					=> $form['embed_url'],
				);
			}
		}

		// Return field.
		return array(
			'form' => array(
				'label'  		=> __( 'Form', 'convertkit' ),
				'type'   		=> 'select',
				'description' 	=> __( 'The modal, sticky bar or slide in form to display when the text is clicked.', 'convertkit' ),

				// Key/value pairs for the <select> dropdown.
				'values' 		=> $forms,

				// Contains all additional data required to build the link.
				'data'   		=> $forms_data,
			),
		);

	}

	/**
	 * Filters the Post / Page's content, appending the necessary ConvertKit script where
	 * modal, sticky bar or slide in form links have been added by this block formatter
	 * in Gutenberg.
	 * 
	 * We do this here, because we can't arbitrarily inject a <script> using a block formatter
	 * in Gutenberg.
	 *
	 * @since   2.2.0
	 *
	 * @param   string $content    Page/Post Content.
	 * @return  string              Page/Post Content
	 */
	public function maybe_append_script( $content ) {

		// Return content, unedited, if no form links exist in the content.
		if ( strpos( $content, 'data-formkit-toggle' ) === false ) {
			return $content;
		}

		// Get Forms.
		$this->forms = new ConvertKit_Resource_Forms();

		// Return content, unedited, if no Forms exist.
		if ( ! $this->forms->exist() ) {
			return $content;
		}

		// Inject scripts.
		$content = preg_replace_callback(
			'#<' . $this->get_tag() . ' data-id="([^"]*)" data-formkit-toggle="([^"]*)".*?href="([^"]*)".*?>([^>]*)</' . $this->get_tag() . '>#i',
			array( $this, 'inject_scripts' ),
			$content
		);

		// Return.
		return $content;

	}

	/**
	 * Callback function to append script to a matching link.
	 * 
	 * @since 	2.2.0
	 * 
	 * @param 	array 	$match 	preg_replace_callback() match.
	 * @return 	string 			Link with script appended
	 */
	public function inject_scripts( $match ) {

		// Get Form by its ID.
		$form = $this->forms->get_by_id( absint( $match[1] ) );

		// Just return the original element, unedited, if the Form could not be found.
		if ( ! $form ) {
			return $match[0];
		}

		// Return the original link, unedited, if the Form doesn't have a UID or JS embed.
		// This prevents issues with legacy modal forms.
		if ( ! array_key_exists( 'uid', $form ) ) {
			return $match[0];
		}
		if ( ! array_key_exists( 'embed_js', $form ) ) {
			return $match[0];
		}

		// Inject the script immediately after the link.
		$script = '<script async data-uid="' . esc_attr( $form['uid'] ) . '" src="' . esc_url( $form['embed_js'] ) .'"></script>';

		// Return.
		return $match[0] . $script;

	}

}
