<?php
/**
 * Pre publish actions view
 *
 * @package ConvertKit
 * @author ConvertKit
 */

?>
<div id="convertkit-pre-publish-actions">
	<div class="misc-pub-section">
		<?php
		// Iterate through actions.
		foreach ( $pre_publish_actions as $name => $pre_publish_action ) {
			printf(
				'<label for="convertkit_action_%s">
					%s
					<input type="checkbox" name="_convertkit_action_%s" id="convertkit_action_%s" value="1"%s />
				</label>
				<p class="description">%s</p>',
				esc_html( $name ),
				esc_html( $pre_publish_action['label'] ),
				esc_html( $name ),
				esc_html( $name ),
				esc_html( checked( get_post_meta( $post->ID, '_convertkit_action_' . $name, true ), '1', false ) ),
				esc_html( $pre_publish_action['description'] )
			);
		}
		wp_nonce_field( 'wp-convertkit-pre-publish-actions', 'wp-convertkit-pre-publish-actions-nonce' );
		?>
	</div>
</div>
