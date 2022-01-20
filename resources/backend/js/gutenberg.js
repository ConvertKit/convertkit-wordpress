/**
 * Registers blocks in the Gutenberg editor.
 *
 * @since   1.9.6.5
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
 * @since 	1.9.6.5
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
		const { Fragment } 		  			  = element;
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
					for ( const panel in block.panels ) {

						// Build Inspector Control Panel Rows, one for each Field.
						var rows = [];
						for ( var i in block.panels[ panel ].fields ) {
							const attribute = block.panels[ panel ].fields[ i ], // e.g. 'term'.
									field   = block.fields[ attribute ]; // field array.

							var fieldElement,
							fieldProperties  = {},
							fieldOptions     = [
								{
									label: '(None)',
									value: '',
								}
							];

							// Define Field Element based on the Field Type.
							switch ( field.type ) {

								case 'select':
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
									title: block.panels[ panel ].label,
									key: panel,
									initialOpen: initialOpen
								},
								rows
							)
						);

						// Don't open any further panels.
						initialOpen = false;
					}

					// Generate Block Preview.
					var preview = '';
					if ( typeof block.render_callback_gutenberg_preview !== 'undefined' ) {
						// Use a custom callback function to render this block's preview in the Gutenberg Editor.
						// This doesn't affect the output for this block on the frontend site, which will always
						// use the block's PHP's render() function.
						preview = window[ block.render_callback_gutenberg_preview ]( block, props );
					} else {
						// Use the block's PHP's render() function by calling the ServerSideRender component.
						preview = el(
							ServerSideRender, 
							{
			                    block: 'convertkit/' + block.name,
			                    attributes: props.attributes,
			                    className: 'convertkit-' + block.name,
			                }
						);
					}

					// Return.
					return (
						el(
							// Sidebar Panel with Fields.
							Fragment,
							{},
							el(
								InspectorControls,
								{},
								panels
							),

							// Block Preview.
							preview
						)
					);
				},

				// Output.
				save: function( props ) {

					// Deliberate; preview in the editor is determined by the preview above.
					// On the frontend site, the block's render() PHP class is always called.
					return null;

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