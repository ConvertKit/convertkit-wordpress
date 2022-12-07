<?php
/**
 * Add Term Fields template
 *
 * @package ConvertKit
 * @author ConvertKit
 */

?>
<div class="form-field term-description-wrap">
	<label for="tag-description"><?php esc_html_e( 'ConvertKit Form', 'convertkit' ); ?></label>

	<div class="convertkit-select2-container convertkit-select2-container-grid">
		<select name="wp-convertkit[form]" id="wp-convertkit-form" class="convertkit-select2">
			<option value="0" data-preserve-on-refresh="1" selected>
				<?php esc_html_e( 'Default', 'convertkit' ); ?>
			</option>
			<?php
			if ( $convertkit_forms->exist() ) {
				foreach ( $convertkit_forms->get() as $convertkit_form ) {
					?>
					<option value="<?php echo esc_attr( $convertkit_form['id'] ); ?>">
						<?php echo esc_html( $convertkit_form['name'] ); ?>
					</option>
					<?php
				}
			}
			?>
		</select>
		<button class="wp-convertkit-refresh-resources" class="button button-secondary" title="<?php esc_attr_e( 'Refresh Forms from ConvertKit account', 'convertkit' ); ?>" data-resource="forms" data-field="#wp-convertkit-form">
			<span class="dashicons dashicons-update"></span>
		</button>
		<p class="description">
			<?php _e( '<code>Default</code>: Display a form based on the Post\'s settings.', 'convertkit' ); /* phpcs:ignore */ ?>
			<br />
			<?php
			echo sprintf(
				/* translators: Taxonomy Name */
				esc_html__( 'Any other option will display that form after the main content for Posts assigned to this %s.', 'convertkit' ),
				'category'
			);
			?>
		</p>
	</div>

	<?php
	wp_nonce_field( 'wp-convertkit-save-meta', 'wp-convertkit-save-meta-nonce' );
	?>
</div>
