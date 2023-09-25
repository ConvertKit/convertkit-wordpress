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
		foreach ( $pre_publish_actions as $name => $action ) {
			printf(
				'<label for="convertkit_action_%s">
					%s
					<input type="checkbox" name="wp-convertkit[%s]" id="convertkit_action_%s" value="1" />
				</label>
				<p class="description">%s</p>',
				$name,
				$action['label'],
				$name,
				$name,
				$action['description']
			);
		}
		wp_nonce_field( 'wp-convertkit-save-meta', 'wp-convertkit-save-meta-nonce' );
		?>
	</div>
</div>
