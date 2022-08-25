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
							<a href="admin.php?page=convertkit-setup&step=<?php echo esc_attr( ( $this->step + 1 ) ); ?>" class="button button-primary">
								<?php echo esc_html( $this->steps[ $this->step ]['next_button_label'] ); ?>
							</a>
						</div>
						<?php
					}
					?>
				</div>
			</div><!-- /#convertkit-setup-body -->
		</div>
	</body>
</html>
