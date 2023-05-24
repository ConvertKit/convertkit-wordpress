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
	<header>
		<h2 class="title">
			<?php echo esc_html_e( 'What went wrong?', 'convertkit' ); ?>
		</h2>
	</header>

	<form method="post" action="<?php echo esc_url( admin_url( 'plugins.php' ) ); ?>" id="convertkit-deactivation-modal-form">
		<ul>
			<?php
			if ( is_array( $reasons ) && count( $reasons ) > 0 ) {
				foreach ( $reasons as $reason => $labels ) {
					?>
					<li>
						<label>
							<span>
								<input type="radio" name="convertkit-deactivation-reason" value="<?php echo esc_attr( $reason ); ?>" data-placeholder="<?php echo esc_attr( $labels['placeholder'] ); ?>" />
							</span>
							<span><?php echo esc_html( $labels['label'] ); ?></span>
						</label>
					</li>
					<?php
				}
			}
			?>
		</ul>

		<input type="text" name="convertkit-deactivation-reason-text" placeholder="" class="widefat" />

		<input type="submit" name="submit" value="<?php esc_attr_e( 'Deactivate', 'convertkit' ); ?>" class="button" />
	</form>
</div>
