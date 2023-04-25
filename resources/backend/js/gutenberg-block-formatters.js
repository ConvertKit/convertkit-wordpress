/**
 * Registers formatters in the Block Toolbar in the Gutenberg editor.
 *
 * @since   2.2.0
 *
 * @package ConvertKit
 * @author ConvertKit
 */

// Register Gutenberg Block Toolbar formatters if the Gutenberg Editor is loaded on screen.
// This prevents JS errors if this script is accidentally enqueued on a non-
// Gutenberg editor screen, or the Classic Editor Plugin is active.
if ( typeof wp !== 'undefined' &&
	typeof wp.blocks !== 'undefined' ) {

	// Register each ConvertKit formatter in Gutenberg.
	for ( const formatter in convertkit_block_formatters ) {
		convertKitGutenbergRegisterBlockFormatter( convertkit_block_formatters[ formatter ] );
	}

}

/**
 * Registers the given formatter in Gutenberg.
 *
 * @since   2.2.0
 *
 * @param   object  formatter   Block formatter.
 */
function convertKitGutenbergRegisterBlockFormatter( formatter ) {

	( function( editor, richText, element, components, block ) {

		const {
			Fragment,
			useState,
			createElement
		} = element;
		const {
			registerFormatType,
			toggleFormat,
			applyFormat,
			useAnchorRef
		} = richText;
		const {
			BlockControls,
			RichTextToolbarButton,
		} = editor;
		const {
			Button,
			Popover,
			SelectControl
		} = components;

		// Build Icon, if it's an object.
		var icon = 'dashicons-tablet';
		if ( typeof formatter.gutenberg_icon !== 'undefined' ) {
			if ( formatter.gutenberg_icon.search( 'svg' ) >= 0 ) {
				// SVG.
				icon = element.RawHTML(
					{
						children: formatter.gutenberg_icon
					}
				);
			} else {
				// Dashicon.
				icon = formatter.gutenberg_icon;
			}
		}

		// Register Format Type.
		registerFormatType(
			'convertkit/' + formatter.name,
			{
				title:      formatter.title,

				// The tagName and className combination allow Gutenberg to uniquely identify
				// whether this formatter has been used on the selected text.
				tagName:    formatter.tag,
				className:  'convertkit-' + formatter.name,

				attributes: formatter.attributes,

				// Editor.
				edit: function( props ) {

					// Get props and anchor reference to the text.
					const { contentRef, isActive, onChange, value } = props;
					const { activeFormats }                         = value;
					const anchorRef                                 = useAnchorRef( { ref: contentRef, value } );

					// State to show popover.
					const [ showPopover, setShowPopover ] = useState( false );

					// Define array of elements to display when the button is clicked.
					var elements = [];

					// Define object comprising of attributes.
					var attributes = {};
					for ( var attribute in formatter.attributes ) {
						attributes[ attribute ] = '';
					}

					// If this formatter has been applied to the selected text,
					// the selected text may have existing attributes.
					// Fetch those attribute values.
					if ( typeof activeFormats !== 'undefined' ) {
						const formats = activeFormats.filter( format => 'convertkit/' + formatter.name === format['type'] );
						if ( formats.length > 0 ) {
							for ( var attribute in formatter.attributes ) {
								if ( typeof formats[0].unregisteredAttributes !== 'undefined' ) {
									attributes[ attribute ] = formats[0].unregisteredAttributes[ attribute ];
								} else if ( typeof formats[0].attributes !== 'undefined' ) {
									attributes[ attribute ] = formats[0].attributes[ attribute ];
								}
							}
						}
					}

					// Define fields.
					for ( var fieldName in formatter.fields ) {
						const field = formatter.fields[ fieldName ];

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
						elements.push(
							createElement(
								SelectControl,
								{
									key:        'convertkit_' + formatter.name + '_' + fieldName,
									id:         'convertkit-' + formatter.name + '-' + fieldName,
									label:      field.label,
									value:      attributes[ fieldName ],
									help:       field.description,
									options:    fieldOptions,
									onChange:   function( newValue ) {

										setShowPopover( false );

										if ( newValue ) {
											// Build object of new attributes.
											var newAttributes = {};
											for ( var attribute in formatter.attributes ) {
												// If 'None' selected, blank the attribute's value.
												if ( newValue === '' ) {
													newAttributes[ attribute ] = '';
												} else {
													newAttributes[ attribute ] = field.data[ newValue ][ attribute ];
												}
											}

											// Apply format.
											onChange(
												applyFormat(
													value,
													{
														type: 'convertkit/' + formatter.name,
														attributes: newAttributes
													}
												)
											);
										} else {
											// Remove format.
											onChange(
												toggleFormat(
													value,
													{
														type: 'convertkit/' + formatter.name
													}
												)
											);
										}

									}
								}
							)
						);
					}

					return (
						createElement(
							Fragment,
							{
								key:  'convertkit_' + formatter.name + '_rich_text_toolbar_fragment'
							},
							// Register the button in the rich text toolbar.
							createElement(
								RichTextToolbarButton,
								{
									key:  'convertkit_' + formatter.name + '_rich_text_toolbar_button',
									icon: icon,
									title: formatter.title,
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
									key:  'convertkit_' + formatter.name + '_popover',
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
		formatter
	) );

}
