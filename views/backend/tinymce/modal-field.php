<?php
/**
 * TinyMCE Modal Field view
 *
 * @package ConvertKit
 * @author ConvertKit
 */

// Build a string of data- attributes.
$data_attributes                   = '';
$data_attributes_shortcode_defined = false;
if ( isset( $field['data'] ) ) {
	foreach ( $field['data'] as $data_attribute => $data_attribute_value ) {
		$data_attributes .= ' data-' . $data_attribute . '="' . $data_attribute_value . '"';

		if ( $data_attribute === 'shortcode' ) {
			$data_attributes_shortcode_defined = true;
		}
	}
}
if ( ! $data_attributes_shortcode_defined ) {
	$data_attributes .= ' data-shortcode="' . $field_name . '"';
}

switch ( $field['type'] ) {

	/**
	 * Text
	 */
	case 'text':
	case 'text_multiple':
		?>
		<input type="text" 
				id="tinymce_modal_<?php echo esc_attr( $field_name ); ?>"
				name="<?php echo esc_attr( $field_name ); ?>"
				value="<?php echo esc_attr( isset( $shortcode['attributes'][ $field_name ]['default'] ) ? $shortcode['attributes'][ $field_name ]['default'] : '' ); ?>" 
				<?php echo $data_attributes; // phpcs:ignore ?>
				placeholder="<?php echo esc_attr( isset( $field['placeholder'] ) ? $field['placeholder'] : '' ); ?>"
				class="widefat <?php echo esc_attr( isset( $field['class'] ) ? $field['class'] : '' ); ?>" />
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
				<?php echo $data_attributes; // phpcs:ignore ?>
				min="<?php echo esc_attr( $field['min'] ); ?>" 
				max="<?php echo esc_attr( $field['max'] ); ?>" 
				step="<?php echo esc_attr( $field['step'] ); ?>"
				class="widefat <?php echo esc_attr( isset( $field['class'] ) ? $field['class'] : '' ); ?>" />
		<?php
		break;

	/**
	 * Select
	 */
	case 'select':
		?>
		<select name="<?php echo esc_attr( $field_name ); ?>"
				id="tinymce_modal_<?php echo esc_attr( $field_name ); ?>"
				<?php echo $data_attributes; // phpcs:ignore ?>
				size="1"
				class="widefat <?php echo esc_attr( isset( $field['class'] ) ? $field['class'] : '' ); ?>">
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
	 * Multiple Select
	 */
	case 'select_multiple':
		?>
		<select name="<?php echo esc_attr( $field_name ); ?>[]"
				id="tinymce_modal_<?php echo esc_attr( $field_name ); ?>"
				<?php echo $data_attributes; // phpcs:ignore ?>
				size="1"
				multiple="multiple"
				class="widefat <?php echo esc_attr( isset( $field['class'] ) ? $field['class'] : '' ); ?>">
			<?php
			$field['default_value'] = ( isset( $shortcode['attributes'][ $field_name ]['default'] ) ? $shortcode['attributes'][ $field_name ]['default'] : '' );
			if ( isset( $field['values'] ) && is_array( $field['values'] ) && count( $field['values'] ) > 0 ) {
				foreach ( $field['values'] as $value => $label ) {
					?>
					<option value="<?php echo esc_attr( $value ); ?>"<?php selected( $field['default_value'], $value ); ?>>
						<?php echo esc_attr( $label ); ?>
					</option>
					<?php
				}
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
				<?php echo $data_attributes; // phpcs:ignore ?>
				size="1"
				class="widefat <?php echo esc_attr( isset( $field['class'] ) ? $field['class'] : '' ); ?>">
			<?php
			$field['default_value'] = ( isset( $shortcode['attributes'][ $field_name ]['default'] ) ? $shortcode['attributes'][ $field_name ]['default'] : '' );
			?>
			<option value="0"<?php selected( $field['default_value'], 0 ); ?>><?php esc_html_e( 'No', 'convertkit' ); ?></option>
			<option value="1"<?php selected( $field['default_value'], 1 ); ?>><?php esc_html_e( 'Yes', 'convertkit' ); ?></option>
		</select>
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
