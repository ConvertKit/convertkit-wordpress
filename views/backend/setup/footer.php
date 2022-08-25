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
						if ( isset( $this->steps[ $this->step ]['back_button'] ) ) {
							?>
							<div class="left">
								<a href="<?php echo esc_attr( $this->steps[ $this->step ]['back_button']['url'] ); ?>" class="button">
									<?php echo esc_html( $this->steps[ $this->step ]['back_button']['label'] ); ?>
								</a>
							</div>
							<?php
						}

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
		</div>
	</body>
</html>
