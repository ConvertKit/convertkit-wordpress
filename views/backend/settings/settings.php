<div class="wrap">
	<?php screen_icon(); ?>
	<h2><?php _e('ConvertKit Settings'); ?></h2>

	<form method="post" action="<?php esc_attr_e(esc_url($settings_link)); ?>">
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row"><label for="wp-convertkit-api_key"><?php _e('API Key'); ?></label></th>
					<td>
						<input class="regular-text code" type="text" name="wp-convertkit[api_key]" id="wp-convertkit-api_key" value="<?php esc_attr_e($settings['api_key']); ?>" /><br />
						<small><a href="https://convertkit.com/app/account/edit" target="_blank"><?php _e('Get your ConvertKit key now'); ?></a></small>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><label for="wp-convertkit-default_form"><?php _e('Default Form'); ?></label></th>
					<td>
						<select name="wp-convertkit[default_form]" id="wp-convertkit-default_form">
							<option <?php selected(0, $settings['default_form']); ?> value="0"><?php _e('None'); ?></option>
							<?php foreach($forms as $form) { ?>
							<option <?php selected($form['id'], $settings['default_form']); ?> value="<?php esc_attr_e($form['id']); ?>"><?php esc_html_e($form['name']); ?></option>
							<?php } ?>
						</select>
						<?php if(empty($settings['api_key'])) { ?>
							<br />
							<small><?php _e('Enter your API Key above to get your available forms'); ?></small>
						<?php } ?>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><label for="wp-convertkit-default_form_orientation"><?php _e('Default Form Orientation'); ?></label></th>
					<td>
						<select name="wp-convertkit[default_form_orientation]" id="wp-convertkit-default_form_orientation">
							<option <?php selected('horizontal', $settings['default_form_orientation']); ?> value="horizontal"><?php _e('Horizontal'); ?></option>
							<option <?php selected('vertical', $settings['default_form_orientation']); ?> value="vertical"><?php _e('Vertical'); ?></option>
						</select>
					</td>
				</tr>
			</tbody>
		</table>

		<p class="submit">
			<?php wp_nonce_field('wp-convertkit-save-settings', 'wp-convertkit-save-settings-nonce'); ?>
			<input type="submit" class="button button-primary" name="wp-convertkit-save-settings" value="<?php _e('Save Changes'); ?>" />
		</p>

		<p>
			<em>
				<?php _e('If you have questions or problems, please contact'); ?>
				<a href="mailto:support@convertkit.com?subject=<?php esc_attr_e(urlencode(__('ConvertKit WordPress Plugin - Support'))); ?>">support@convertkit.com</a>
			</em>
		</p>
	</form>
</div>