<?php
/**
 * Settings > Tools view
 *
 * @package ConvertKit
 * @author ConvertKit
 */

?>
<div class="metabox-holder">
	<div id="debug-log" class="postbox">
		<h2><?php esc_html_e( 'Debug Log', 'convertkit' ); ?></h2>
		<p class="description">
			<?php esc_html_e( 'Use this tool to help debug Kit plugin functionality.', 'convertkit' ); ?><br />
			<?php esc_html_e( 'For performance, the last 500 lines of the log are displayed. Use the Download Log option to review the full log.', 'convertkit' ); ?><br />
		</p>

		<textarea readonly="readonly" id="debug-log-textarea" class="large-text convertkit-monospace" rows="15"><?php echo esc_textarea( $log->read() ); ?></textarea>

		<?php
		if ( $log->exists() ) {
			?>
			<p>
				<?php
				submit_button(
					__( 'Download log', 'convertkit' ),
					'primary',
					'convertkit-download-debug-log',
					false
				);
				?>
				<input type="submit" name="convertkit-clear-debug-log" id="convertkit-clear-debug-log" class="button button-secondary" value="<?php esc_attr_e( 'Clear log', 'convertkit' ); ?>" />
			</p>
			<p><?php esc_html_e( 'Log file', 'convertkit' ); ?>: <code><?php echo esc_attr( $log->get_filename() ); ?></code></p>
			<?php
		}
		?>
	</div><!-- .postbox -->

	<div id="system-info" class="postbox">
		<h2><?php esc_html_e( 'System Info', 'convertkit' ); ?></h2>
		<p class="description"><?php esc_html_e( 'Use this tool to send system info to support when necessary.', 'convertkit' ); ?></p>

		<textarea readonly="readonly" id="system-info-textarea" class="large-text convertkit-monospace" rows="15"><?php echo esc_textarea( $system_info ); ?></textarea>

		<p>
			<?php
			submit_button(
				__( 'Download system info', 'convertkit' ),
				'primary',
				'convertkit-download-system-info',
				false
			);
			?>
		</p>
	</div><!-- .postbox -->

	<div id="export" class="postbox">
		<h2><?php esc_html_e( 'Export Configuration', 'convertkit' ); ?></h2>

		<p class="description">
			<?php esc_html_e( 'Downloads this plugin\'s configuration as a JSON file.', 'convertkit' ); ?><br />
			<strong>
				<?php esc_html_e( 'This file includes sensitive API credentials. Use with caution.', 'convertkit' ); ?>
			</strong>
		</p>

		<p>
			<?php
			submit_button(
				__( 'Export', 'convertkit' ),
				'primary',
				'convertkit-export',
				false
			);
			?>
		</p>
	</div><!-- .postbox -->

	<div id="import" class="postbox">
		<h2><?php esc_html_e( 'Import Configuration', 'convertkit' ); ?></h2>

		<p class="description">
			<?php esc_html_e( 'Imports a configuration file generated by this plugin.', 'convertkit' ); ?><br />
			<strong>
				<?php esc_html_e( 'This will overwrite any existing settings stored on this installation.', 'convertkit' ); ?><br />
			</strong>
		</p>

		<input type="file" name="import" />

		<p>
			<?php
			submit_button(
				__( 'Import', 'convertkit' ),
				'primary',
				'convertkit-import',
				false
			);
			?>
		</p>
	</div><!-- .postbox -->

	<?php
	wp_nonce_field( 'convertkit-settings-tools', '_convertkit_settings_tools_nonce' );
	?>
</div><!-- .metabox-holder -->
