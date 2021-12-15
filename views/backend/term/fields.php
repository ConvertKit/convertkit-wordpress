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
		<label for="description"><?php esc_html_e( 'ConvertKit Form', 'convertkit' ); ?></label>
	</th>
	<td>
		<?php
		if ( ! $convertkit_forms->exist() ) {
			esc_html_e( 'No Forms exist in ConvertKit.', 'convertkit' );
		} else {
			?>
			<select name="wp-convertkit[form]" id="wp-convertkit-form">
				<option value="0"<?php selected( 0, $convertkit_term->get_form() ); ?>>
					<?php esc_html_e( 'None', 'convertkit' ); ?>
				</option>
				<?php
				foreach ( $convertkit_forms->get() as $convertkit_form ) {
					?>
					<option value="<?php echo esc_attr( $convertkit_form['id'] ); ?>"<?php selected( $convertkit_form['id'], $convertkit_term->get_form() ); ?>>
						<?php echo esc_html( $convertkit_form['name'] ); ?>
					</option>
					<?php
				}
				?>
			</select>
			<p class="description">
				<?php _e( '<code>None</code>: do not display a form.', 'convertkit' ); /* phpcs:ignore */ ?>
				<br />
				<?php
				echo sprintf(
					/* translators: Taxonomy Name */
					esc_html__( 'Any other option will display that form after the main content for Posts in this %s.', 'convertkit' ),
					'category'
				);
				?>
			</p>
			<?php
			wp_nonce_field( 'wp-convertkit-save-meta', 'wp-convertkit-save-meta-nonce' );
		}
		?>
	</td>
</tr>
