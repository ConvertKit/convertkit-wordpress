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
 * @category Widgets
 * @version  1.0.0
 * @extends  WP_Widget
 */
class CK_Widget_Form extends WP_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {

		parent::__construct( 'convertkit_form', __( 'ConvertKit Form', 'convertkit' ), array(
			'classname'   => 'convertkit widget_convertkit_form',
			'description' => __( 'Display a ConvertKit form.', 'convertkit' ),
			'customize_selective_refresh' => true,
		) );

	}

	/**
	 * Outputs the settings update form.
	 *
	 * @param array $instance Widget settings.
	 * @return null
	 */
	public function form( $instance ) {

		$forms = new ConvertKit_Resource_Forms;

		// Bail if no Forms exist.
		if ( ! $forms->exist() ) {
			?>
			<p>
				<?php _e( 'To display a ConvertKit Form, at least one form must be defined in your ConvertKit Account.' ); ?>
			</p>
			<?php
		}

		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'convertkit' ); ?></label>
			<input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" value="<?php esc_attr( $instance['title'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'form' ); ?>"><?php _e( 'Form', 'convertkit' ); ?></label>
			<select name="<?php echo $this->get_field_name( 'form' ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'form' ); ?>" size="1">
				<?php
				foreach ( $forms->get() as $form ) {
					?>
					<option value="<?php echo $form['id']; ?>"<?php selected( $form['id'], $instance['form'] ); ?>>
						<?php echo $form['name']; ?>
					</option>
					<?php
				}
				?>
			</select>
		</p>
		<?php

	}

	/**
	 * Output the html at the start of a widget.
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance Widget settings.
	 */
	public function widget_start( $args, $instance ) {

		echo $args['before_widget'];
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

	}

	/**
	 * Output the html at the end of a widget.
	 *
	 * @param array $args After widget setting.
	 */
	public function widget_end( $args ) {

		echo $args['after_widget'];
		
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
		$forms = new ConvertKit_Resource_Forms;
		$form = $forms->get_html( $instance['form'] );
		
		// Bail if the Form has an error.
		if ( is_wp_error( $form ) ) {
			return;
		}

		// Output Form.
		$this->widget_start( $args, $instance );
		echo $form;
		$this->widget_end( $args );

	}


	/**
	 * Updates a particular instance of a widget.
	 *
	 * @see    WP_Widget->update
	 * @param  array $new_instance Updated widget settings.
	 * @param  array $old_instance Original widget settings.
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {

		return array(
			'title' => sanitize_text_field( $new_instance['title'] ),
			'form' => sanitize_text_field( $new_instance['form'] ),
		);

	}

}
