const { Component } = wp.element;
const { SelectControl, PanelBody } = wp.components;
const { InspectorControls } = wp.editor;
const { __ } = wp.i18n;

class CKForm extends Component {
    constructor( props ) {
        super( props );

        this.state = {
            form: false,
            forms: []
        };

        this.getForm = this.getForm.bind(this);
    }
    componentDidMount() {
        const self = this;
        $.ajax({
            url: ajaxurl,
            data: { action: 'convertkit_get_forms' },
            success: function( resp ) {
                if ( resp.success ) {
                    self.setState( { forms: resp.data.forms } );
                }
            }
        });

        if ( this.props.attributes.id ) {
            this.getForm( this.props.attributes.id );
        }
    }
    getForm( id ) {
        const self = this;

        $.ajax({
            url: ajaxurl,
            data: { action: 'convertkit_get_form', id: id },
            success: function( resp ) {
                if ( resp.success ) {
                    self.setState( { form: resp.data.form } );
                }
            }
        });
    }
    render() {
        const { attributes, className, setAttributes } = this.props;
        const self = this;
        let defaultOption = [{ value:0, label: __( 'Choose a form') }];
        const options = defaultOption.concat(Object.entries( this.state.forms ).map( ( item ) => ({
            value: item[1].id,
            label: item[1].name
        })));

        let output = <p className={ className }>{ __( 'Please create a form in ConvertKit' ) }</p>;

        if ( this.state.forms ) {

            let html = __( 'Select a form' );

            if ( attributes.id ) {
                html = __( 'Loading the form...' );
            }

            if ( false !== this.state.form ) {
                html = this.state.form;

                if ( ! html ) {
                    html = __( 'Looks like there is no form markup for the selected. Choose another.' );
                }
            }
            output = [
                <InspectorControls key="controls">
                    <PanelBody>
                        <SelectControl
                            label={ __('Form') }
                            value={ attributes.id }
                            options={ options }
                            onChange={ ( id ) => { setAttributes( { id } ); this.getForm( id ); } }
                        />
                    </PanelBody>
                </InspectorControls>,
                <div className={ className } dangerouslySetInnerHTML={{ __html: html }}></div>];
        }

        return output;
    }
}

export default CKForm;