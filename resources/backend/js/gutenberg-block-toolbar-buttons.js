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
            createElement,
            Fragment,
            useState
        }                                     = element;
        const { 
            registerFormatType,
            toggleFormat
        }                                     = richText;
        const { 
            BlockControls,
            URLPopover,
            ColorPalette
        }                                     = editor;
        const { 
            ToolbarGroup,
            ToolbarButton,
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

        // Register Block.
        registerFormatType(
            'convertkit/' + block.name,
            {
                title:      block.title,
                tagName:    'a',
                className:  block.name,

                // Editor.
                edit: function( props ) {

                    // Store whether the button has been clicked.
                    const [ showFields, setShowFields ] = useState( false );

                    // Define array of elements to display when the button is clicked.
                    var elements = [];

                    // Define toolbar button.
                    elements.push(
                        createElement(
                            ToolbarButton,
                            {
                                key:  'convertkit_' + block.name + '_toolbar_button',
                                icon: icon,
                                title: block.title,
                                onClick: function() {
                                    // Set the flag to show the fields using useState().
                                    setShowFields( true );
                                }
                            }
                        )
                    );

                    // Define fields to display if the button was clicked and we need to show the options.
                    if ( showFields ) {
                        for ( var attribute in block.fields ) {
                            const field = block.fields[ attribute ]; // field array.
                           
                            // Build options for <select> input.
                            var fieldOptions = [
                                {
                                    label: '(None)',
                                    value: '',
                                }
                            ];
                            for ( var value in field.values ) {
                                fieldOptions.push(
                                    {
                                        label: field.values[ value ],
                                        value: value
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
                                    id:         'convertkit_' + block.name + '_' + attribute,
                                    label:      field.label,
                                    help:       field.description,
                                    options:    fieldOptions,
                                    onChange:   function( value ) {
                                        console.log( value );

                                        // Set the flag to hide the fields using useState().
                                        setShowFields( false );
                                    }
                                }
                            ) );
                        }
                    }

                    // Return.
                    return createElement(
                        Fragment,
                        {},
                        createElement(
                            BlockControls,
                            {},
                            createElement(
                                ToolbarGroup,
                                {},
                                elements
                            )
                        )
                    )
                    
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
