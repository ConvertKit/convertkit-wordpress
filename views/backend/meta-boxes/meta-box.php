<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row"><label for="wp-convertkit-form"><?php _e('Form'); ?></label></th>
			<td>
				<select name="wp-convertkit[form]" id="wp-convertkit-form">
					<option <?php selected(-1, $meta['form']); ?> value="-1"><?php _e('Default'); ?></option>
					<option <?php selected(0, $meta['form']); ?> value="0"><?php _e('None'); ?></option>
					<?php foreach($forms as $form) { ?>
					<option <?php selected($form['id'], $meta['form']); ?> value="<?php esc_attr_e($form['id']); ?>"><?php esc_html_e($form['name']); ?></option>
					<?php } ?>
				</select><br />
				<small>
					<?php
					printf(__('Choose <code>Default</code> to use the form specified on the <a href="%s" target="_blank">settings page</a>,
							<code>None</code> to not display a form, or any other option to specify a particular form for this piece of content.'), esc_attr(esc_url($settings_link)));
					?>
				</small>
				<br />
				<small>
					<?php _e('To make changes to your forms, '); ?>
					<a href="https://convertkit.com/app/landing_pages" target="_blank"><?php _e('sign in to ConvertKit'); ?></a>
				</small>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row"><label for="wp-convertkit-form_orientation"><?php _e('Form Orientation'); ?></label></th>
			<td>
				<select name="wp-convertkit[form_orientation]" id="wp-convertkit-form_orientation">
					<option <?php selected('default', $meta['form_orientation']); ?> value="default"><?php _e('Default'); ?></option>
					<option <?php selected('horizontal', $meta['form_orientation']); ?> value="horizontal"><?php _e('Horizontal'); ?></option>
					<option <?php selected('vertical', $meta['form_orientation']); ?> value="vertical"><?php _e('Vertical'); ?></option>
				</select>
			</td>
		</tr>
	</tbody>
</table>


<?php wp_nonce_field('wp-convertkit-save-meta', 'wp-convertkit-save-meta-nonce');
