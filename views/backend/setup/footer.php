<?php
/**
 * Outputs the footer template for the setup screen
 *
 * @package ConvertKit
 * @author ConvertKit
 */

?>
				</div>

				<div id="convertkit-setup-footer">
					<?php
					if ( isset( $back_button_label ) ) {
						?>
						<div class="left">
							<a href="<?php echo esc_attr( $back_button_url ); ?>" class="button"><?php echo esc_html( $back_button_label ); ?></a>
						</div>
						<?php
					}

					if ( isset( $next_button_label ) ) {
						?>
						<div class="right">
							<?php wp_nonce_field( 'convertkit-restrict-content-setup' ); ?>
							<button class="button button-primary button-large"><?php echo esc_html( $next_button_label ); ?></button>
						</div>
						<?php
					}
					?>
				</div>
			</form>
		</div>
	</body>
</html>
