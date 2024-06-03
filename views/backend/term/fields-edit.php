<?php
/**
 * Edit Term Fields template
 *
 * @package ConvertKit
 * @author ConvertKit
 */

?>
<tr class="form-field">
	<th scope="row">
		<label for="wp-convertkit-form"><?php esc_html_e( 'ConvertKit Form', 'convertkit' ); ?></label>
	</th>
	<td>
		<div class="convertkit-select2-container convertkit-select2-container-grid">
			<?php
			echo $convertkit_forms->get_select_field_all( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				'wp-convertkit[form]',
				'wp-convertkit-form',
				array(
					'convertkit-select2',
				),
				esc_attr( $convertkit_term->get_form() ),
				array(
					'0' => esc_html__( 'Default', 'convertkit' ),
				)
			);
			?>

			<button class="wp-convertkit-refresh-resources" class="button button-secondary" title="<?php esc_attr_e( 'Refresh Forms from ConvertKit account', 'convertkit' ); ?>" data-resource="forms" data-field="#wp-convertkit-form">
				<span class="dashicons dashicons-update"></span>
			</button>
			<p class="description">
				<code><?php esc_html_e( 'Default', 'convertkit' ); ?></code>
				<?php esc_html_e( ': Display a form based on the Post\'s settings.', 'convertkit' ); ?>
				<br />
				<?php
				printf(
					/* translators: Taxonomy Name */
					esc_html__( 'Any other option will display that form after the main content for Posts assigned to this %s.', 'convertkit' ),
					'category'
				);
				?>
			</p>
		</div>
	</td>
</tr>
<tr class="form-field">
	<th scope="row">
		<label for="wp-convertkit-form-position"><?php esc_html_e( 'Display ConvertKit Form on Archive?', 'convertkit' ); ?></label>
	</th>
	<td>
		<select name="wp-convertkit[form_position]" id="wp-convertkit-form-position" size="1">
			<option value=""><?php esc_attr_e( 'No', 'convertkit' ); ?></option>
			<option value="before"<?php selected( 'before', $convertkit_term->get_form_position() ); ?>><?php esc_attr_e( 'Before Posts', 'convertkit' ); ?></option>
			<option value="after"<?php selected( 'after', $convertkit_term->get_form_position() ); ?>><?php esc_attr_e( 'After Posts', 'convertkit' ); ?></option>
		</select>
		<p class="description">
			<?php
			esc_html_e( 'Whether to display the Form on this Category\'s archive page, above or below the main posts list.', 'convertkit' );
			?>
		</p>
	
		<?php
		wp_nonce_field( 'wp-convertkit-save-meta', 'wp-convertkit-save-meta-nonce' );
		?>
	</td>
</tr>
