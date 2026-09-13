import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import { cardClassName, cardStyle } from './classes';

export default function save( { attributes } ) {
	const blockProps = useBlockProps.save( {
		className: cardClassName( attributes ),
		style: cardStyle( attributes ),
	} );
	const innerBlocksProps = useInnerBlocksProps.save( blockProps );
	return <div { ...innerBlocksProps } />;
}
