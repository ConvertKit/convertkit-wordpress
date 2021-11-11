<?php
// Output Field
if ( $field['type'] == 'repeater' ) {
    include( 'modal-field-repeater.php' );
} else {
    $condition = '';
    if ( isset( $field['condition'] ) ) {
        if ( is_array( $field['condition']['value'] ) ) {
            $condition = implode( ' ', $field['condition']['value'] );
        } else {
            $condition = $field['condition']['value'];
        }
    }
    ?>
    <div class="convertkit-option">
        <div class="left">
            <label for="tinymce_modal_<?php echo $field_name; ?>">
                <?php echo $field['label']; ?>
            </label>
        </div>
        <div class="right <?php echo $condition; ?>">
            <?php
            include( 'modal-field.php' );
            ?>
        </div>
    </div>
    <?php
}