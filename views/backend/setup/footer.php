<?php
/**
 * Outputs the footer template for the setup screen
 *
 * @package ConvertKit
 * @author ConvertKit
 */

?>				
					</div><!-- /#convertkit-setup-content -->

					<div id="convertkit-setup-footer">
						<?php
						// Display a back link if supplied.
						if ( $this->previous_step_url ) {
							?>
							<div class="left">
								<a href="<?php echo $this->previous_step_url ?>" class="button">
									<?php echo esc_html_e( 'Back', 'convertkit' ); ?>
								</a>
							</div>
							<?php
						}

						// Display a submit button if supplied.
						if ( isset( $this->steps[ $this->step ]['next_button'] ) ) {
							?>
							<div class="right">
								<?php wp_nonce_field( 'convertkit-setup' ); ?>
								<button class="button button-primary button-large"><?php echo esc_html( $this->steps[ $this->step ]['next_button']['label'] ); ?></button>
							</div>
							<?php
						}
						?>
					</div>
				</form>
			</div><!-- /#convertkit-setup-body -->

			<div id="convertkit-setup-exit-link">
				<a href="<?php echo $this->exit_url; ?>" title="<?php esc_html_e( 'Exit wizard without saving', 'convertkit' ); ?>">
					<?php esc_html_e( 'Exit wizard without saving', 'convertkit' ); ?>
				</a>
			</div>
		</div>
	</body>
</html>
