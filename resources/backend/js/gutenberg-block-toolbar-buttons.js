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
    ( function( editor, richText, element, components, block ) {

        const {
            createElement
        }                                     = element;
        const { 
            registerFormatType,
            toggleFormat,
            applyFormat
        }                                     = richText;
        const { 
            RichTextToolbarButton
        }                                     = editor;
        const { 
            Popover,
            SelectControl
        }                                     = components;

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

        const type = 'convertkit/' + block.name;

        // Register Format Type.
        registerFormatType(
            'convertkit/' + block.name,
            {
                title:      block.title,
                tagName:    block.tag,
                className:  block.name,
                attributes: block.attributes,

                // Editor.
                edit: function( props ) {

                    // Define array of elements to display when the button is clicked.
                    var elements = [];

                    // Define fields.
                    for ( var attribute in block.fields ) {
                        const field = block.fields[ attribute ];
                       
                        // Build options for <select> input.
                        var fieldOptions = [
                            {
                                label: '(None)',
                                value: '',
                            }
                        ];
                        for ( var fieldValue in field.values ) {
                            fieldOptions.push(
                                {
                                    label: field.values[ fieldValue ],
                                    value: fieldValue
                                }
                            );
                        }

                        // Sort field's options alphabetically by label.
                        fieldOptions.sort(
                            function ( x, y ) {

                                let a = x.label.toUpperCase(),
                                b     = y.label.toUpperCase();
                                return a.localeCompare( b );

                            }
                        );

                        // Add field to array.
                        elements.push( createElement(
                            SelectControl,
                            {
                                key:        'convertkit_' + block.name + '_' + attribute,
                                label:      field.label,
                                value:      props.activeAttributes[ attribute ] ? props.activeAttributes[ attribute ] : '',
                                help:       field.description,
                                options:    fieldOptions,
                                onChange:   function( value ) {

                                    // Apply formatting changes.
                                    props.onChange( applyFormat(
                                        props.value,
                                        {
                                            type: type,

                                            // @TODO Make dynamic based on registered attributes.
                                            attributes: {
                                                'data-formkit-toggle': value,
                                                'href': 'http://something.com/' + value
                                            }
                                        }
                                    ) );

                                }
                            }
                        ) );
                    }

                    return [
                        createElement(
                            RichTextToolbarButton,
                            {
                                key:  'convertkit_' + block.name + '_rich_text_toolbar_button',
                                icon: icon,
                                title: block.title,
                                isActive: props.isActive,
                                onClick: function() {
                                    props.onChange(
                                        toggleFormat( props.value, {
                                            type: type
                                        })
                                    )
                                }
                            }
                        ),
                        props.isActive && ( createElement(
                            Popover,
                            {
                                key:  'convertkit_' + block.name + '_popover',
                                position: 'bottom center',
                                headerTitle: 'Sets Attributes',
                            },
                            elements
                        ))
                    ];
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
