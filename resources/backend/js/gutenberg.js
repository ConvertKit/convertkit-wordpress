/**
 * Registers blocks in the Gutenberg editor.
 *
 * @since   1.9.6.5
 *
 * @package ConvertKit
 * @author ConvertKit
 */

// Register Gutenberg Blocks if the Gutenberg Editor is loaded on screen.
// This prevents JS errors if this script is accidentally enqueued on a non-
// Gutenberg editor screen, or the Classic Editor Plugin is active.
if ( typeof wp !== 'undefined' &&
	typeof wp.data !== 'undefined' &&
	typeof wp.data.dispatch( 'core/edit-post' ) !== 'undefined' &&
	wp.data.dispatch( 'core/edit-post' ) !== null ) {

	// Register each ConvertKit Block in Gutenberg.
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
				description:block.description,
				category:   block.category,
				icon:       icon,
				keywords: 	block.keywords,
				attributes: block.attributes,
				example: 	{
					attributes: {
						is_gutenberg_example: true,
					}
				},

				// Editor.
				edit: function( props ) {

					// If requesting an example of how this block looks (which is requested
					// when the user adds a new block and hovers over this block's icon),
					// show the preview image.
					if ( props.attributes.is_gutenberg_example === true ) {
						return (
							Fragment,
							{},
							el(
								'img',
								{
									src: block.gutenberg_example_image,
								}
							)
						);
					}

					// Build Inspector Control Panels, which will appear in the Sidebar when editing the Block.
					var panels  = [],
					initialOpen = true;
					for ( const panel in block.panels ) {

						// Build Inspector Control Panel Rows, one for each Field.
						var rows = [];
						for ( var i in block.panels[ panel ].fields ) {
							const attribute = block.panels[ panel ].fields[ i ], // e.g. 'term'.
									field   = block.fields[ attribute ]; // field array.

							var fieldElement; // Holds the field element (select, textarea, text etc).

							// Define Field's Properties.
							var fieldProperties = {
								id:  		'convertkit_' + block.name + '_' + attribute,
								label: 		field.label,
								help: 		field.description,
								value: 		props.attributes[ attribute ],
								onChange: 	function( value ) {
									if ( field.type == 'number' ) {
										// Cast value to integer if a value exists.
										if ( value.length > 0 ) {
											value = Number( value );
										}
									}

									var newValue          = {};
									newValue[ attribute ] = value;
									props.setAttributes( newValue );
								}
							};

							// Define additional Field Properties and the Field Element,
							// depending on the Field Type (select, textarea, text etc).
							switch ( field.type ) {

								case 'select':
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

									// Assign options to field.
									fieldProperties.options = fieldOptions;

									// Define field element.
									fieldElement = el(
										SelectControl,
										fieldProperties
									);
									break;

								case 'toggle':
									// Define field properties.
									fieldProperties.checked = props.attributes[ attribute ];

									// Define field element.
									fieldElement = el(
										ToggleControl,
										fieldProperties
									);
									break;

								case 'number':
									// Define field properties.
									fieldProperties.type = field.type;
									fieldProperties.min  = field.min;
									fieldProperties.max  = field.max;
									fieldProperties.step = field.step;

									// Define field element.
									fieldElement = el(
										TextControl,
										fieldProperties
									);
									break;

								default:
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
					if ( typeof block.gutenberg_preview_render_callback !== 'undefined' ) {
						// Use a custom callback function to render this block's preview in the Gutenberg Editor.
						// This doesn't affect the output for this block on the frontend site, which will always
						// use the block's PHP's render() function.
						preview = window[ block.gutenberg_preview_render_callback ]( block, props );
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

					// Deliberate; preview in the editor is determined by the return statement in `edit` above.
					// On the frontend site, the block's render() PHP class is always called, so we dynamically
					// fetch the content.
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
