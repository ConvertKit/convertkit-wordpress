<?php
/**
 * Notification output for a review request.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

?>

<div class="notice notice-info is-dismissible review-<?php echo esc_attr( $this->plugin_slug ); ?>">
	<p>
		<?php
		echo esc_html(
			sprintf(
				/* translators: Plugin Name */
				__( 'We\'d be super grateful if you could spread the word about %s and give it a 5 star rating on WordPress?', 'convertkit' ),
				$this->plugin_name
			)
		);
		?>
	</p>
	<p>
		<a href="<?php echo esc_attr( $this->get_review_url() ); ?>" class="button button-primary" rel="noopener" target="_blank">
			<?php esc_html_e( 'Yes, leave review', 'convertkit' ); ?>
		</a>
		<a href="<?php echo esc_attr( $this->get_support_url() ); ?>" class="button" rel="noopener" target="_blank">
			<?php
			echo esc_html(
				sprintf(
					/* translators: Plugin Name */
					__( 'No, I\'m having issues with %s', 'convertkit' ),
					$this->plugin_name
				)
			);
			?>
		</a>
	</p>

	<script type="text/javascript">
		jQuery( document ).ready( function( $ ) {
			// Dismiss Review Notification.
			$( 'div.review-<?php echo esc_attr( $this->plugin_slug ); ?>' ).on( 'click', 'a, button.notice-dismiss', function( e ) {

				// Do request
				$.post( 
					ajaxurl, 
					{
						action: '<?php echo esc_attr( str_replace( '-', '_', $this->plugin_slug ) ); ?>_dismiss_review',
					},
					function( response ) {
					}
				);

				// Hide notice
				$( 'div.review-<?php echo esc_attr( $this->plugin_slug ); ?>' ).hide();

			} );
		} );
	</script>
</div>

