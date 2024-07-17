<?php
/**
 * Outputs a dropdown field comprising of options covering:
 * - Do not subscribe
 * - Subscribe
 * - Forms
 *
 * @package ConvertKit
 * @author ConvertKit
 */

?>
<select class="<?php echo esc_attr( $class ); ?>" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>">
	<option <?php selected( '', $value ); ?> value="" data-preserve-on-refresh="1">
		<?php esc_html_e( '(Do not subscribe)', 'convertkit' ); ?>
	</option>

	<option <?php selected( 'subscribe', $value ); ?> value="subscribe" data-preserve-on-refresh="1">
		<?php esc_html_e( 'Subscribe', 'convertkit' ); ?>
	</option>

	<optgroup label="<?php esc_attr_e( 'Forms', 'convertkit' ); ?>" id="ckwc-forms" data-option-value-prefix="form_">
		<?php
		if ( $forms->exist() ) {
			foreach ( $forms->get() as $form ) {
				echo sprintf(
					'<option value="%s"%s>%s [%s]</option>',
					esc_attr( $form['id'] ),
					selected( $form['id'], $value, false ),
					esc_attr( $form['name'] ),
					( ! empty( $form['format'] ) ? esc_attr( $form['format'] ) : 'inline' )
				);
			}
		}
		?>
	</optgroup>
</select>
