<?php
/**
 * Metabox view
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
				<div class="convertkit-select2-container convertkit-select2-container-grid">
					<select name="wp-convertkit[form]" id="wp-convertkit-form" class="convertkit-select2 widefat">
						<option value="-1"<?php selected( - 1, $convertkit_post->get_form() ); ?> data-preserve-on-refresh="1">
							<?php esc_html_e( 'Default', 'convertkit' ); ?>
						</option>
						<option value="0"<?php selected( 0, $convertkit_post->get_form() ); ?> data-preserve-on-refresh="1">
							<?php esc_html_e( 'None', 'convertkit' ); ?>
						</option>
						<?php
						if ( $convertkit_forms->exist() ) {
							foreach ( $convertkit_forms->get() as $form ) {
								?>
								<option value="<?php echo esc_attr( $form['id'] ); ?>"<?php selected( $form['id'], $convertkit_post->get_form() ); ?>>
									<?php echo esc_attr( $form['name'] ); ?>
								</option>
								<?php
							}
						}
						?>
					</select>
					<button class="wp-convertkit-refresh-resources" class="button button-secondary hide-if-no-js" title="<?php esc_attr_e( 'Refresh Forms from ConvertKit account', 'convertkit' ); ?>" data-resource="forms" data-field="#wp-convertkit-form">
						<span class="dashicons dashicons-update"></span>
					</button>
					<p class="description">
						<?php
						printf(
							/* translators: settings url */
							__( '<code>Default</code>: Uses the form specified on the <a href="%s" target="_blank">settings page</a>.', 'convertkit' ), /* phpcs:ignore */
							esc_attr( esc_url( $settings_link ) )
						);
						?>
						<br />
						<?php _e( '<code>None</code>: do not display a form.', 'convertkit' ); /* phpcs:ignore */ ?>
						<br />
						<?php esc_html_e( 'Any other option will display that form after the main content.', 'convertkit' ); ?>
						<br />
						<?php
						echo sprintf(
							/* translators: Link to sign in to ConvertKit */
							esc_html__( 'To make changes to your forms, %s', 'convertkit' ),
							'<a href="' . esc_url( convertkit_get_sign_in_url() ) . '" target="_blank">' . esc_html__( 'sign in to ConvertKit', 'convertkit' ) . '</a>'
						);
						?>
					</p>
				</div>
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
					<div class="convertkit-select2-container convertkit-select2-container-grid">
						<select name="wp-convertkit[landing_page]" id="wp-convertkit-landing_page" class="convertkit-select2">
							<option <?php selected( '', $convertkit_post->get_landing_page() ); ?> value="0" data-preserve-on-refresh="1">
								<?php esc_html_e( 'None', 'convertkit' ); ?>
							</option>
							<?php
							if ( $convertkit_landing_pages->exist() ) {
								foreach ( $convertkit_landing_pages->get() as $landing_page ) {
									if ( isset( $convertkit_landing_page['url'] ) ) {
										?>
										<option value="<?php echo esc_attr( $landing_page['url'] ); ?>"<?php selected( $landing_page['url'], $convertkit_post->get_landing_page() ); ?>>
											<?php echo esc_attr( $landing_page['name'] ); ?>
										</option>
										<?php
									} else {
										?>
										<option value="<?php echo esc_attr( $landing_page['id'] ); ?>"<?php selected( $landing_page['id'], $convertkit_post->get_landing_page() ); ?>>
											<?php echo esc_attr( $landing_page['name'] ); ?>
										</option>
										<?php
									}
								}
							}
							?>
						</select>
						<button class="wp-convertkit-refresh-resources" class="button button-secondary hide-if-no-js" title="<?php esc_attr_e( 'Refresh Landing Pages from ConvertKit account', 'convertkit' ); ?>" data-resource="landing_pages" data-field="#wp-convertkit-landing_page">
							<span class="dashicons dashicons-update"></span>
						</button>
						<p class="description">
							<?php esc_html_e( 'Select a landing page to make it appear in place of this page.', 'convertkit' ); ?>
							<br />
							<?php
							echo sprintf(
								/* translators: Link to sign in to ConvertKit */
								esc_html__( 'To make changes to your landing pages, %s', 'convertkit' ),
								'<a href="' . esc_url( convertkit_get_sign_in_url() ) . '" target="_blank">' . esc_html__( 'sign in to ConvertKit', 'convertkit' ) . '</a>'
							);
							?>
						</p>
					</div>
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
				<div class="convertkit-select2-container convertkit-select2-container-grid">
					<select name="wp-convertkit[tag]" id="wp-convertkit-tag" class="convertkit-select2">
						<option value="0"<?php selected( '', $convertkit_post->get_tag() ); ?> data-preserve-on-refresh="1">
							<?php esc_html_e( 'None', 'convertkit' ); ?>
						</option>
						<?php
						if ( $convertkit_tags->exist() ) {
							foreach ( $convertkit_tags->get() as $convertkit_tag ) {
								?>
								<option value="<?php echo esc_attr( $convertkit_tag['id'] ); ?>"<?php selected( $convertkit_tag['id'], $convertkit_post->get_tag() ); ?>>
									<?php echo esc_attr( $convertkit_tag['name'] ); ?>
								</option>
								<?php
							}
						}
						?>
					</select>
					<button class="wp-convertkit-refresh-resources" class="button button-secondary hide-if-no-js" title="<?php esc_attr_e( 'Refresh Tags from ConvertKit account', 'convertkit' ); ?>" data-resource="tags" data-field="#wp-convertkit-tag">
						<span class="dashicons dashicons-update"></span>
					</button>
					<p class="description">
						<?php esc_html_e( 'Select a tag to apply to visitors of this page who are subscribed.', 'convertkit' ); ?>
						<br />
						<?php esc_html_e( 'A visitor is deemed to be subscribed if they have clicked a link in an email to this site which includes their subscriber ID, or have entered their email address in a ConvertKit Form on this site.', 'convertkit' ); ?>
					</p>
				</div>
			</td>
		</tr>

		<?php
		if ( $restrict_content_settings->enabled() ) {
			?>
			<tr valign="top">
				<th scope="row">
					<label for="wp-convertkit-restrict_content"><?php esc_html_e( 'Member Content', 'convertkit' ); ?></label>
				</th>
				<td>
					<div class="convertkit-select2-container convertkit-select2-container-grid">
						<select name="wp-convertkit[restrict_content]" id="wp-convertkit-restrict_content" class="convertkit-select2">
							<option value="0"<?php selected( '', $convertkit_post->get_restrict_content() ); ?> data-preserve-on-refresh="1">
								<?php esc_html_e( 'Don\'t restrict content to members only.', 'convertkit' ); ?>
							</option>

							<?php
							if ( $convertkit_products->exist() ) {
								?>
								<optgroup label="<?php esc_attr_e( 'Products', 'convertkit' ); ?>">
									<?php
									foreach ( $convertkit_products->get() as $product ) {
										?>
										<option value="product_<?php echo esc_attr( $product['id'] ); ?>"<?php selected( 'product_' . $product['id'], $convertkit_post->get_restrict_content() ); ?>>
											<?php echo esc_attr( $product['name'] ); ?>
										</option>
										<?php
									}
									?>
								</optgroup>
								<?php
							}
							?>
						</select>
						<button class="wp-convertkit-refresh-resources" class="button button-secondary hide-if-no-js" title="<?php esc_attr_e( 'Refresh Products Pages from ConvertKit account', 'convertkit' ); ?>" data-resource="products" data-field="#wp-convertkit-restrict_content">
							<span class="dashicons dashicons-update"></span>
						</button>
						<p class="description">
							<?php esc_html_e( 'Select the ConvertKit product that the visitor must be subscribed to, permitting them access to view this members only content.', 'convertkit' ); ?>
						</p>
					</div>
				</td>
			</tr>
			<?php
		}
		?>
	</tbody>
</table>

<?php
wp_nonce_field( 'wp-convertkit-save-meta', 'wp-convertkit-save-meta-nonce' );
