import ckIcon from '../icon';
import CKContent from './content';
const { registerBlockType } = wp.blocks;
const { InnerBlocks } = wp.editor;
const { __ } = wp.i18n;


registerBlockType( 'convertkit/custom-content', {
    title: __( 'ConvertKit Custom Content' ),
    icon: ckIcon,
    category: 'widgets',
    keywords: [
        __( 'convertkit' ),
        __( 'form' ),
        __( 'email' )
    ],

    attributes: {
        tag: {
            type: 'string'
        },
        has_tag: {
            type: 'string'
        }
    },

    edit: CKContent,

    save() {
        return ( <InnerBlocks.Content />);
    }
} );