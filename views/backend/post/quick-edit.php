<?php
/**
 * Quick Edit view
 *
 * @package ConvertKit
 * @author ConvertKit
 */

?>
<div id="convertkit-quick-edit" style="display:none;">
	<!-- Form -->
	<label for="wp-convertkit-form">
		<span class="title"><?php esc_html_e( 'Form', 'convertkit' ); ?></span>
		<select name="wp-convertkit[form]" id="wp-convertkit-quick-edit-form" size="1">
			<option value="-1" data-preserve-on-refresh="1"><?php esc_html_e( 'Default', 'convertkit' ); ?></option>
			<option value="0" data-preserve-on-refresh="1"><?php esc_html_e( 'None', 'convertkit' ); ?></option>
			<?php
			if ( $convertkit_forms->exist() ) {
				foreach ( $convertkit_forms->get() as $form ) {
					?>
					<option value="<?php echo esc_attr( $form['id'] ); ?>"><?php echo esc_attr( $form['name'] ); ?></option>
					<?php
				}
			}
			?>
		</select>
		<button class="wp-convertkit-refresh-resources" class="button button-secondary" title="<?php esc_attr_e( 'Refresh Forms from ConvertKit account', 'convertkit' ); ?>" data-resource="forms" data-field="#wp-convertkit-quick-edit-form">
			<span class="dashicons dashicons-update"></span>
		</button>
	</label>

	<!-- Tag -->
	<label for="wp-convertkit-tag">
		<span class="title"><?php esc_html_e( 'Tag', 'convertkit' ); ?></span>
		<select name="wp-convertkit[tag]" id="wp-convertkit-quick-edit-tag" size="1">
			<option value="0" data-preserve-on-refresh="1">
				<?php esc_html_e( 'None', 'convertkit' ); ?>
			</option>
			<?php
			if ( $convertkit_tags->exist() ) {
				foreach ( $convertkit_tags->get() as $convertkit_tag ) {
					?>
					<option value="<?php echo esc_attr( $convertkit_tag['id'] ); ?>"><?php echo esc_attr( $convertkit_tag['name'] ); ?></option>
					<?php
				}
			}
			?>
		</select>
		<button class="wp-convertkit-refresh-resources" class="button button-secondary" title="<?php esc_attr_e( 'Refresh Tags from ConvertKit account', 'convertkit' ); ?>" data-resource="tags" data-field="#wp-convertkit-quick-edit-tag">
			<span class="dashicons dashicons-update"></span>
		</button>
	</label>
	<?php wp_nonce_field( 'wp-convertkit-save-meta', 'wp-convertkit-save-meta-nonce' ); ?>
</div>

