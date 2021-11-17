<?php
/**
 * ConvertKit Widgets class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Registers Widgets.
 *
 * @since   1.0.0
 */
class ConvertKit_Widgets {

	/**
	 * Constructor.
	 *
	 * @since   1.0.0
	 */
	public function __construct() {

		add_action( 'widgets_init', array( $this, 'register_widgets' ) );

	}

	/**
	 * Register widget.
	 *
	 * @since   1.0.0
	 */
	public function register_widgets() {

		register_widget( 'CK_Widget_Form' );

	}

}
