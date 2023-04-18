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
            Fragment,
            useState,
            createElement
        }                                     = element;
        const { 
            registerFormatType,
            toggleFormat,
            applyFormat,
            useAnchorRef
        }                                     = richText;
        const { 
            BlockControls,
            RichTextToolbarButton,
        }                                     = editor;
        const {
            Button,
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

        // Register Format Type.
        registerFormatType(
            'convertkit/' + block.name,
            {
                title:      block.title,

                // The tagName and className combination allow Gutenberg to uniquely identify
                // whether this formatter has been used on the selected text.
                tagName:    block.tag,
                className:  block.name,

                attributes: block.attributes,

                // Editor.
                edit: function( props ) {

                    // Get props and anchor reference to the text.
                    const { contentRef, isActive, onChange, value } = props;
                    const { activeFormats } = value;
                    const anchorRef = useAnchorRef( { ref: contentRef, value } );

                    // State to show popover.
                    const [ showPopover, setShowPopover ] = useState( false );

                    // Define array of elements to display when the button is clicked.
                    var elements = [];

                    // Define object comprising of attributes.
                    var attributes = {};
                    for ( var attribute in block.attributes ) {
                        attributes[ attribute ] = '';
                    }

                    // If this formatter has been applied to the selected text,
                    // the selected text may have existing attributes.
                    // Fetch those attribute values.
                    const formats = activeFormats.filter( format => 'convertkit/' + block.name === format['type'] );
                    if ( formats.length > 0 ) {
                        for ( var attribute in block.attributes ) {
                            // @TODO Figure out why the attributes begin in .unregisteredAttributes, but then
                            // on change move to .attributes.
                            if ( typeof formats[0].unregisteredAttributes !== 'undefined' ) {
                                attributes[ attribute ] = formats[0].unregisteredAttributes[ attribute ];   
                            } else if ( typeof formats[0].attributes !== 'undefined' ) {
                                attributes[ attribute ] = formats[0].attributes[ attribute ];
                            }
                        }
                    }

                    // Define fields.
                    for ( var fieldName in block.fields ) {
                        const field = block.fields[ fieldName ];

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
                                key:        'convertkit_' + block.name + '_' + fieldName,
                                label:      field.label,
                                value:      attributes[ fieldName ],
                                help:       field.description,
                                options:    fieldOptions,
                                onChange:   function( newValue ) {

                                    setShowPopover( false );

                                    if ( newValue ) {
                                        // Build object of new attributes.
                                        var newAttributes = {};
                                        for ( var attribute in block.attributes ) {
                                            // If 'None' selected, blank the attribute's value.
                                            if ( newValue === '' ) {
                                                newAttributes[ attribute ] = '';
                                            } else {
                                                newAttributes[ attribute ] = field.data[ newValue ][ attribute ];
                                            }
                                        }

                                        // Apply format.
                                        onChange( applyFormat(
                                            value,
                                            {
                                                type: 'convertkit/' + block.name,
                                                attributes: newAttributes
                                            }
                                        ) );
                                    } else {
                                        // Remove format.
                                        onChange( toggleFormat(
                                            value,
                                            {
                                                type: 'convertkit/' + block.name
                                            }
                                        ) );
                                    }

                                }
                            }
                        ) );
                    }

                    return (
                        createElement(
                            Fragment,
                            {
                                key:  'convertkit_' + block.name + '_rich_text_toolbar_fragment'
                            },

                            // Register the button in the rich text toolbar.
                            createElement(
                                RichTextToolbarButton,
                                {
                                    key:  'convertkit_' + block.name + '_rich_text_toolbar_button',
                                    icon: icon,
                                    title: block.title,
                                    isActive: isActive,
                                    onClick: function() {
                                        setShowPopover( true );
                                    }
                                },
                            ),

                            // Popover which displays fields when the button is active.
                            showPopover && ( createElement(
                                Popover,
                                {
                                    key:  'convertkit_' + block.name + '_popover',
                                    className: 'convertkit-popover',
                                    anchor: anchorRef,
                                    onClose: function() {
                                        setShowPopover( false );
                                    }
                                },
                                elements
                            ) )
                        )
                    );
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
