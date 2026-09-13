import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import { heroProps } from './props';

export default function save( { attributes } ) {
	const blockProps = useBlockProps.save( heroProps( attributes ) );
	const innerBlocksProps = useInnerBlocksProps.save( { className: 'bwfd-hero__inner' } );
	return (
		<section { ...blockProps }>
			<div className="bwfd-hero__overlay" aria-hidden="true" />
			<div { ...innerBlocksProps } />
		</section>
	);
}
