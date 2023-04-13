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
            getTextContent,
            slice,
            insert,
            create,
            toggleFormat,
            applyFormat
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

        const type = 'convertkit/' + block.name;

        // Register Format Type.
        registerFormatType(
            'convertkit/' + block.name,
            {
                title:      block.title,
                tagName:    'a',
                className:  block.name,

                // Editor.
                edit: function( props ) {

                    // Get properties we want to inspect.
                    const { 
                        isActive,
                        onChange,
                        value
                    } = props;

                    // What is going on here?
                    const onToggle = function() {
                        // Set up the anchorRange when the Popover is opened.
                        const selection = document.defaultView.getSelection();

                        anchorRange = selection.rangeCount > 0 ? selection.getRangeAt( 0 ) : null;
                        onChange( toggleFormat( value, { type } ) );
                    };

                    // Get selected text. If none is selected, this string will be blank.
                    const selectedText = getTextContent( slice( value ) );

                    // Store whether the fields should be displayed i.e. has the button been clicked.
                    const [ showFields, setShowFields ] = useState( false );

                    // Define array of elements to display when the button is clicked.
                    var elements = [];

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
                                    help:       field.description,
                                    options:    fieldOptions,
                                    onChange:   function( formID ) {

                                        const newValue = {
                                            ...formID,
                                            formats: value.formats.splice(
                                                value.start,
                                                0,
                                                value.formats.at( value.start )
                                            ),
                                            text: '<a href="#">' + formID + '</a>',
                                        };

                                        onChange( insert( value, newValue ) );

                                        //onChange( toggleFormat( value, { type } ) );

                                        /*
                                        onChange( toggleFormat( value, {
                                            attributes: {
                                                'href': '#' + formID,
                                                'data-test': 'yes'
                                            }
                                        }));
                                        */

                                        // Set the flag to hide the fields using useState().
                                        setShowFields( false );

                                        /*
                                        console.log( selectedText );
                                        console.log( value );
                                        console.log( formID );

                                        element = create({
                                            'html' : 'before ' + formID + ' after'
                                        });

                                        if( element.formats.length === 0 ) {
                                            return;
                                        }
                                        for ( let i = element.formats[0].length - 1; i >= 0; i-- ) {
                                            value = toggleFormat(value, element.formats[0][i]);
                                        }

                                        onChange(value);
                                        */
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
                                [
                                    createElement(
                                        ToolbarButton,
                                        {
                                            key:  'convertkit_' + block.name + '_toolbar_button',
                                            icon: icon,
                                            title: block.title,
                                            onClick: function() {
                                                // Set the flag to show the fields using useState().
                                                setShowFields( true );

                                                onToggle;
                                            }
                                        }
                                    ),
                                    createElement(
                                        URLPopover,
                                        {
                                            key:  'convertkit_' + block.name + '_toolbar_button_fields',
                                        },
                                        elements
                                    )
                                ]
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
