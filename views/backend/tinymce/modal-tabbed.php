<?php
/**
 * TinyMCE Tabbed Modal view
 *
 * @package ConvertKit
 * @author ConvertKit
 */

?>

<!-- .wp-core-ui ensures styles are applied on frontend editors for e.g. buttons.css -->
<form class="convertkit-tinymce-popup wp-core-ui">
	<input type="hidden" name="shortcode" value="convertkit_<?php echo esc_attr( $shortcode['name'] ); ?>" />
	<input type="hidden" name="editor_type" value="<?php echo esc_attr( $editor_type ); // quicktags|tinymce. ?>" />
	<input type="hidden" name="close_shortcode" value="<?php echo esc_attr( $shortcode['shortcode_include_closing_tag'] ? '1' : '0' ); ?>" />

	<!-- Vertical Tabbed UI -->
	<div class="convertkit-vertical-tabbed-ui">
		<!-- Tabs -->
		<ul class="convertkit-nav-tabs convertkit-js-tabs" 
			data-panels-container="#<?php echo esc_attr( $shortcode['name'] ); ?>-container"
			data-panel=".<?php echo esc_attr( $shortcode['name'] ); ?>"
			data-active="convertkit-nav-tab-vertical-active"
			data-match-height="#convertkit-tinymce-modal-body">

			<?php
			// data-match-height="#convertkit-tinymce-modal-body" removed from above.
			// Output each Tab.
			$first_tab = true;
			foreach ( $shortcode['panels'] as $modal_tab_name => $modal_tab ) {
				?>
				<li class="convertkit-nav-tab<?php echo esc_attr( ( isset( $modal_tab['class'] ) ? ' ' . $modal_tab['class'] : '' ) ); ?>">
					<a href="#<?php echo esc_attr( $shortcode['name'] ) . '-' . esc_attr( $modal_tab_name ); ?>"<?php echo ( $first_tab ? ' class="convertkit-nav-tab-vertical-active"' : '' ); ?>>
						<?php echo esc_html( $modal_tab['label'] ); ?>
					</a>
				</li>
				<?php
				$first_tab = false;
			}
			?>
		</ul>

		<!-- Content -->
		<div id="<?php echo esc_attr( $shortcode['name'] ); ?>-container" class="convertkit-nav-tabs-content">
			<?php
			// Output each Tab Panel.
			foreach ( $shortcode['panels'] as $modal_tab_name => $modal_tab ) {
				?>
				<div id="<?php echo esc_attr( $shortcode['name'] ) . '-' . esc_attr( $modal_tab_name ); ?>" class="<?php echo esc_attr( $shortcode['name'] ); ?>">
					<div class="postbox">
						<?php
						// Iterate through this tab's field names.
						foreach ( $modal_tab['fields'] as $field_name ) {
							// Skip if this field doesn't exist.
							if ( ! isset( $shortcode['fields'][ $field_name ] ) ) {
								continue;
							}

							// Fetch the field properties.
							$field = $shortcode['fields'][ $field_name ];

							// Output Field.
							include 'modal-field-row.php';
						}
						?>
					</div>
				</div>
				<?php
			}
			?>
		</div>
	</div>
</form>
