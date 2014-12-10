<div class="wrap">
	<?php screen_icon(); ?>
	<h2><?php _e('ConvertKit Settings', 'wp_convertkit'); ?></h2>

	<form action="options.php" method="post">
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row"><label for="<?php echo esc_attr(self::_settings_id('api_key')); ?>"><?php _e('API Key'); ?></label></th>
					<td>
						<input class="regular-text code" type="text" name="<?php echo esc_attr(self::_settings_name('api_key')); ?>" id="<?php echo esc_attr(self::_settings_id('api_key')); ?>" value="<?php echo esc_attr($settings['api_key']); ?>" />
						<p class="description"><a href="https://app.convertkit.com/account/edit" target="_blank"><?php _e('Get your ConvertKit API Key'); ?></a></p>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><label for="<?php echo esc_attr(self::_settings_id('default_form')); ?>"><?php _e('Default Form'); ?></label></th>
					<td>
						<select name="<?php echo esc_attr(self::_settings_name('default_form')); ?>" id="<?php echo esc_attr(self::_settings_id('default_form')); ?>">
							<option <?php selected(0, $settings['default_form']); ?> value="0"><?php _e('None'); ?></option>
							<?php foreach($forms as $form) { ?>
							<option <?php selected($form['id'], $settings['default_form']); ?> value="<?php echo esc_attr($form['id']); ?>"><?php esc_html_e($form['name']); ?></option>
							<?php } ?>
						</select>

						<?php if(!$forms) { ?>
						<p class="description"><?php _e('Enter your API Key above to get your available forms'); ?></p>
						<?php } ?>
					</td>
				</tr>

			</tbody>
		</table>

		<p class="submit">
			<?php settings_fields(self::SETTINGS_NAME); ?>
			<input type="submit" class="button button-primary" name="<?php echo esc_attr(self::_settings_id('save')); ?>-settings" value="<?php _e('Save Changes'); ?>" />
		</p>

		<p class="description">
			<?php _e('If you have questions or problems, please contact', 'wp_convertkit'); ?>
			<a href="mailto:support@convertkit.com?subject=<?php echo esc_attr(urlencode(__('ConvertKit WordPress Plugin - Support', 'wp_convertkit'))); ?>">support@convertkit.com</a>
		</p>
	</form>
</div>