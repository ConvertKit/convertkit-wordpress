<?php
/**
 * MetaBox template
 *
 * @package ConvertKit
 * @author ConvertKit
 */
?>
<table class="form-table">
	<tbody>
		<!-- Form -->
		<tr valign="top">
			<th scope="row">
				<label for="wp-convertkit-form"><?php esc_html_e( 'Form', 'convertkit' ); ?></label>
			</th>
			<td>
				<?php
				if ( ! $forms->exist() ) {
					esc_html_e( 'No Forms exist in ConvertKit.', 'convertkit' );
				} else {
					?>
					<select name="wp-convertkit[form]" id="wp-convertkit-form">
						<option value="-1"<?php selected( - 1, $convertkit_post->get_form() ); ?>>
							<?php esc_html_e( 'Default', 'convertkit' ); ?>
						</option>
						<option value="0"<?php selected( 0, $convertkit_post->get_form() ); ?>>
							<?php esc_html_e( 'None', 'convertkit' ); ?>
						</option>
						<?php 
						foreach ( $forms->get() as $form ) { 
							?>
							<option value="<?php echo $form['id']; ?>"<?php selected( $form['id'], $convertkit_post->get_form() ); ?>>
								<?php echo $form['name']; ?>
							</option>
							<?php
						}
						?>
					</select>
					<p class="description">
						<?php
						/* translators: settings url */
						printf( 
							__( '<code>Default</code>: Uses the form specified on the <a href="%s" target="_blank">settings page</a>.', 'convertkit' ), 
							esc_attr( esc_url( $settings_link ) )
						);
						?>
						<br />
						<?php _e( '<code>None</code>: do not display a form.', 'convertkit' ); ?>
						<br />
						<?php _e( 'Any other option will display that form after the main content.', 'convertkit' ); ?>
					</p>
					<?php
				}
				?>

				<p class="description">
					<?php
					echo sprintf(
						esc_html__( 'To make changes to your forms, %s', 'convertkit' ),
						'<a href="https://app.convertkit.com/" target="_blank">' . esc_html__( 'sign in to ConvertKit','convertkit' ) . '</a>'
					);
					?>
				</p>
			</td>
		</tr>
		
		<!-- Landing Page -->
		<?php 
		if ( 'page' === $post->post_type ) { 
			?>
			<tr valign="top">
				<th scope="row">
					<label for="wp-convertkit-landing_page"><?php esc_html_e( 'Landing Page', 'convertkit' ); ?></label>
				</th>
				<td>
					<?php
					if ( ! $landing_pages->exist() ) {
						esc_html_e( 'No Landing Pages exist in ConvertKit.', 'convertkit' );
					} else {
						?>
						<select name="wp-convertkit[landing_page]" id="wp-convertkit-landing_page">
							<option <?php selected( '', $convertkit_post->get_landing_page() ); ?> value="0">
								<?php _e( 'None', 'convertkit' ); ?>
							</option>
							<?php 
							foreach ( $landing_pages->get() as $landing_page ) {
								$name = sanitize_text_field( $landing_page['name'] );
								if ( isset( $landing_page['url'] ) ) {
									?>
									<option value="<?php echo esc_attr( $landing_page['url'] ); ?>"<?php selected( $landing_page['url'], $convertkit_post->get_landing_page() ); ?>>
										<?php echo esc_attr( $name ); ?>
									</option>
									<?php
								} else {
									?>
									<option value="<?php echo esc_attr( $landing_page['id'] ); ?>"<?php selected( $landing_page['id'], $convertkit_post->get_landing_page() ); ?>>
										<?php echo esc_attr( $name ); ?>
									</option>
									<?php
								}
							}
							?>
						</select>
						<p class="description">
							<?php esc_html_e( 'Select a landing page to make it appear in place of this page.', 'convertkit' ); ?>
						</p>
						<?php
					}
					?>

					<p class="description">
						<?php
						echo sprintf(
							esc_html__( 'To make changes to your landing pages, %s', 'convertkit' ),
							'<a href="https://app.convertkit.com/" target="_blank">' . esc_html__( 'sign in to ConvertKit','convertkit' ) . '</a>'
						);
						?>
					</p>
				</td>
			</tr>
			<?php
		}
		?>

		<!-- Tag -->
		<tr valign="top">
			<th scope="row">
				<label for="wp-convertkit-tag"><?php esc_html_e( 'Add a Tag', 'convertkit' ); ?></label>
			</th>
			<td>
				<?php
				if ( ! $tags->exist() ) {
					esc_html_e( 'No Tags exist in ConvertKit.', 'convertkit' );
				} else {
					?>
					<select name="wp-convertkit[tag]" id="wp-convertkit-tag">
						<option value="0"<?php selected( '', $convertkit_post->get_tag() ); ?>>
							<?php _e( 'None', 'convertkit' ); ?>
						</option>
						<?php
						foreach ( $tags->get() as $tag ) {
							$name = sanitize_text_field( $tag['name'] );
							?>
							<option value="<?php echo esc_attr( $tag['id'] ); ?>"<?php selected( $tag['id'], $convertkit_post->get_tag() ); ?>>
								<?php echo esc_attr( $name ); ?>
							</option>
							<?php
						}
						?>
					</select>
					<?php
				}
				?>
				<p class="description">
					<?php esc_html_e( 'Select a tag to apply to visitors of this page who are subscribed.', 'convertkit' ); ?>
					<br />
					<?php esc_html_e( 'A visitor is deemed to be subscribed if they have clicked a link in an email to this site which includes their subscriber ID, or have entered their email address in a ConvertKit Form on this site.', 'convertkit' ); ?>
				</p>
			</td>
		</tr>
	</tbody>
</table>

<?php
wp_nonce_field( 'wp-convertkit-save-meta', 'wp-convertkit-save-meta-nonce' );