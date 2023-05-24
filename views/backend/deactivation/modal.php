<?php
/**
 * Deactivation Modal view, displayed when a Plugin is deactivated.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

?>
<div id="convertkit-deactivation-modal-overlay" class="convertkit-modal-overlay"></div>
<div id="convertkit-deactivation-modal" class="convertkit-modal">
	<h2 class="title">
		<?php echo esc_html( CONVERTKIT_PLUGIN_NAME ); ?>
	</h2>

	<p class="message">
		<?php
		echo sprintf(
			/* Translators: Plugin Name */
			esc_html__( 'Optional: We\'d be grateful if you could take a moment to let us know why you\'re deactivating %s', 'convertkit' ),
			esc_html( CONVERTKIT_PLUGIN_NAME )
		);
		?>
	</p>

	<form method="post" action="<?php echo esc_url( admin_url( 'plugins.php' ) ); ?>" id="convertkit-deactivation-modal-form">
		<ul>
			<?php
			if ( is_array( $reasons ) && count( $reasons ) > 0 ) {
				foreach ( $reasons as $key => $label ) {
					?>
					<li>
						<label>
							<span><input type="radio" name="reason" value="<?php echo esc_attr( $key ); ?>" /></span>
							<span><?php echo esc_html( $label ); ?></span>
						</label>
					</li>
					<?php
				}
			}
			?>
		</ul>

		<div class="additional-information">
			<p>
				<label for="reason_text">
					<?php esc_html_e( 'Optional: Was there a problem, any feedback or something we could do better?', 'convertkit' ); ?>
				</label>
				<input type="text" id="reason_text" name="reason_text" value="" placeholder="<?php esc_attr_e( 'e.g. XYZ Plugin because it has this feature...', 'convertkit' ); ?>" class="widefat" />
			</p>
		</div>

		<input type="submit" name="submit" value="<?php esc_attr_e( 'Deactivate', 'convertkit' ); ?>" class="button button-primary" />
	</form>
</div>
