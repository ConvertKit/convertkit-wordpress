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
<select class="<?php echo esc_attr( $css_class ); ?>" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>">
	<option <?php selected( '', $value ); ?> value="" data-preserve-on-refresh="1">
		<?php esc_html_e( '(Do not subscribe)', 'convertkit' ); ?>
	</option>

	<option <?php selected( 'subscribe', $value ); ?> value="subscribe" data-preserve-on-refresh="1">
		<?php esc_html_e( 'Subscribe', 'convertkit' ); ?>
	</option>

	<optgroup label="<?php esc_attr_e( 'Forms', 'convertkit' ); ?>" id="ckwc-forms" data-option-value-prefix="form:">
		<?php
		if ( $forms->exist() ) {
			foreach ( $forms->get() as $form ) {
				printf(
					'<option value="%s"%s>%s [%s]</option>',
					esc_attr( 'form:' . $form['id'] ),
					selected( 'form:' . $form['id'], $value, false ),
					esc_attr( $form['name'] ),
					( ! empty( $form['format'] ) ? esc_attr( $form['format'] ) : 'inline' )
				);
			}
		}
		?>
	</optgroup>
</select>
