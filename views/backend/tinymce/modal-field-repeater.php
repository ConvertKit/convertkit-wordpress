<div class="wpzinc-option">
	<div class="full">
		<table class="widefat">
			<thead>
				<tr>
					<?php
					foreach ( $field['sub_fields'] as $sub_field_name => $sub_field ) {
						?>
						<th><?php echo $sub_field['label']; ?></th>
						<?php
					}
					?>
					<th><?php _e( 'Actions', 'page-generator-pro' ); ?></th>
				</tr>
			</thead>

			<tfoot>
				<tr>
					<td colspan="3">
						<button class="wpzinc-add-table-row button" data-table-row-selector="repeater-row">
							<?php _e( 'Add', 'page-generator-pro' ); ?>
						</button>
					</td>
				</tr>
			</tfoot>

			<tbody id="<?php echo $shortcode['name']; ?>-<?php echo $field_name; ?>">
				<tr id="<?php echo $shortcode['name']; ?>-<?php echo $field_name; ?>-row" class="repeater-row hidden">
					<?php
					$sub_fields = $field['sub_fields'];
					foreach ( $sub_fields as $field_name => $field ) {
						?>
						<td>
							<?php include( 'tinymce-modal-field.php' ); ?>
						</td>
						<?php
					}
					?> 
					<td>
						<a href="#" class="wpzinc-delete-table-row">
							<?php _e( 'Delete', 'page-generator-pro' ); ?>
						</a>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>