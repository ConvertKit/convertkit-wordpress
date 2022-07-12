<?php
/**
 * Bulk Edit view
 *
 * @package ConvertKit
 * @author ConvertKit
 */

?>
<div id="convertkit-bulk-edit" style="display:none;">
	<!-- Form -->
	<label for="wp-convertkit-form">
		<span class="title"><?php esc_html_e( 'Form', 'convertkit' ); ?></span>
		<select name="wp-convertkit[form]" id="wp-convertkit-form" size="1">
			<?php
			// For Bulk Edit, the 'No Change' value is -1. However, because this Plugin has historically used -1
			// to mean that the Default form for the Post Type should be displayed, using -1 as 'No Change' in Bulk Edit
			// would result in Posts/Pages having (or not having) the Form setting updated, when the user may/may not
			// have selected the 'Default' option.
			// Therefore, we use -2 to denote 'No Change'.
			?>
			<option value="-2"><?php esc_html_e( '— No Change —', 'convertkit' ); ?></option>
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
			<?php
			// For Bulk Edit, the 'No Change' value is -1. However, because this Plugin has historically used -1
			// to mean that the Default form for the Post Type should be displayed, using -1 as 'No Change' in Bulk Edit
			// would result in Posts/Pages having (or not having) the Form setting updated, when the user may/may not
			// have selected the 'Default' option.
			// Therefore, we use -2 to denote 'No Change', even though this setting is for the Tag, so we're at least consistent.
			?>
			<option value="-2"><?php esc_html_e( '— No Change —', 'convertkit' ); ?></option>
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

