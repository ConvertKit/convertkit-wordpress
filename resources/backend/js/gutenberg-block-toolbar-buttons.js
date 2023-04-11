/**
 * Registers buttons in the Block Toolbar in the Gutenberg editor.
 *
 * @since   2.2.0
 *
 * @package ConvertKit
 * @author ConvertKit
 */

console.log( 'loaded' );

// Register Gutenberg Block Toolbar buttons if the Gutenberg Editor is loaded on screen.
// This prevents JS errors if this script is accidentally enqueued on a non-
// Gutenberg editor screen, or the Classic Editor Plugin is active.
if ( typeof wp !== 'undefined' &&
    typeof wp.blocks !== 'undefined' ) {

    // Register each ConvertKit Block Toolbar button in Gutenberg.
    for ( const button in convertkit_blocks_toolbar_buttons ) {
        convertKitGutenbergRegisterBlockToolbarButton( convertkit_blocks_toolbar_buttons[ button ] );
    }

}

/**
 * Registers the given block toolbar button in Gutenberg.
 *
 * @since   2.2.0
 *
 * @param   object  block   Block Toolbar Button.
 */
function convertKitGutenbergRegisterBlockToolbarButton( block ) {

    console.log( block );

    // Register Block.
    ( function( editor, richText, element, components, block ) {

        const el                              = element.createElement;
        const { registerFormatType }          = richText;
        const { BlockControls }               = editor;
        const { 
            ToolbarGroup,
            ToolbarButton
        } = components;

        // Build Icon, if it's an object.
        var icon = 'dashicons-tablet';
        if ( typeof block.gutenberg_icon !== 'undefined' ) {
            if ( block.gutenberg_icon.search( 'svg' ) >= 0 ) {
                // SVG.
                icon = element.RawHTML(
                    {
                        children: block.gutenberg_icon
                    }
                );
            } else {
                // Dashicon.
                icon = block.gutenberg_icon;
            }
        }

        // Register Block.
        registerFormatType(
            'convertkit/' + block.name,
            {
                title:      block.title,
                tagName:    'a',
                className:  block.name,

                // Editor.
                edit: function( props ) {

                    console.log( 'edit' );

                    return el(
                        BlockControls,
                        {},
                        el(
                            ToolbarGroup,
                            {},
                            el(
                                ToolbarButton,
                                {
                                    icon: icon,
                                    title: block.title,
                                    onClick: function() {
                                        console.log( 'button clicked' );
                                    }
                                }
                            )
                        )
                    )

                    /*
                    return el(
                        RichTextToolbarButton,
                        {
                            icon: 'editor-code',
                            title: block.title,
                            onClick: function() {
                                console.log( 'button clicked' );
                            }
                        }
                    );
                    */

                }
            }
        );

    } (
        window.wp.blockEditor,
        window.wp.richText,
        window.wp.element,
        window.wp.components,
        block
    ) );

}