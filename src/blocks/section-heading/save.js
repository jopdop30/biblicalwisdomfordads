import { useBlockProps, RichText } from '@wordpress/block-editor';

export default function save( { attributes } ) {
	const { content, level, size, showRule, textAlign } = attributes;
	const blockProps = useBlockProps.save( {
		className: `bwfd-section-heading has-text-align-${ textAlign }`,
	} );

	return (
		<div { ...blockProps }>
			<RichText.Content
				tagName={ `h${ level }` }
				className={ `bwfd-section-heading__title has-${ size }-font-size` }
				value={ content }
			/>
			{ showRule && <span className="bwfd-section-heading__rule" aria-hidden="true" /> }
		</div>
	);
}
