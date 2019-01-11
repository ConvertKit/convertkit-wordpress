const { Component } = wp.element;
const { PanelBody, Toolbar, SelectControl } = wp.components;
const { BlockControls, InnerBlocks, InspectorControls } = wp.editor;
const { __ } = wp.i18n;

/**
 * Internal dependencies
 */
import './content.scss';

class CKContent extends Component {
    constructor( props ) {
        super( props );

        this.state = {
            tags: [],
        };
    }
    componentDidMount() {
        const self = this;
        $.ajax({
            url: ajaxurl,
            data: { action: 'convertkit_get_block_tags' },
            success: function( resp ) {
                if ( resp.success ) {
                    self.setState( { tags: resp.data.tags } );
                }
            }
        });
    }
    render() {
        const { attributes, className, setAttributes } = this.props;
        const { has_tag, tag } = attributes;
        let defaultOption = [{ value:0, label: __( 'Choose a tag') }];
        const options = defaultOption.concat(Object.entries( this.state.tags ).map( ( item ) => ({
            value: item[1].id,
            label: item[1].name
        })));

        let needsTag = '';
        let _has_tag = has_tag || 'true';
        let _tag = parseInt( tag ) || 0;
        let _className = className;
        if ( 'false' === _has_tag ) {
            _className += ' without-tag';
        }

        if ( ! _tag ) {
            needsTag = ' | ' + __( 'Please, select a tag.' );
        }

        function visibleControl() {
            return {
                icon: 'visibility',
                // translators: %s: heading level e.g: "1", "2", "3"
                title: __( 'Has Tag'),
                isActive: 'true' === _has_tag,
                onClick: () => setAttributes( { has_tag: 'true' } ),
            };
        }

        function hideControl() {
            return {
                icon: 'hidden',
                // translators: %s: heading level e.g: "1", "2", "3"
                title: __( 'Without Tag'),
                isActive: 'false' === _has_tag,
                onClick: () => setAttributes( { has_tag: 'false' } ),
            };
        }

        return [
            <BlockControls key="controls">
                <Toolbar controls={[ visibleControl(), hideControl() ]} />
            </BlockControls>,
            <InspectorControls>
                <PanelBody title={ __( 'Tag Settings' ) }>
                    <Toolbar controls={ [ visibleControl(), hideControl() ] } />
                    <SelectControl
                        label={ __('Tag') }
                        value={ attributes.tag }
                        options={ options }
                        onChange={ ( tag ) => { setAttributes( { tag } ); } }
                    />
                </PanelBody>
            </InspectorControls>,
            <div className={ _className }>
                <p className='ck-custom-content-desc'>{ __( 'ConvertKit Custom Content' ) + needsTag }</p>
                <InnerBlocks />
            </div>];

    }
}

export default CKContent;