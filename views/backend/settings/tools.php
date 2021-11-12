<div class="metabox-holder">

	<div class="postbox">
		<h3><span><?php esc_html_e( 'Debug Log', 'convertkit' ); ?></span></h3>
		<div class="inside">
			<p class="description">
				<?php _e( 'Use this tool to help debug ConvertKit plugin functionality.', 'convertkit' ); ?><br />
				<?php _e( 'For performance, the last 500 lines of the log are displayed. Use the Download Log option to review the full log.', 'convertkit' ); ?><br />
			</p>

			<textarea readonly="readonly" class="large-text convertkit-monospace" rows="15"><?php echo esc_textarea( $log->read() ); ?></textarea>

			<?php
			if ( $log->exists() ) {
				?>
				<p class="submit">
					<?php
					submit_button(
						__( 'Download Log', 'convertkit' ),
						'primary',
						'convertkit-download-debug-log',
						false
					);

					submit_button(
						__( 'Clear Log', 'convertkit' ),
						'secondary',
						'convertkit-clear-debug-log',
						false
					);
					?>
				</p>
				<p><?php _e( 'Log file', 'convertkit' ); ?>: <code><?php echo $log->get_filename(); ?></code></p>
				<?php
			}
			?>
		</div><!-- .inside -->
	</div><!-- .postbox -->

	<div class="postbox">
		<h3><span><?php esc_html_e( 'System Info', 'convertkit' ); ?></span></h3>
		<div class="inside">
			<p><?php _e( 'Use this tool to send system info to support when necessary.', 'convertkit' ); ?></p>

			<textarea readonly="readonly" class="large-text convertkit-monospace" rows="15"><?php echo esc_textarea( $system_info->get() ); ?></textarea>

			<p class="submit">
				<?php
				submit_button(
					__( 'Download System Info', 'convertkit' ),
					'primary',
					'convertkit-download-system-info',
					false
				);
				?>
			</p>
		</div><!-- .inside -->

		<?php
		// Nonce for Log and System Info actions.
		wp_nonce_field( 'convertkit-settings-tools', '_convertkit_settings_tools_nonce' );
		?>
	</div><!-- .postbox -->
</div><!-- .metabox-holder -->