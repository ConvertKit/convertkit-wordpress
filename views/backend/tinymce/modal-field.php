<?php
/**
 * TinyMCE Modal Field view
 *
 * @package ConvertKit
 * @author ConvertKit
 */

switch ( $field['type'] ) {

	/**
	 * Text
	 */
	case 'text':
		?>
		<input type="text" 
				id="tinymce_modal_<?php echo esc_attr( $field_name ); ?>"
				name="<?php echo esc_attr( $field_name ); ?>"
				value="<?php echo esc_attr( isset( $shortcode['attributes'][ $field_name ]['default'] ) ? $shortcode['attributes'][ $field_name ]['default'] : '' ); ?>" 
				data-shortcode="<?php echo esc_attr( $field_name ); ?>"
				placeholder="<?php echo esc_attr( isset( $field['placeholder'] ) ? $field['placeholder'] : '' ); ?>"
				class="widefat" />
		<?php
		break;

	/**
	 * Number
	 */
	case 'number':
		?>
		<input type="number" 
				id="tinymce_modal_<?php echo esc_attr( $field_name ); ?>"
				name="<?php echo esc_attr( $field_name ); ?>" 
				value="<?php echo esc_attr( isset( $shortcode['attributes'][ $field_name ]['default'] ) ? $shortcode['attributes'][ $field_name ]['default'] : '' ); ?>" 
				data-shortcode="<?php echo esc_attr( $field_name ); ?>"
				min="<?php echo esc_attr( $field['min'] ); ?>" 
				max="<?php echo esc_attr( $field['max'] ); ?>" 
				step="<?php echo esc_attr( $field['step'] ); ?>"
				class="widefat" />
		<?php
		break;

	/**
	 * Select
	 */
	case 'select':
		?>
		<select name="<?php echo esc_attr( $field_name ); ?>"
				id="tinymce_modal_<?php echo esc_attr( $field_name ); ?>"
				data-shortcode="<?php echo esc_attr( $field_name ); ?>"
				size="1"
				class="widefat">
			<?php
			$field['default_value'] = ( isset( $shortcode['attributes'][ $field_name ]['default'] ) ? $shortcode['attributes'][ $field_name ]['default'] : '' );
			foreach ( $field['values'] as $value => $label ) {
				?>
				<option value="<?php echo esc_attr( $value ); ?>"<?php selected( $field['default_value'], $value ); ?>>
					<?php echo esc_attr( $label ); ?>
				</option>
				<?php
			}
			?>
		</select>
		<?php
		break;

	/**
	 * Toggle
	 */
	case 'toggle':
		?>
		<select name="<?php echo esc_attr( $field_name ); ?>"
				id="tinymce_modal_<?php echo esc_attr( $field_name ); ?>"
				data-shortcode="<?php echo esc_attr( $field_name ); ?>"
				size="1"
				class="widefat">
			<?php
			$field['default_value'] = ( isset( $shortcode['attributes'][ $field_name ]['default'] ) ? $shortcode['attributes'][ $field_name ]['default'] : '' );
			?>
			<option value="0"<?php selected( $field['default_value'], 0 ); ?>><?php esc_html_e( 'No', 'convertkit' ); ?></option>
			<option value="1"<?php selected( $field['default_value'], 1 ); ?>><?php esc_html_e( 'Yes', 'convertkit' ); ?></option>
		</select>
		<?php
		break;

	/**
	 * Color Picker
	 */
	case 'color':
		?>
		<input type="text" 
				id="tinymce_modal_<?php echo esc_attr( $field_name ); ?>"
				name="<?php echo esc_attr( $field_name ); ?>"
				value="<?php echo esc_attr( isset( $shortcode['attributes'][ $field_name ]['default'] ) ? $shortcode['attributes'][ $field_name ]['default'] : '' ); ?>" 
				data-shortcode="<?php echo esc_attr( $field_name ); ?>"
				placeholder="<?php echo esc_attr( isset( $field['placeholder'] ) ? $field['placeholder'] : '' ); ?>"
				class="widefat convertkit-color-picker" />
		<?php
		break;
}

if ( isset( $field['description'] ) ) {
	?>
	<p class="description">
		<?php echo esc_attr( $field['description'] ); ?>
	</p>
	<?php
}
