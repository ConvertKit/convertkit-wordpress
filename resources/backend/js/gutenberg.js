/**
 * Registers blocks in the Gutenberg editor.
 *
 * @since   1.9.6
 *
 * @package ConvertKit
 * @author ConvertKit
 */

// Determine if Gutenberg is active.
convertkit_is_gutenberg_active = ( ( typeof wp !== 'undefined' && typeof wp.data !== 'undefined' && typeof wp.data.dispatch( 'core/edit-post' ) !== 'undefined' ) ? true : false );

if ( convertkit_is_gutenberg_active && wp.data.dispatch( 'core/edit-post' ) !== null ) {

	for ( const block in convertkit_blocks ) {

		// convertKitGutenbergRegisterBlock( convertkit_blocks[ block ] );.

	}

}

/**
 * Registers the given block as a TinyMCE Plugin, with a button in
 * the Visual Editor toolbar.
 *
 * @since 	1.9.6
 *
 * @param 	object 	block 	Block
 */
function convertKitGutenbergRegisterBlock( block ) {

	// Build Gutenberg compliant Attributes object.
	var blockAttributes = {};
	for ( const field in block.fields ) {
		// Assume the attribute's type is a string.
		var type = 'string';

		// Depending on the field's type, change the attribute type.
		switch ( block.fields[ field ].type ) {
			case 'number':
				type = 'number';
				break;

			case 'text_multiple':
				type = 'array';
				break;

			case 'select_multiple':
				type = 'array';
				break;

			case 'toggle':
				type = 'boolean';
				break;
		}

		// Define the attribute's type.
		blockAttributes[ field ] = {
			type: type,
		}
	}

	// Register Block.
	( function( blocks, editor, element, components, block ) {

		// Define some constants for the various items we'll use.
		const el                              = element.createElement;
		const { registerBlockType }           = blocks;
		const { RichText, InspectorControls } = editor;
		const { Fragment }                    = element;
		const {
			TextControl,
			CheckboxControl,
			RadioControl,
			SelectControl,
			TextareaControl,
			ToggleControl,
			RangeControl,
			FormTokenField,
			Panel,
			PanelBody,
			PanelRow
		}                                     = components;

		// Build Icon, if it's an object.
		var icon = 'dashicons-tablet';
		if ( typeof block.icon !== 'undefined' ) {
			if ( block.icon.search( 'svg' ) >= 0 ) {
				// SVG.
				icon = element.RawHTML(
					{
						children: block.icon
					}
				);
			} else {
				// Dashicon.
				icon = block.icon;
			}
		}

		// Register Block.
		registerBlockType(
			'convertkit/' + block.name,
			{
				title:      block.title,
				description:block.description,
				category:   block.category,
				icon:       icon,
				keywords: 	block.keywords,

				// Define the block attributes.
				attributes: blockAttributes,

				// Editor.
				edit: function( props ) {

					// Build Inspector Control Panels, which will appear in the Sidebar when editing the Block.
					var panels  = [],
					initialOpen = true;
					for ( const panel in block.tabs ) {

						// Build Inspector Control Panel Rows, one for each Field.
						var rows = [];
						for ( var i in block.tabs[ panel ].fields ) {
							const attribute = block.tabs[ panel ].fields[ i ], // e.g. 'term'.
									field   = block.fields[ attribute ]; // field array.

							var fieldElement,
							fieldClassNames  = [],
							fieldProperties  = {},
							fieldOptions     = [],
							fieldSuggestions = [],
							fieldData        = {};

							// Build values for <select> inputs.
							if ( typeof field.values !== 'undefined' ) {
								for ( var value in field.values ) {
									fieldOptions.push(
										{
											label: field.values[ value ],
											value: value
										}
									);
									fieldSuggestions.push( '[' + value + '] ' + field.values[ value ] ); // NEVER CHANGE THIS EVER.
								}
							}

							// Build data- attributes.
							if ( typeof field.data !== 'undefined' ) {
								for ( var key in field.data ) {
									fieldData[ 'data-' + key ] = field.data[ key ];
								}
							}

							// Build CSS class name(s).
							if ( typeof field.class !== 'undefined' ) {
								fieldClassNames.push( field.class );
							}
							if ( typeof field.condition !== 'undefined' ) {
								fieldClassNames.push( field.condition.value );
							}

							// Define Field Element based on the Field Type.
							switch ( field.type ) {

								case 'select':
									// Define field properties.
									fieldProperties = {
										label: 		field.label,
										help: 		field.description,
										className: 	fieldClassNames.join( ' ' ),
										options: 	fieldOptions,
										value: 		props.attributes[ attribute ],
										onChange: function( value ) {
											var newValue          = {};
											newValue[ attribute ] = value;
											props.setAttributes( newValue );
										}
									};

									// Add data- attributes.
									for ( var key in fieldData ) {
										fieldProperties[ key ] = fieldData[ key ];
									}

									// Define field element.
									fieldElement = el(
										SelectControl,
										fieldProperties
									);
									break;

								case 'select_multiple':
									// Convert values to labels.
									var values = [];
									for ( var index in props.attributes[ attribute ] ) {
										values.push( '[' + props.attributes[ attribute ][ index ] + '] ' + field.values[ props.attributes[ attribute ][ index ] ] );
									}

									// Define field properties.
									fieldProperties = {
										label: 			field.label,
										help: 			field.description,
										className: 		fieldClassNames.join( ' ' ),
										suggestions: 	fieldSuggestions,
										maxSuggestions: 5,
										value: 			values,
										onChange: function( values ) {
											// Extract values between square brackets, and remove the rest.
											var newValues    = [],
												valuesLength = values.length;
											for ( index = 0; index < valuesLength; index++ ) {
												var matches = values[ index ].match( /\[(.*?)\]/ );
												if ( matches ) {
													newValues.push( matches[1] );
												}
											}

											var newValue          = {};
											newValue[ attribute ] = newValues;
											props.setAttributes( newValue );
										},
									};

									// Add data- attributes.
									for ( var key in fieldData ) {
										fieldProperties[ key ] = fieldData[ key ];
									}

									// Define field element.
									fieldElement = el(
										FormTokenField,
										fieldProperties
									);
									break;

								case 'text_multiple':
									// Define field properties.
									fieldProperties = {
										label: 			field.label,
										help: 			field.description,
										className: 		fieldClassNames.join( ' ' ),
										value: 			props.attributes[ attribute ],
										onChange: function( values ) {
											var newValue          = {};
											newValue[ attribute ] = values;
											props.setAttributes( newValue );
										}
									};

									// Add data- attributes.
									for ( var key in fieldData ) {
										fieldProperties[ key ] = fieldData[ key ];
									}

									// Define field element.
									fieldElement = el(
										FormTokenField,
										fieldProperties
									);
									break;

								case 'toggle':
									// Define field properties.
									fieldProperties = {
										label: 		field.label,
										help: 		field.description,
										className: 	fieldClassNames.join( ' ' ),
										checked: 	props.attributes[ attribute ],
										onChange: function( value ) {
											var newValue          = {};
											newValue[ attribute ] = value;
											props.setAttributes( newValue );
										},
									}

									// Add data- attributes.
									for ( var key in fieldData ) {
										fieldProperties[ key ] = fieldData[ key ];
									}

									// Define field element.
									fieldElement = el(
										ToggleControl,
										fieldProperties
									);
									break;

								case 'number':
									// Define field properties.
									fieldProperties = {
										type: 		field.type,
										label: 		field.label,
										help: 		field.description,
										min: 		field.min,
										max: 		field.max,
										step: 		field.step,
										className: 	fieldClassNames.join( ' ' ),
										value: 		props.attributes[ attribute ],
										onChange: function( value ) {
											// Cast value to integer if a value exists.
											if ( value.length > 0 ) {
												value = Number( value );
											}

											var newValue          = {};
											newValue[ attribute ] = value;
											props.setAttributes( newValue );
										},
									};

									// Add data- attributes.
									for ( var key in fieldData ) {
										fieldProperties[ key ] = fieldData[ key ];
									}

									// Define field element.
									fieldElement = el(
										TextControl,
										fieldProperties
									);
									break;

								case 'autocomplete':
									// Define field properties.
									fieldProperties = {
										list: 		'autocomplete-' + i,
										type: 		'text',
										label: 		field.label,
										help: 		field.description,
										className: 	fieldClassNames.join( ' ' ),
										options: 	field.values,
										value: 		props.attributes[ attribute ],
										onChange: function( value ) {
											var newValue          = {};
											newValue[ attribute ] = value;
											props.setAttributes( newValue );
										},
									};

									// Add data- attributes.
									for ( var key in fieldData ) {
										fieldProperties[ key ] = fieldData[ key ];
									}

									// Define field element.
									fieldElement = el(
										WPZincAutocompleterControl,
										fieldProperties
									);
									break;

								default:
									// Define field properties.
									fieldProperties = {
										type: 		field.type,
										label: 		field.label,
										help: 		field.description,
										className: 	fieldClassNames.join( ' ' ),
										value: 		props.attributes[ attribute ],
										onChange: function( value ) {
											var newValue          = {};
											newValue[ attribute ] = value;
											props.setAttributes( newValue );
										},
									};

									// Add data- attributes.
									for ( var key in fieldData ) {
										fieldProperties[ key ] = fieldData[ key ];
									}

									// Define field element.
									fieldElement = el(
										TextControl,
										fieldProperties
									);
									break;
							}

							// Add Field as a Row.
							rows.push(
								el(
									PanelRow,
									{},
									fieldElement
								)
							);
						}

						// Add the Panel Rows to a new Panel.
						panels.push(
							el(
								PanelBody,
								{
									title: block.tabs[ panel ].label,
									initialOpen: initialOpen
								},
								rows
							)
						);

						// Don't open any further panels.
						initialOpen = false;
					}

					// Return.
					return (
					el(
						Fragment,
						{},
						el(
							InspectorControls,
							{},
							panels
						),
						// Block Markup.
						el(
							'div',
							{},
							'[convertkit-' + block.name + ']'
						)
					)
					);
				},

				// Output.
				save: function( props ) {

					return null;

				}
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
