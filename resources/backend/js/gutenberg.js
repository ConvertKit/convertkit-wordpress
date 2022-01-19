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

		convertKitGutenbergRegisterBlock( convertkit_blocks[ block ] );

	}

}

/**
 * Registers the given block in Gutenberg.
 *
 * @since 	1.9.6
 *
 * @param 	object 	block 	Block
 */
function convertKitGutenbergRegisterBlock( block ) {

	// Register Block.
	( function( blocks, editor, element, components, block ) {

		// Define some constants for the various items we'll use.
		const el                              = element.createElement;
		const { registerBlockType }           = blocks;
		const { RichText, InspectorControls } = editor;
		const { Fragment } 	  				  = element;
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
			PanelRow,
			SandBox,
			ServerSideRender
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
				attributes: block.attributes,

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
							fieldProperties  = {},
							fieldOptions     = [];

							// Build values for <select> inputs.
							if ( typeof field.values !== 'undefined' ) {
								for ( var value in field.values ) {
									fieldOptions.push(
										{
											label: field.values[ value ],
											value: value
										}
									);
								}
							}

							// Define Field Element based on the Field Type.
							switch ( field.type ) {

								case 'select':
									// Define field properties.
									fieldProperties = {
										label: 		field.label,
										help: 		field.description,
										options: 	fieldOptions,
										value: 		props.attributes[ attribute ],
										onChange: function( value ) {
											var newValue          = {};
											newValue[ attribute ] = value;
											props.setAttributes( newValue );
										}
									};

									// Define field element.
									fieldElement = el(
										SelectControl,
										fieldProperties
									);
									break;

								case 'toggle':
									// Define field properties.
									fieldProperties = {
										label: 		field.label,
										help: 		field.description,
										checked: 	props.attributes[ attribute ],
										onChange: function( value ) {
											var newValue          = {};
											newValue[ attribute ] = value;
											props.setAttributes( newValue );
										},
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

									// Define field element.
									fieldElement = el(
										TextControl,
										fieldProperties
									);
									break;

								default:
									// Define field properties.
									fieldProperties = {
										type: 		field.type,
										label: 		field.label,
										help: 		field.description,
										value: 		props.attributes[ attribute ],
										onChange: function( value ) {
											var newValue          = {};
											newValue[ attribute ] = value;
											props.setAttributes( newValue );
										},
									};

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
									{
										// Required to avoid "Each child in a list should have a unique "key" prop." error.
										key: attribute
									},
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
									key: panel,
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

							// Block Output/Preview.
							/*
							el(
								ServerSideRender, 
								{
				                    block: 'convertkit/' + block.name,
				                    attributes: props.attributes,
				                    className: 'convertkit-' + block.name,
				                }
							)
							*/

							el(
								'div',
								{
									className: 'convertkit-' + block.name
								},
								SandBox({
									html: '<script async data-uid="85629c512d" src="https://cheerful-architect-3237.ck.page/85629c512d/index.js"></script>',
									title: 'Test',
									type: 'embed',
									styles: [],
									scripts: []
								})
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
