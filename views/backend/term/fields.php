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
		<label for="description"><?php _e( 'ConvertKit Form', 'convertkit' ); ?></label>
	</th>
	<td>
		<?php
		if ( ! $forms->exist() ) {
			esc_html_e( 'No Forms exist in ConvertKit.', 'convertkit' );
		} else {
			?>
			<select name="wp-convertkit[form]" id="wp-convertkit-form">
				<option value="0"<?php selected( 0, $convertkit_term->get_form() ); ?>>
					<?php esc_html_e( 'None', 'convertkit' ); ?>
				</option>
				<?php 
				foreach ( $forms->get() as $form ) { 
					?>
					<option value="<?php echo $form['id']; ?>"<?php selected( $form['id'], $convertkit_term->get_form() ); ?>>
						<?php echo $form['name']; ?>
					</option>
					<?php
				}
				?>
			</select>
			<p class="description">
				<?php _e( '<code>None</code>: do not display a form.', 'convertkit' ); ?>
				<br />
				<?php 
				echo sprintf(
					__( 'Any other option will display that form after the main content for Posts in this %s.', 'convertkit' ),
					'category'
				);
				?>
			</p>
			<?php
		}
		?>
	</td>
</tr>