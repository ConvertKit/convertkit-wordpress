import ckIcon from '../icon';
import editForm from './form';

const { registerBlockType } = wp.blocks;

const { __ } = wp.i18n;


registerBlockType( 'convertkit/form', {
    title: __( 'ConvertKit Form' ),
    icon: ckIcon,
    category: 'widgets',
    keywords: [
        __( 'convertkit' ),
        __( 'form' ),
        __( 'email' )
    ],

    attributes: {
        id: {
            type: 'string'
        },
        blocks: {
            type: 'string'
        }
    },

    edit: editForm,

    save() {
        // Rendering in PHP
        return null;
    }
} );