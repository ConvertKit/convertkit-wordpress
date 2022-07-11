<?php
/**
 * Quick Edit view
 *
 * @package ConvertKit
 * @author ConvertKit
 */

?>
<div class="convertkit-quick-edit" style="display:none;">
	<!-- Form -->
	<label for="wp-convertkit-form">
		<span class="title"><?php esc_html_e( 'Form', 'convertkit' ); ?></span>
		<select name="wp-convertkit[form]" id="wp-convertkit-form" size="1">
			<option value="-1"><?php esc_html_e( 'Default', 'convertkit' ); ?></option>
			<option value="0"><?php esc_html_e( 'None', 'convertkit' ); ?></option>
			<?php
			foreach ( $convertkit_forms->get() as $form ) {
				?>
				<option value="<?php echo esc_attr( $form['id'] ); ?>"><?php echo esc_attr( $form['name'] ); ?></option>
				<?php
			}
			?>
		</select>
	</label>

	<!-- Tag -->
	<label for="wp-convertkit-tag">
		<span class="title"><?php esc_html_e( 'Tag', 'convertkit' ); ?></span>
		<select name="wp-convertkit[tag]" id="wp-convertkit-tag" size="1">
			<option value="0">
				<?php esc_html_e( 'None', 'convertkit' ); ?>
			</option>
			<?php
			foreach ( $convertkit_tags->get() as $convertkit_tag ) {
				?>
				<option value="<?php echo esc_attr( $convertkit_tag['id'] ); ?>"><?php echo esc_attr( $convertkit_tag['name'] ); ?></option>
				<?php
			}
			?>
		</select>
	</label>
	<?php wp_nonce_field( 'wp-convertkit-save-meta', 'wp-convertkit-save-meta-nonce' ); ?>
</div>

