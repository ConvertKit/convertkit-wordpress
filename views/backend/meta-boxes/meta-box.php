<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row"><label for="wp-convertkit-form"><?php _e('Form', 'convertkit'); ?></label></th>
			<td>
				<select name="wp-convertkit[form]" id="wp-convertkit-form">
					<option <?php selected(-1, $meta['form']); ?> value="-1"><?php _e('Default', 'convertkit'); ?></option>
					<option <?php selected(0, $meta['form']); ?> value="0"><?php _e('None', 'convertkit'); ?></option>
					<?php foreach($forms as $form) { ?>
					<option <?php selected($form['id'], $meta['form']); ?> value="<?php echo esc_attr($form['id'], 'convertkit'); ?>"><?php esc_html_e($form['name'], 'convertkit'); ?></option>
					<?php } ?>
				</select>

				<p class="description">
					<?php
					printf(__('Choose <code>Default</code> to use the form specified on the <a href="%s" target="_blank">settings page</a>,', 'convertkit'), esc_attr(esc_url($settings_link)));
					echo __('<code>None</code> to not display a form, or any other option to specify a particular form for this piece of content.', 'convertkit');
					?>
				</p>

				<p class="description">
					<?php _e('To make changes to your forms,', 'convertkit'); ?>
					<a href="https://app.convertkit.com/" target="_blank"><?php _e('sign in to ConvertKit', 'convertkit'); ?></a>
				</p>
			</td>
		</tr>

		<?php if('page' === $post->post_type) { ?>
		<tr valign="top">
			<th scope="row"><label for=""><?php _e('Landing Page', 'convertkit'); ?></label></th>
			<td>
				<select name="wp-convertkit[landing_page]" id="wp-convertkit-landing_page">
					<option <?php selected('', $meta['landing_page']); ?> value="0"><?php _e('None', 'convertkit'); ?></option>
					<?php foreach($landing_pages as $landing_page) { ?>
					<option <?php selected($landing_page['url'], $meta['landing_page']); ?> value="<?php echo esc_attr($landing_page['url'], 'convertkit'); ?>"><?php esc_html_e($landing_page['name'], 'convertkit'); ?></option>
					<?php } ?>
				</select>

				<p class="description"><?php _e('Select a landing page to make it appear in place of this page.', 'convertkit'); ?></p>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>

<?php wp_nonce_field('wp-convertkit-save-meta', 'wp-convertkit-save-meta-nonce');

