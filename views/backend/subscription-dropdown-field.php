<?php
/**
 * Outputs a dropdown field comprising of options covering:
 * - Do not subscribe
 * - Subscribe
 * - Forms
 * - Tags
 *
 * @package ConvertKit
 * @author ConvertKit
 */

?>
<select class="<?php echo esc_attr( $class ); ?>" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>">
	<?php
	// If Bulk Edit is true, add a No Change option and select it.
	if ( $is_bulk_edit === true ) {
		?>
		<option value="-1" data-preserve-on-refresh="1"<?php selected( '', $name ); ?>><?php esc_html_e( '— No Change —', 'convertkit' ); ?></option>
		<?php
	}
	?>

	<option <?php selected( '', $name ); ?> value="" data-preserve-on-refresh="1">
		<?php esc_html_e( '(Do not subscribe)', 'convertkit' ); ?>
	</option>

	<option <?php selected( '', $name ); ?> value="" data-preserve-on-refresh="1">
		<?php esc_html_e( 'Subscribe', 'convertkit' ); ?>
	</option>

	<optgroup label="<?php esc_attr_e( 'Forms', 'convertkit' ); ?>" id="ckwc-forms" data-option-value-prefix="form_">
		<?php
		if ( $forms->exist() ) {
			foreach ( $forms->get() as $form ) {
				?>
				<option value="form_<?php echo esc_attr( $form['id'] ); ?>"<?php selected( 'form_' . esc_attr( $form['id'] ), $name ); ?>><?php echo esc_html( $form['name'] ); ?></option>
				<?php
			}
		}
		?>
	</optgroup>

	<optgroup label="<?php esc_attr_e( 'Tags', 'convertkit' ); ?>" id="ckwc-tags" data-option-value-prefix="tag_">
		<?php
		if ( $tags->exist() ) {
			foreach ( $tags->get() as $convertkit_tag ) {
				?>
				<option value="tag_<?php echo esc_attr( $convertkit_tag['id'] ); ?>"<?php selected( 'tag_' . esc_attr( $convertkit_tag['id'] ), $name ); ?>><?php echo esc_html( $convertkit_tag['name'] ); ?></option>
				<?php
			}
		}
		?>
	</optgroup>
</select>
