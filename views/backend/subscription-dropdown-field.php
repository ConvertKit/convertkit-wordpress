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

	<?php
	// Output additional options, if defined.
	if ( is_array( $additional_options ) ) {
		foreach ( $additional_options as $additional_option_key => $additional_option_value ) {
			printf(
				'<option value="%s"%s>%s</option>',
				esc_attr( $additional_option_key ),
				selected( $additional_option_key, $value, false ),
				esc_attr( $additional_option_value )
			);
		}
	}
	?>

	<optgroup label="<?php esc_attr_e( 'Forms', 'convertkit' ); ?>" id="convertkit-forms" data-option-value-prefix="form:">
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

	<optgroup label="<?php esc_attr_e( 'Sequences', 'convertkit' ); ?>" id="convertkit-sequences" data-option-value-prefix="sequence:">
		<?php
		if ( $sequences->exist() ) {
			foreach ( $sequences->get() as $sequence ) {
				printf(
					'<option value="%s"%s>%s</option>',
					esc_attr( 'sequence:' . $sequence['id'] ),
					selected( 'sequence:' . $sequence['id'], $value, false ),
					esc_attr( $sequence['name'] )
				);
			}
		}
		?>
	</optgroup>

	<optgroup label="<?php esc_attr_e( 'Tags', 'convertkit' ); ?>" id="convertkit-tags" data-option-value-prefix="tag:">
		<?php
		if ( $tags->exist() ) {
			foreach ( $tags->get() as $convertkit_tag ) {
				printf(
					'<option value="%s"%s>%s</option>',
					esc_attr( 'tag:' . $convertkit_tag['id'] ),
					selected( 'tag:' . $convertkit_tag['id'], $value, false ),
					esc_attr( $convertkit_tag['name'] )
				);
			}
		}
		?>
	</optgroup>
</select>
