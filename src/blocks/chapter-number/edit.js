import { __, sprintf } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';
import { PanelBody, SelectControl } from '@wordpress/components';

/**
 * Chapter number: reads the chapter post's Order from the editor's data
 * store (the block is used in templates, so the post comes from context).
 */
export default function Edit( { attributes, setAttributes, context } ) {
	const { format } = attributes;
	const { postId, postType } = context;
	const number = useSelect(
		( select ) => {
			if ( ! postId || postType !== 'bwfd_chapter' ) {
				return 0;
			}
			const record = select( coreStore ).getEditedEntityRecord( 'postType', postType, postId );
			return record ? parseInt( record.menu_order, 10 ) || 0 : 0;
		},
		[ postId, postType ]
	);
	const blockProps = useBlockProps( { className: 'bwfd-chapter-number' } );
	const n = number || 22;
	let text = sprintf( /* translators: %d: chapter number */ __( 'Chapter %d', 'bwfd' ), n );
	if ( format === 'number' ) {
		text = String( n );
	} else if ( format === 'label-of' ) {
		text = sprintf( /* translators: %d: chapter number */ __( 'Chapter %d of 40', 'bwfd' ), n );
	}

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Chapter number', 'bwfd' ) }>
					<SelectControl
						label={ __( 'Format', 'bwfd' ) }
						value={ format }
						options={ [
							{ label: __( 'Chapter 22', 'bwfd' ), value: 'label' },
							{ label: __( '22', 'bwfd' ), value: 'number' },
							{ label: __( 'Chapter 22 of 40', 'bwfd' ), value: 'label-of' },
						] }
						onChange={ ( value ) => setAttributes( { format: value } ) }
						__nextHasNoMarginBottom
						__next40pxDefaultSize
					/>
				</PanelBody>
			</InspectorControls>
			<span { ...blockProps }>{ text }</span>
		</>
	);
}
