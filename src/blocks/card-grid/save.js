import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import { gridProps } from './props';

export default function save( { attributes } ) {
	const blockProps = useBlockProps.save( gridProps( attributes ) );
	const innerBlocksProps = useInnerBlocksProps.save( blockProps );
	return <div { ...innerBlocksProps } />;
}
