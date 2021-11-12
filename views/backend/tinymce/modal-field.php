<?php
// Build a string of data- attributes
$data_attributes = '';
$data_attributes_shortcode_defined = false;
if ( isset( $field['data'] ) ) {
	foreach ( $field['data'] as $data_attribute => $data_attribute_value ) {
		$data_attributes .= ' data-' . $data_attribute . '="' . $data_attribute_value . '"';

		if ( $data_attribute == 'shortcode' ) {
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
				id="tinymce_modal_<?php echo $field_name; ?>"
				name="<?php echo $field_name; ?>"
				value="<?php echo ( isset( $field['default_value'] ) ? $field['default_value'] : '' ); ?>" 
				<?php echo $data_attributes; ?>
				placeholder="<?php echo ( isset( $field['placeholder'] ) ? $field['placeholder'] : '' ); ?>"
				class="widefat <?php echo ( isset( $field['class'] ) ? $field['class'] : '' ); ?>" />
		<?php
		break;

	/**
	 * Number
	 */
	case 'number':
		?>
		<input type="number" 
				id="tinymce_modal_<?php echo $field_name; ?>"
				name="<?php echo $field_name; ?>" 
				value="<?php echo ( isset( $field['default_value'] ) ? $field['default_value'] : '' ); ?>" 
				<?php echo $data_attributes; ?>
				min="<?php echo $field['min']; ?>" 
				max="<?php echo $field['max']; ?>" 
				step="<?php echo $field['step']; ?>"
				class="widefat <?php echo ( isset( $field['class'] ) ? $field['class'] : '' ); ?>" />
		<?php
		break;

	/**
	 * Select
	 */
	case 'select':
		?>
		<select name="<?php echo $field_name; ?>"
				id="tinymce_modal_<?php echo $field_name; ?>"
				<?php echo $data_attributes; ?>
				size="1"
				class="widefat <?php echo ( isset( $field['class'] ) ? $field['class'] : '' ); ?>">
			<?php
			$field['default_value'] = ( isset( $field['default_value'] ) ? $field['default_value'] : '' );
			foreach ( $field['values'] as $value => $label ) {
				?>
				<option value="<?php echo $value; ?>"<?php selected( $field['default_value'], $value ); ?>>
					<?php echo $label; ?>
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
		<select name="<?php echo $field_name; ?>[]"
				id="tinymce_modal_<?php echo $field_name; ?>"
				<?php echo $data_attributes; ?>
				size="1"
				multiple="multiple"
				class="widefat <?php echo ( isset( $field['class'] ) ? $field['class'] : '' ); ?>">
			<?php
			$field['default_value'] = ( isset( $field['default_value'] ) ? $field['default_value'] : '' );
			if ( isset( $field['values'] ) && is_array( $field['values'] ) && count( $field['values'] ) > 0 ) {
				foreach ( $field['values'] as $value => $label ) {
					?>
					<option value="<?php echo $value; ?>"<?php selected( $field['default_value'], $value ); ?>>
						<?php echo $label; ?>
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
		<select name="<?php echo $field_name; ?>"
				id="tinymce_modal_<?php echo $field_name; ?>"
				<?php echo $data_attributes; ?>
				size="1"
				class="widefat <?php echo ( isset( $field['class'] ) ? $field['class'] : '' ); ?>">
			<?php
			$field['default_value'] = ( isset( $field['default_value'] ) ? $field['default_value'] : '' );
			?>
			<option value="0"<?php selected( $field['default_value'], 0 ); ?>><?php _e( 'No', 'convertkit' ); ?></option>
			<option value="1"<?php selected( $field['default_value'], 1 ); ?>><?php _e( 'Yes', 'convertkit' ); ?></option>
		</select>
		<?php
		break;
}

if ( isset( $field['description'] ) ) {
	?>
	<p class="description">
		<?php echo $field['description']; ?>
	</p>
	<?php
}