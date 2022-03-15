<?php
/**
 * ConvertKit Form Widget class
 *
 * @package ConvertKit
 * @author ConvertKit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Form Widget.
 *
 * @author   ConvertKit
 * @version  1.0.0
 */
class CK_Widget_Form extends WP_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {

		parent::__construct(
			'convertkit_form',
			__( 'ConvertKit Form', 'convertkit' ),
			array(
				'classname'                   => 'convertkit widget_convertkit_form',
				'description'                 => __( 'Display a ConvertKit form.', 'convertkit' ),
				'customize_selective_refresh' => true,
			)
		);

	}

	/**
	 * Outputs the settings update form.
	 *
	 * @since   1.0.0
	 *
	 * @param   array $instance   Current settings.
	 * @return  string              Default return is 'noform'
	 */
	public function form( $instance ) {

		$forms = new ConvertKit_Resource_Forms();

		// Bail if no Forms exist.
		if ( ! $forms->exist() ) {
			?>
			<p>
				<?php esc_html_e( 'To display a ConvertKit Form, at least one form must be defined in your ConvertKit Account.', 'convertkit' ); ?>
			</p>
			<?php
		}
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'convertkit' ); ?></label>
			<input type="text" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" value="<?php esc_attr( $instance['title'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'form' ) ); ?>"><?php esc_html_e( 'Form', 'convertkit' ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'form' ) ); ?>" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'form' ) ); ?>" size="1">
				<?php
				foreach ( $forms->get() as $form ) {
					?>
					<option value="<?php echo esc_attr( $form['id'] ); ?>"<?php selected( $form['id'], $instance['form'] ); ?>>
						<?php echo esc_attr( $form['name'] ); ?>
					</option>
					<?php
				}
				?>
			</select>
		</p>
		<?php

		return '';

	}

	/**
	 * Output widget.
	 *
	 * @see WP_Widget
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance Widget settings.
	 */
	public function widget( $args, $instance ) {

		// Bail if no form is defined.
		if ( ! isset( $instance['form'] ) ) {
			return;
		}

		// Get Form.
		$forms = new ConvertKit_Resource_Forms();
		$form  = $forms->get_html( $instance['form'] );

		// Bail if the Form has an error.
		if ( is_wp_error( $form ) ) {
			return;
		}

		// Output Form.
		echo $args['before_widget']; // phpcs:ignore
		if ( $instance['title'] ) {
			echo $args['before_title'] . $instance['title'] . $args['after_title']; // phpcs:ignore
		}
		echo $form; // phpcs:ignore
		echo $args['after_widget']; // phpcs:ignore

	}

	/**
	 * Updates a particular instance of a widget.
	 *
	 * @see    WP_Widget->update
	 * @param  array $new_instance Updated widget settings.
	 * @param  array $old_instance Original widget settings.
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) { // phpcs:ignore

		return array(
			'title' => sanitize_text_field( $new_instance['title'] ),
			'form'  => sanitize_text_field( $new_instance['form'] ),
		);

	}

}
