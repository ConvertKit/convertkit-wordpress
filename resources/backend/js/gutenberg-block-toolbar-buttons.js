/**
 * Registers buttons in the Block Toolbar in the Gutenberg editor.
 *
 * @since   2.2.0
 *
 * @package ConvertKit
 * @author ConvertKit
 */

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

    // Register Block.
    ( function( blocks, editor, element, components, block ) {

        const el                              = element.createElement;
        const { registerBlockType }           = blocks;

        /*
        var RichText = editor.RichText;
        var AlignmentToolbar = editor.AlignmentToolbar;
        var BlockControls = editor.BlockControls;
        var useBlockProps = editor.useBlockProps;
        */

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
        registerBlockType(
            'convertkit/' + block.name,
            {
                title:      block.title,
                category:   block.category,
                icon:       icon,
                attributes: block.attributes,
                example:    {
                    attributes: {
                        is_gutenberg_example: true,
                    }
                },

                // Editor.
                edit: function( props ) {

                    return el(
                        'div',
                        useBlockProps(),
                        el(
                            BlockControls,
                            { key: 'controls' },
                            el( 
                                AlignmentToolbar, {
                                value: alignment,
                                onChange: function( value ) {
                                    if ( field.type == 'number' ) {
                                        // If value is a blank string i.e. no attribute value was provided,
                                        // cast it to the field's minimum number setting.
                                        // This prevents WordPress' block renderer API returning a 400 error
                                        // because a blank value will be passed as a string, when WordPress
                                        // expects it to be a numerical value.
                                        if ( value === '' ) {
                                            value = field.min;
                                        }

                                        // Cast value to integer if a value exists.
                                        if ( value.length > 0 ) {
                                            value = Number( value );
                                        }
                                    }

                                    var newValue          = {};
                                    newValue[ attribute ] = value;
                                    props.setAttributes( newValue );
                                },
                            } )
                        ),
                        el( RichText, {
                            key: 'richtext',
                            tagName: 'p',
                            style: { textAlign: alignment },
                            onChange: onChangeContent,
                            value: content,
                        } )
                    );

                },

                // Output.
                save: function( props ) {

                    

                },
            }
        );

    } (
        window.wp.blocks,
        window.wp.blockEditor,
        window.wp.element,
        window.wp.components,
        block
    ) );

}